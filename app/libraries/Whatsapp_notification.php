<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Notification Library
 * 
 * Provides a reusable interface for sending WhatsApp notifications
 * for various events in the SMM panel system.
 * 
 * @package    SMM Panel
 * @subpackage Libraries
 * @category   Notifications
 */
class Whatsapp_notification {

    protected $CI;
    protected $api_url;
    protected $api_key;
    protected $admin_phone;
    protected $is_configured = false;

    /**
     * Constructor - Load configuration
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->_load_config();
    }

    /**
     * Load WhatsApp API configuration from database
     */
    private function _load_config() {
        try {
            $config = $this->CI->db->get('whatsapp_config')->row();
            
            if ($config && !empty($config->url) && !empty($config->api_key)) {
                $this->api_url = $config->url;
                $this->api_key = $config->api_key;
                $this->admin_phone = isset($config->admin_phone) ? $config->admin_phone : '';
                $this->is_configured = true;
            }
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to load config - ' . $e->getMessage());
            $this->is_configured = false;
        }
    }

    /**
     * Check if WhatsApp notification is configured
     * 
     * @return bool
     */
    public function is_configured() {
        return $this->is_configured;
    }

    /**
     * Send WhatsApp notification
     * 
     * @param string $event_type Event type identifier (e.g., 'welcome_message', 'order_cancelled')
     * @param array  $variables  Variables to replace in template
     * @param string $phone      Phone number (optional, defaults to admin phone)
     * @return bool|string       Returns true on success, error message on failure
     */
    public function send($event_type, $variables = array(), $phone = null) {
        // Check if configured
        if (!$this->is_configured) {
            log_message('error', 'WhatsApp Notification: Not configured');
            return 'WhatsApp API not configured';
        }

        // Get notification settings
        $notification = $this->_get_notification_settings($event_type);
        
        if (!$notification) {
            log_message('error', 'WhatsApp Notification: Event type not found - ' . $event_type);
            return 'Notification template not found';
        }

        // Check if notification is enabled
        if ($notification->status != 1) {
            log_message('info', 'WhatsApp Notification: Disabled for event - ' . $event_type);
            return 'Notification disabled';
        }

        // Use admin phone if no phone provided
        if (empty($phone)) {
            $phone = $this->admin_phone;
        }

        // Validate phone number
        if (empty($phone)) {
            log_message('error', 'WhatsApp Notification: No phone number provided');
            return 'No phone number provided';
        }

        // Process template with variables
        $message = $this->_process_template($notification->template, $variables);

        // Send the message
        return $this->_send_message($phone, $message);
    }

