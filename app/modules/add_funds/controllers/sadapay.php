<?php
defined('BASEPATH') or exit('No direct script access allowed');

class sadapay extends MX_Controller
{
    public $tb_users;
    public $tb_transaction_logs;
    public $tb_payments;
    public $tb_payments_bonuses;
    public $paypal;
    public $payment_type;
    public $payment_id;
    public $currency_code;
    public $payment_lib;
    public $mode;

    public $sadapay_mid;
    public $merchant_key;
    public $currency_rate_to_usd;
    public $payment_fee;

    public function __construct($payment = "")
    {
        parent::__construct();
        $this->load->model('add_funds_model', 'model');

        $this->tb_users            = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->tb_payments         = PAYMENTS_METHOD;
        $this->tb_payments_bonuses = PAYMENTS_BONUSES;
        $this->payment_type        = get_class($this);
        $this->currency_code       = get_option("currency_code", "USD");
        if ($this->currency_code == "") {
            $this->currency_code = 'USD';
        }
        if (!$payment) {
            $payment = $this->model->get('id, type, name, params', $this->tb_payments, ['type' => $this->payment_type]);
        }
        $this->payment_id              = $payment->id;
        $params                        = $payment->params;
        $option                        = get_value($params, 'option');
        $this->mode                    = get_value($option, 'environment');
        $this->sadapay_mid             = get_value($option, 'sadapay_mid');
        $this->currency_rate_to_usd    = get_value($option, 'rate_to_usd');
        $this->payment_fee             = get_value($option, 'tnx_fee');
        $this->load->helper("paytm");
        // $this->payment_lib = new paytmapi($this->merchant_key, $this->paytm_mid, $this->mode, get_option('website_name'));
    }

    public function index()
    {
        redirect(cn('add_funds'));
    }

    /**
     * Create payment: logs pending transaction and notifies admin via WhatsApp.
     */
    public function create_payment($data_payment = "")
    {
        _is_ajax($data_payment['module']);
        $amount = $data_payment['amount'];
        if (!$amount) {
            _validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
        }

        $ORDER_ID   = session('qrtransaction_id');
        $TXN_AMOUNT = $amount;

        $data = array(
            "uid" => session('uid'),
        );

        $check_transactionsqr = get_field(TRANSACTION_LOGS, ["transaction_id" => $ORDER_ID, 'type' => $this->payment_type], 'id');

        if (empty($check_transactionsqr)) {
            $converted_amount = $amount / $this->currency_rate_to_usd;
            $data_tnx_log = array(
                "ids"              => ids(),
                "uid"              => session("uid"),
                "type"             => $this->payment_type,
                "transaction_id"   => $ORDER_ID,
                "amount"           => round($converted_amount, 4),
                'txn_fee'          => round($converted_amount * ($this->payment_fee / 100), 4),
                "note"             => $TXN_AMOUNT, // store PKR/Local amount as note for audit
                "status"           => 0,           // pending
                "created"          => NOW,
            );
            $this->db->insert($this->tb_transaction_logs, $data_tnx_log);

            // WhatsApp notify: New SadaPay payment submission
            $user_info  = session('user_current_info');
            $user_email = is_array($user_info) ? ($user_info['email'] ?? 'N/A') : 'N/A';
            $this->sendWhatsAppNotification($TXN_AMOUNT, $ORDER_ID, $user_email, 'new');

            $this->load->view("sadapay/redirect", $data);
        } else {
            ms(array(
                "status"  => "error",
                "message" => lang("transaction_id_already_used"),
            ));
        }
    }

