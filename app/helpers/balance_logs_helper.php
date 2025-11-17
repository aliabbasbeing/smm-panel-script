<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Balance Logs Helper
 * 
 * Helper functions for logging balance changes
 */

if (!function_exists('log_balance_change')) {
    /**
     * Log a balance change
     * 
     * @param int $uid - User ID
     * @param string $action_type - Type of action: 'deduction', 'addition', 'refund', 'manual_add', 'manual_deduct'
     * @param float $amount - Amount of change (always positive)
     * @param float $balance_before - Balance before the change
     * @param float $balance_after - Balance after the change
     * @param string $description - Description of the change
     * @param string $related_id - Related order ID, transaction ID, etc. (optional)
     * @param string $related_type - Type of related record: 'order', 'transaction', 'refund', etc. (optional)
     * @return bool - True on success, false on failure
     */
    function log_balance_change($uid, $action_type, $amount, $balance_before, $balance_after, $description, $related_id = null, $related_type = null) {
        $CI =& get_instance();
        $CI->load->database();
        
        // Validate action type
        $valid_action_types = ['deduction', 'addition', 'refund', 'manual_add', 'manual_deduct'];
        if (!in_array($action_type, $valid_action_types)) {
            log_message('error', 'Invalid action_type for log_balance_change: ' . $action_type);
            return false;
        }
        
        // Prepare data
        $data = array(
            'ids'            => ids(),
            'uid'            => (int)$uid,
            'action_type'    => $action_type,
            'amount'         => round((float)$amount, 4),
            'balance_before' => round((float)$balance_before, 4),
            'balance_after'  => round((float)$balance_after, 4),
            'description'    => $description,
            'related_id'     => $related_id,
            'related_type'   => $related_type,
            'created'        => NOW,
        );
        
        // Insert into database
        $result = $CI->db->insert(BALANCE_LOGS, $data);
        
        if (!$result) {
            log_message('error', 'Failed to insert balance log for user ' . $uid);
            return false;
        }
        
        return true;
    }
}

if (!function_exists('log_order_deduction')) {
    /**
     * Log balance deduction for an order
     * 
     * @param int $uid - User ID
     * @param string $order_id - Order ID
     * @param float $amount - Amount deducted
     * @param float $balance_before - Balance before deduction
     * @param float $balance_after - Balance after deduction
     * @return bool
     */
    function log_order_deduction($uid, $order_id, $amount, $balance_before, $balance_after) {
        $description = "Order placed - ID: " . $order_id;
        return log_balance_change($uid, 'deduction', $amount, $balance_before, $balance_after, $description, $order_id, 'order');
    }
}

if (!function_exists('log_payment_addition')) {
    /**
     * Log balance addition from payment
     * 
     * @param int $uid - User ID
     * @param string $transaction_id - Transaction ID
     * @param float $amount - Amount added
     * @param float $balance_before - Balance before addition
     * @param float $balance_after - Balance after addition
     * @param string $payment_method - Payment method used
     * @return bool
     */
    function log_payment_addition($uid, $transaction_id, $amount, $balance_before, $balance_after, $payment_method = '') {
        $description = "Payment received";
        if ($payment_method) {
            $description .= " via " . $payment_method;
        }
        $description .= " - Transaction ID: " . $transaction_id;
        return log_balance_change($uid, 'addition', $amount, $balance_before, $balance_after, $description, $transaction_id, 'transaction');
    }
}

if (!function_exists('log_refund')) {
    /**
     * Log balance refund
     * 
     * @param int $uid - User ID
     * @param string $order_id - Order ID
     * @param float $amount - Amount refunded
     * @param float $balance_before - Balance before refund
     * @param float $balance_after - Balance after refund
     * @return bool
     */
    function log_refund($uid, $order_id, $amount, $balance_before, $balance_after) {
        $description = "Refund for order - ID: " . $order_id;
        return log_balance_change($uid, 'refund', $amount, $balance_before, $balance_after, $description, $order_id, 'refund');
    }
}

if (!function_exists('log_manual_funds')) {
    /**
     * Log manual funds addition or deduction by admin
     * 
     * @param int $uid - User ID
     * @param float $amount - Amount (positive for addition, will determine type automatically)
     * @param float $balance_before - Balance before change
     * @param float $balance_after - Balance after change
     * @param string $note - Note from admin
     * @param string $transaction_id - Transaction ID (optional)
     * @return bool
     */
    function log_manual_funds($uid, $amount, $balance_before, $balance_after, $note = '', $transaction_id = '') {
        // Determine if it's addition or deduction based on balance change
        $is_addition = $balance_after > $balance_before;
        $action_type = $is_addition ? 'manual_add' : 'manual_deduct';
        $description = "Manual funds " . ($is_addition ? "added" : "deducted") . " by admin";
        if ($note) {
            $description .= " - Note: " . $note;
        }
        return log_balance_change($uid, $action_type, abs($amount), $balance_before, $balance_after, $description, $transaction_id, 'manual');
    }
}

if (!function_exists('get_balance_action_class')) {
    /**
     * Get CSS class for balance action badge
     * 
     * @param string $action_type - Action type
     * @return string - CSS class name
     */
    function get_balance_action_class($action_type) {
        $classes = array(
            'deduction'     => 'badge-action-deduction',
            'addition'      => 'badge-action-addition',
            'refund'        => 'badge-action-refund',
            'manual_add'    => 'badge-action-manual_add',
            'manual_deduct' => 'badge-action-manual_deduct',
        );
        return isset($classes[$action_type]) ? $classes[$action_type] : 'badge-default';
    }
}

if (!function_exists('is_balance_positive_action')) {
    /**
     * Check if balance action is positive (increases balance)
     * 
     * @param string $action_type - Action type
     * @return bool - True if positive action
     */
    function is_balance_positive_action($action_type) {
        return in_array($action_type, ['addition', 'refund', 'manual_add']);
    }
}

if (!function_exists('format_balance_action_display')) {
    /**
     * Format action type for display
     * 
     * @param string $action_type - Action type
     * @return string - Formatted display text
     */
    function format_balance_action_display($action_type) {
        return ucfirst(str_replace('_', ' ', $action_type));
    }
}