    /**
     * Get notification settings from database
     * 
     * @param string $event_type
     * @return object|null
     */
    private function _get_notification_settings($event_type) {
        try {
            $this->CI->db->where('event_type', $event_type);
            return $this->CI->db->get('whatsapp_notifications')->row();
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to get settings - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process template by replacing variables
     * 
     * @param string $template
     * @param array  $variables
     * @return string
     */
    private function _process_template($template, $variables) {
        // Add default variables
        $default_vars = array(
            'website_name' => get_option('website_name', 'SmartPanel'),
            'currency_symbol' => get_option('currency_symbol', '$'),
        );

        $variables = array_merge($default_vars, $variables);

        // Replace variables in template
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    /**
     * Send message via WhatsApp API
     * 
     * @param string $phone
     * @param string $message
     * @return bool|string
     */
    private function _send_message($phone, $message) {
        // Remove + from phone number if present
        $phone = ltrim($phone, '+');

        // Prepare data for the POST request
        $data = array(
            'apiKey' => $this->api_key,
            'phoneNumber' => $phone,
            'message' => $message
        );

        // Initialize cURL
        $ch = curl_init($this->api_url);

        // Set the headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        // Set the POST method and attach the data
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Set options to return the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            log_message('error', 'WhatsApp Notification: cURL error - ' . $error);
            return 'Failed to send: ' . $error;
        }

        curl_close($ch);

        // Log the response
        log_message('info', 'WhatsApp Notification: Sent to ' . $phone . ' - Response: ' . $response);

        return true;
    }

    /**
     * Get all notification settings
     * 
     * @return array
     */
    public function get_all_notifications() {
        try {
            return $this->CI->db->order_by('id', 'ASC')->get('whatsapp_notifications')->result();
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to get all notifications - ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Update notification status
     * 
     * @param string $event_type
     * @param int    $status
     * @return bool
     */
    public function update_status($event_type, $status) {
        try {
            $this->CI->db->where('event_type', $event_type);
            $this->CI->db->update('whatsapp_notifications', array('status' => $status));
            return true;
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to update status - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update notification template
     * 
     * @param string $event_type
     * @param string $template
     * @return bool
     */
    public function update_template($event_type, $template) {
        try {
            $this->CI->db->where('event_type', $event_type);
            $this->CI->db->update('whatsapp_notifications', array('template' => $template));
            return true;
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to update template - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order status change notification
     * 
     * This method sends WhatsApp notifications when an order's status changes.
     * It supports completed, partial, cancelled, and refunded statuses.
     * 
     * @param object $order     Order object with order details
     * @param string $old_status Previous status of the order
     * @param string $new_status New status of the order
     * @param string $phone     User's phone number (optional, will fetch from user if not provided)
     * @return bool|string      Returns true on success, error message on failure
     */
    public function send_order_status_notification($order, $old_status, $new_status, $phone = null) {
        // Only send notification if status actually changed
        if (strtolower($old_status) === strtolower($new_status)) {
            return 'Status unchanged';
        }

        // Determine the event type based on the new status
        $event_type = $this->_get_event_type_from_status($new_status);
        if (!$event_type) {
            log_message('info', 'WhatsApp Notification: No notification configured for status - ' . $new_status);
            return 'No notification for this status';
        }

        // Get user details if phone not provided
        if (empty($phone) && isset($order->uid)) {
            $user = $this->_get_user_details($order->uid);
            if ($user && !empty($user->whatsapp_number)) {
                $phone = $user->whatsapp_number;
            }
        }

        if (empty($phone)) {
            log_message('info', 'WhatsApp Notification: No phone number for order ID ' . $order->id);
            return 'No phone number available';
        }

        // Get service name
        $service_name = '';
        if (isset($order->service_id)) {
            $service = $this->_get_service_details($order->service_id);
            if ($service && isset($service->name)) {
                $service_name = $service->name;
            }
        }

        // Get username
        $username = '';
        if (isset($order->uid)) {
            $user = $this->_get_user_details($order->uid);
            if ($user) {
                if (isset($user->first_name) && !empty($user->first_name)) {
                    $username = $user->first_name;
                } elseif (isset($user->username) && !empty($user->username)) {
                    $username = $user->username;
                }
            }
        }

        // Get decimal places from settings for consistent formatting
        $decimal_places = function_exists('get_option') ? (int)get_option('currency_decimal', 4) : 4;

        // Prepare variables for template
        $variables = array(
            'order_id' => isset($order->id) ? $order->id : '',
            'service_name' => $service_name,
            'quantity' => isset($order->quantity) ? $order->quantity : '',
            'link' => isset($order->link) ? $order->link : '',
            'charge' => isset($order->charge) ? number_format($order->charge, $decimal_places) : number_format(0, $decimal_places),
            'old_status' => ucfirst($old_status),
            'new_status' => ucfirst($new_status),
            'username' => $username,
            'remains' => isset($order->remains) ? $order->remains : '',
            'delivered_quantity' => $this->_calculate_delivered_quantity($order),
            'ordered_quantity' => isset($order->quantity) ? $order->quantity : '',
            'refund_amount' => isset($order->refund_amount) ? number_format($order->refund_amount, $decimal_places) : number_format(0, $decimal_places),
            'new_balance' => isset($order->new_balance) ? number_format($order->new_balance, $decimal_places) : number_format(0, $decimal_places),
        );

        // Send the notification
        $result = $this->send($event_type, $variables, $phone);

        // Log the notification attempt
        $this->_log_notification($order->id, $event_type, $old_status, $new_status, is_bool($result) && $result);

        return $result;
    }

    /**
     * Send notification for order completed
     * 
     * @param object $order     Order object
     * @param string $old_status Previous status
     * @param string $phone     User's phone number (optional)
     * @return bool|string
     */
    public function send_order_completed_notification($order, $old_status, $phone = null) {
        return $this->send_order_status_notification($order, $old_status, 'completed', $phone);
    }

    /**
     * Send notification for order partial
     * 
     * @param object $order     Order object
     * @param string $old_status Previous status
     * @param string $phone     User's phone number (optional)
     * @return bool|string
     */
    public function send_order_partial_notification($order, $old_status, $phone = null) {
        return $this->send_order_status_notification($order, $old_status, 'partial', $phone);
    }

    /**
     * Send notification for order cancelled
     * 
     * @param object $order     Order object
     * @param string $old_status Previous status
     * @param string $phone     User's phone number (optional)
     * @return bool|string
     */
    public function send_order_cancelled_notification($order, $old_status, $phone = null) {
        return $this->send_order_status_notification($order, $old_status, 'canceled', $phone);
    }

    /**
     * Send notification for order refunded
     * 
     * @param object $order     Order object
     * @param string $old_status Previous status
     * @param string $phone     User's phone number (optional)
     * @return bool|string
     */
    public function send_order_refunded_notification($order, $old_status, $phone = null) {
        return $this->send_order_status_notification($order, $old_status, 'refunded', $phone);
    }

    /**
     * Get event type from order status
     * 
     * @param string $status
     * @return string|null
     */
    private function _get_event_type_from_status($status) {
        $status = strtolower($status);
        $status_map = array(
            'completed' => 'order_completed',
            'partial' => 'order_partial',
            'canceled' => 'order_cancelled',
            'cancelled' => 'order_cancelled',
            'refunded' => 'order_refunded',
        );

        return isset($status_map[$status]) ? $status_map[$status] : null;
    }

    /**
     * Get user details by user ID
     * 
     * @param int $user_id
     * @return object|null
     */
    private function _get_user_details($user_id) {
        try {
            $this->CI->db->select('id, first_name, last_name, email, whatsapp_number, balance');
            $this->CI->db->where('id', $user_id);
            return $this->CI->db->get('users')->row();
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to get user details - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get service details by service ID
     * 
     * @param int $service_id
     * @return object|null
     */
    private function _get_service_details($service_id) {
        try {
            $this->CI->db->select('id, name');
            $this->CI->db->where('id', $service_id);
            return $this->CI->db->get('services')->row();
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Notification: Failed to get service details - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate delivered quantity from order
     * 
     * @param object $order
     * @return int|string
     */
    private function _calculate_delivered_quantity($order) {
        if (!isset($order->quantity)) {
            return '';
        }
        
        if (!isset($order->remains)) {
            return $order->quantity;
        }
        
        $remains = is_numeric($order->remains) ? (int)$order->remains : 0;
        return $order->quantity - $remains;
    }

    /**
     * Log notification event (optional logging)
     * 
     * @param int    $order_id
     * @param string $event_type
     * @param string $old_status
     * @param string $new_status
     * @param bool   $success
     */
    private function _log_notification($order_id, $event_type, $old_status, $new_status, $success) {
        $log_message = sprintf(
            'WhatsApp Notification: Order #%d status changed from %s to %s, event: %s, sent: %s',
            $order_id,
            $old_status,
            $new_status,
            $event_type,
            $success ? 'YES' : 'NO'
        );
        log_message('info', $log_message);
    }

    /**
     * Check if notification is enabled for a specific status
     * 
     * @param string $status
     * @return bool
     */
    public function is_status_notification_enabled($status) {
        $event_type = $this->_get_event_type_from_status($status);
        if (!$event_type) {
            return false;
        }

        $notification = $this->_get_notification_settings($event_type);
        return $notification && $notification->status == 1;
    }
}