    /**
     * Completes the payment after verifying response from gateway.
     * Sends WhatsApp notification for success or failure.
     */
    public function complete()
    {
        $requestParamList = array("MID" => $this->sadapay_mid, "ORDERID" => session('qrtransaction_id'));

        $responseParamList = array();
        $responseParamList = getTxnStatusNew($requestParamList);

        // If needed, you can enforce MID matching:
        // if ($this->sadapay_mid != ($responseParamList["MID"] ?? null)) {
        //     redirect(cn("add_funds/unsuccess"));
        // }

        $tnx_id = $responseParamList["ORDERID"] ?? null;

        $transaction = $this->model->get('*', $this->tb_transaction_logs, [
            'transaction_id' => $tnx_id,
            'status'         => 0,
            'type'           => $this->payment_type
        ]);

        if (empty($transaction)) {
            echo "wrong txn id";
            return;
        }

        set_session("uid", $transaction->uid);

        $statusOk   = isset($responseParamList["STATUS"]) && $responseParamList["STATUS"] == "TXN_SUCCESS";
        $amountOk   = isset($responseParamList["TXNAMOUNT"]) && $responseParamList["TXNAMOUNT"] == $transaction->note;

        if ($statusOk && $amountOk) {
            // Mark as completed
            $this->db->update($this->tb_transaction_logs, ['status' => 1, 'transaction_id' => $responseParamList["ORDERID"]],  ['id' => $transaction->id]);

            // Update Balance / bonus + email
            require_once 'add_funds.php';
            $add_funds = new add_funds();
            $add_funds->add_funds_bonus_email($transaction, $this->payment_id);

            set_session("transaction_id", $transaction->id);

            // WhatsApp notify: SadaPay payment completed
            $user_info  = session('user_current_info');
            $user_email = is_array($user_info) ? ($user_info['email'] ?? 'N/A') : 'N/A';
            $this->sendWhatsAppNotification($transaction->note, $tnx_id, $user_email, 'completed');

            redirect(cn("add_funds/success"));
        } else {
            // Mark as failed
            $this->db->update($this->tb_transaction_logs, ['status' => -1, 'transaction_id' => $responseParamList["ORDERID"] ?? $tnx_id],  ['id' => $transaction->id]);

            // WhatsApp notify: SadaPay payment failed
            $user_info  = session('user_current_info');
            $user_email = is_array($user_info) ? ($user_info['email'] ?? 'N/A') : 'N/A';
            $this->sendWhatsAppNotification($transaction->note, $tnx_id, $user_email, 'failed');

            redirect(cn("add_funds/unsuccess"));
        }
    }

    /**
     * Send WhatsApp notification to admin.
     * type: 'new' | 'completed' | 'failed'
     */
    private function sendWhatsAppNotification($amount, $transaction_id, $user_email, $type = 'new')
    {
        try {
            $config = $this->getWhatsAppConfig();
            if (!$config) return false;

            $api_url = $config->url;
            $admin_whatsapp_number = $config->admin_phone;
            $api_key = $config->api_key;

            if (empty($api_url) || empty($admin_whatsapp_number) || empty($api_key)) return false;

            switch ($type) {
                case 'completed':
                    $title = "*✅ SadaPay Payment Completed!*";
                    $tail  = "✨ Transaction completed successfully.";
                    break;
                case 'failed':
                    $title = "*❌ SadaPay Payment Failed!*";
                    $tail  = "⚠️ Transaction verification failed.";
                    break;
                case 'new':
                default:
                    $title = "*🆕 New SadaPay Payment Submission!*";
                    $tail  = "🔍 Awaiting manual verification.";
                    break;
            }

            $message =
                "{$title}\n\n" .
                "💰 *Amount*: PKR {$amount}\n" .
                "🔢 *Transaction ID*: {$transaction_id}\n" .
                "📧 *User Email*: {$user_email}\n\n" .
                "{$tail}";

            $data = [
                "apiKey"      => $api_key,
                "phoneNumber" => $admin_whatsapp_number,
                "message"     => $message
            ];

            $ch = curl_init($api_url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_TIMEOUT        => 30
            ]);

            $response = curl_exec($ch);
            $curlErr  = curl_error($ch);
            $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false) {
                log_message('error', 'WhatsApp notify failed: ' . $curlErr);
                return false;
            }

            $responseData = json_decode($response, true);
            if (!is_array($responseData)) {
                log_message('error', 'WhatsApp notify invalid JSON. Status ' . $status . ' Body: ' . substr($response, 0, 512));
                return false;
            }

            if (!empty($responseData['success'])) {
                return true;
            }

            log_message('error', 'WhatsApp notify unsuccessful. Status ' . $status . ' Response: ' . json_encode($responseData));
            return false;
        } catch (Exception $e) {
            log_message('error', 'WhatsApp notify exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch WhatsApp notification configuration.
     * Expects a table 'whatsapp_config' with columns: url, admin_phone, api_key
     */
    private function getWhatsAppConfig()
    {
        try {
            $query = $this->db->select('url, admin_phone, api_key')
                              ->from('whatsapp_config')
                              ->limit(1)
                              ->get();
            if ($query->num_rows() > 0) return $query->row();
            return false;
        } catch (Exception $e) {
            log_message('error', 'WhatsApp config fetch exception: ' . $e->getMessage());
            return false;
        }
    }
}