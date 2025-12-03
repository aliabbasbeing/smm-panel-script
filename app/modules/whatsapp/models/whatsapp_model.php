<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Model
 * 
 * Handles database operations and API calls to the Node.js WhatsApp server.
 * 
 * @package    SMM Panel
 * @subpackage Modules/WhatsApp
 */
class Whatsapp_model extends MY_Model {

    protected $api_url;
    protected $api_key;
    protected $admin_phone;
    protected $is_configured = false;

    public function __construct() {
        parent::__construct();
        $this->_load_config();
    }

    /**
     * Load WhatsApp API configuration from database
     */
    private function _load_config() {
        try {
            // Check if table exists first
            if (!$this->db->table_exists('whatsapp_config')) {
                $this->is_configured = false;
                return;
            }
            
            $config = $this->db->get('whatsapp_config')->row();
            
            if ($config && !empty($config->url) && !empty($config->api_key)) {
                $this->api_url = rtrim($config->url, '/');
                $this->api_key = $config->api_key;
                $this->admin_phone = isset($config->admin_phone) ? $config->admin_phone : '';
                $this->is_configured = true;
            }
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Model: Failed to load config - ' . $e->getMessage());
            $this->is_configured = false;
        }
    }

    /**
     * Check if WhatsApp API is configured
     */
    public function is_configured() {
        return $this->is_configured;
    }

    /**
     * Get API configuration
     */
    public function get_config() {
        if (!$this->db->table_exists('whatsapp_config')) {
            return null;
        }
        return $this->db->get('whatsapp_config')->row();
    }

    /**
     * Save API configuration
     */
    public function save_config($data) {
        if (!$this->db->table_exists('whatsapp_config')) {
            return false;
        }
        
        $existing = $this->db->get('whatsapp_config')->row();
        
        if ($existing) {
            $this->db->where('id', $existing->id);
            $result = $this->db->update('whatsapp_config', $data);
        } else {
            $result = $this->db->insert('whatsapp_config', $data);
        }
        
        // Reload config after saving
        if ($result) {
            $this->_load_config();
        }
        
        return $result;
    }

    // ==================== NODE.JS API ENDPOINTS ====================

    /**
     * Fetch QR code from Node server
     * GET /qr
     */
    public function fetch_qr() {
        if (!$this->is_configured) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $response = $this->_api_request('GET', '/qr');
        
        if ($response['success']) {
            // QR endpoint returns raw data
            return [
                'success' => true,
                'qr' => $response['data']
            ];
        }
        
        return $response;
    }

    /**
     * Fetch connection status from Node server
     * GET /status
     */
    public function fetch_status() {
        if (!$this->is_configured) {
            return ['success' => false, 'error' => 'WhatsApp API not configured', 'connected' => false];
        }

        $response = $this->_api_request('GET', '/status');
        
        if ($response['success'] && is_array($response['data'])) {
            return [
                'success' => true,
                'connected' => isset($response['data']['connected']) ? $response['data']['connected'] : false,
                'phoneNumber' => isset($response['data']['phoneNumber']) ? $response['data']['phoneNumber'] : null
            ];
        }
        
        return [
            'success' => false,
            'connected' => false,
            'error' => isset($response['error']) ? $response['error'] : 'Unknown error'
        ];
    }

    /**
     * Fetch health status from Node server
     * GET /health
     */
    public function fetch_health() {
        if (!$this->is_configured) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $response = $this->_api_request('GET', '/health');
        
        if ($response['success'] && is_array($response['data'])) {
            return [
                'success' => true,
                'data' => $response['data']
            ];
        }
        
        return [
            'success' => false,
            'error' => isset($response['error']) ? $response['error'] : 'Unknown error'
        ];
    }

    /**
     * Ping the Node server
     * GET /ping
     */
    public function ping() {
        if (!$this->is_configured) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $response = $this->_api_request('GET', '/ping');
        
        return $response;
    }

    /**
     * Logout from WhatsApp
     * POST /logout
     */
    public function logout() {
        if (!$this->is_configured) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $response = $this->_api_request('POST', '/logout');
        
        return $response;
    }

    /**
     * Send a message via WhatsApp
     * POST /send-message
     */
    public function send_message($phone_number, $message) {
        if (!$this->is_configured) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        // Remove + from phone number if present
        $phone_number = ltrim($phone_number, '+');

        $data = [
            'apiKey' => $this->api_key,
            'phoneNumber' => $phone_number,
            'message' => $message
        ];

        $response = $this->_api_request('POST', '/send-message', $data);
        
        // Log the message
        $this->log_message($phone_number, $message, 'manual_send', $response['success'] ? 'sent' : 'failed', $response);
        
        return $response;
    }

