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
}