    /**
     * Make API request to Node.js server
     */
    private function _api_request($method, $endpoint, $data = null, $timeout = 10) {
        $url = $this->api_url . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'Connection error: ' . $curl_error,
                'http_code' => 0
            ];
        }

        // For QR endpoint, response might be raw data (base64 image)
        if ($endpoint === '/qr') {
            return [
                'success' => true,
                'data' => $response
            ];
        }

        // Try to decode JSON response
        $decoded = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'success' => $http_code >= 200 && $http_code < 300,
                'data' => $decoded,
                'http_code' => $http_code
            ];
        }

        return [
            'success' => $http_code >= 200 && $http_code < 300,
            'data' => $response,
            'http_code' => $http_code
        ];
    }

    // ==================== NOTIFICATION TEMPLATES ====================

    /**
     * Get all notification templates
     */
    public function get_all_notifications() {
        if (!$this->db->table_exists('whatsapp_notifications')) {
            return [];
        }
        return $this->db->order_by('id', 'ASC')->get('whatsapp_notifications')->result();
    }

    /**
     * Get notification by event type
     */
    public function get_notification($event_type) {
        if (!$this->db->table_exists('whatsapp_notifications')) {
            return null;
        }
        return $this->db->get_where('whatsapp_notifications', ['event_type' => $event_type])->row();
    }

    /**
     * Update notification settings
     */
    public function update_notification($event_type, $data) {
        if (!$this->db->table_exists('whatsapp_notifications')) {
            return false;
        }
        $this->db->where('event_type', $event_type);
        return $this->db->update('whatsapp_notifications', $data);
    }

    /**
     * Batch update notifications
     */
    public function batch_update_notifications($statuses, $templates) {
        if (!$this->db->table_exists('whatsapp_notifications')) {
            return 0;
        }
        
        $all_notifications = $this->get_all_notifications();
        $updated = 0;

        foreach ($all_notifications as $notification) {
            $event_type = $notification->event_type;
            $status = isset($statuses[$event_type]) ? 1 : 0;
            $template = isset($templates[$event_type]) ? trim($templates[$event_type]) : $notification->template;

            $this->db->where('event_type', $event_type);
            $this->db->update('whatsapp_notifications', [
                'status' => $status,
                'template' => $template
            ]);

            $updated++;
        }

        return $updated;
    }

    // ==================== MESSAGE LOGS ====================

    /**
     * Log a WhatsApp message
     * Works with existing whatsapp_logs table structure
     */
    public function log_message($phone, $message, $event, $status, $response = null) {
        if (!$this->db->table_exists('whatsapp_logs')) {
            return false;
        }
        
        $data = [
            'ids' => $this->_generate_ids(),
            'campaign_id' => 0,  // 0 for notification messages
            'recipient_id' => 0, // 0 for notification messages
            'phone_number' => $phone,
            'message' => $message,
            'status' => $status,
            'error_message' => $event . '|' . ($response ? json_encode($response) : ''), // Store event type in error_message
            'sent_at' => ($status == 'sent') ? date('Y-m-d H:i:s') : null,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('whatsapp_logs', $data);
    }

    /**
     * Get message logs with filters and pagination
     */
    public function get_logs($filters = [], $limit = 20, $offset = 0) {
        if (!$this->db->table_exists('whatsapp_logs')) {
            return [];
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['event_type'])) {
            $this->db->like('error_message', $filters['event_type'] . '|', 'after');
        }

        if (!empty($filters['phone'])) {
            $this->db->like('phone_number', $filters['phone']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('phone_number', $filters['search']);
            $this->db->or_like('message', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $results = $this->db->get('whatsapp_logs')->result();
        
        // Parse event_type from error_message
        foreach ($results as &$row) {
            $parts = explode('|', $row->error_message, 2);
            $row->event_type = isset($parts[0]) ? $parts[0] : '';
            $row->response = isset($parts[1]) ? $parts[1] : '';
        }
        
        return $results;
    }

    /**
     * Count logs with filters
     */
    public function count_logs($filters = []) {
        if (!$this->db->table_exists('whatsapp_logs')) {
            return 0;
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['event_type'])) {
            $this->db->like('error_message', $filters['event_type'] . '|', 'after');
        }

        if (!empty($filters['phone'])) {
            $this->db->like('phone_number', $filters['phone']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('phone_number', $filters['search']);
            $this->db->or_like('message', $filters['search']);
            $this->db->group_end();
        }

        return $this->db->count_all_results('whatsapp_logs');
    }

    /**
     * Get log statistics
     */
    public function get_log_stats() {
        $stats = [
            'total' => 0,
            'sent' => 0,
            'failed' => 0,
            'queued' => 0
        ];
        
        if (!$this->db->table_exists('whatsapp_logs')) {
            return $stats;
        }
        
        $stats['total'] = $this->db->count_all('whatsapp_logs');

        // Count by status
        $this->db->where('status', 'sent');
        $stats['sent'] = $this->db->count_all_results('whatsapp_logs');

        $this->db->where('status', 'failed');
        $stats['failed'] = $this->db->count_all_results('whatsapp_logs');

        $this->db->where('status', 'queued');
        $stats['queued'] = $this->db->count_all_results('whatsapp_logs');

        return $stats;
    }

    /**
     * Delete old logs
     */
    public function delete_old_logs($days = 30) {
        if (!$this->db->table_exists('whatsapp_logs')) {
            return false;
        }
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('created_at <', $cutoff);
        return $this->db->delete('whatsapp_logs');
    }

    /**
     * Generate unique IDs
     */
    private function _generate_ids() {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Get admin phone number
     */
    public function get_admin_phone() {
        return $this->admin_phone;
    }

    /**
     * Get API URL
     */
    public function get_api_url() {
        return $this->api_url;
    }
}
