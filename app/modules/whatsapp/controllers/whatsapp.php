<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Module Controller
 * 
 * Handles WhatsApp device management, notifications, and message logs.
 * 
 * @package    SMM Panel
 * @subpackage Modules/WhatsApp
 */
class Whatsapp extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('whatsapp_model', 'model');
    }

    /**
     * Default route - redirect to device
     */
    public function index() {
        redirect(cn('whatsapp/device'));
    }

    // ==================== DEVICE MANAGEMENT ====================

    /**
     * Device connection dashboard
     */
    public function device() {
        $config = $this->model->get_config();
        
        $data = [
            'module' => get_class($this),
            'tab' => 'device',
            'config' => $config,
            'is_configured' => $this->model->is_configured()
        ];

        $this->template->build('device', $data);
    }

    /**
     * Fetch QR code via AJAX
     */
    public function fetch_qr() {
        $result = $this->model->fetch_qr();
        
        if ($result['success'] && isset($result['qr'])) {
            ms([
                'status' => 'success',
                'qr' => $result['qr']
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => isset($result['error']) ? $result['error'] : 'Failed to fetch QR code'
            ]);
        }
    }

    /**
     * Refresh connection status via AJAX
     */
    public function refresh_status() {
        $result = $this->model->fetch_status();
        
        ms([
            'status' => $result['success'] ? 'success' : 'error',
            'connected' => isset($result['connected']) ? $result['connected'] : false,
            'phoneNumber' => isset($result['phoneNumber']) ? $result['phoneNumber'] : null,
            'message' => isset($result['error']) ? $result['error'] : ''
        ]);
    }

    /**
     * Get health status via AJAX
     */
    public function get_health() {
        $result = $this->model->fetch_health();
        
        if ($result['success']) {
            ms([
                'status' => 'success',
                'data' => $result['data']
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => isset($result['error']) ? $result['error'] : 'Failed to get health status'
            ]);
        }
    }

    /**
     * Ping the server via AJAX
     */
    public function ping() {
        $result = $this->model->ping();
        
        if ($result['success']) {
            ms([
                'status' => 'success',
                'data' => isset($result['data']) ? $result['data'] : []
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => isset($result['error']) ? $result['error'] : 'Ping failed'
            ]);
        }
    }

    /**
     * Logout from WhatsApp via AJAX
     */
    public function logout() {
        if ($this->input->method() !== 'post') {
            ms(['status' => 'error', 'message' => 'Invalid method']);
        }

        $result = $this->model->logout();
        
        if ($result['success']) {
            ms([
                'status' => 'success',
                'message' => 'Logged out successfully. Please refresh to scan a new QR.'
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => isset($result['error']) ? $result['error'] : 'Logout failed'
            ]);
        }
    }

    /**
     * Save API configuration
     */
    public function save_config() {
        if ($this->input->method() !== 'post') {
            ms(['status' => 'error', 'message' => 'Invalid method']);
        }

        $base_url = trim($this->input->post('base_url', true));
        $api_key = trim($this->input->post('api_key', true));
        $admin_phone = trim($this->input->post('admin_phone', true));

        if (empty($base_url) || empty($api_key) || empty($admin_phone)) {
            ms(['status' => 'error', 'message' => 'All fields are required']);
        }

        // Normalize phone number
        $admin_phone = preg_replace('/[\s\-\(\)]+/', '', $admin_phone);
        if (!preg_match('/^\+?[0-9]{6,20}$/', $admin_phone)) {
            ms(['status' => 'error', 'message' => 'Invalid phone number format']);
        }

        $data = [
            'base_url' => rtrim($base_url, '/'),
            'api_key' => $api_key,
            'admin_phone' => $admin_phone
        ];

        if ($this->model->save_config($data)) {
            ms(['status' => 'success', 'message' => 'Configuration saved successfully']);
        } else {
            ms(['status' => 'error', 'message' => 'Failed to save configuration']);
        }
    }

    // ==================== NOTIFICATIONS ====================

    /**
     * Notification settings page
     */
    public function notifications() {
        $notifications = $this->model->get_all_notifications();
        $config = $this->model->get_config();
        
        $data = [
            'module' => get_class($this),
            'tab' => 'notifications',
            'notifications' => $notifications,
            'config' => $config,
            'is_configured' => $this->model->is_configured()
        ];

        $this->template->build('notifications', $data);
    }

    /**
     * Save notification settings
     */
    public function save_notification_settings() {
        if ($this->input->method() !== 'post') {
            ms(['status' => 'error', 'message' => 'Invalid method']);
        }

        $statuses = $this->input->post('notification_status', true);
        $templates = $this->input->post('notification_template', true);

        if (!is_array($statuses)) {
            $statuses = [];
        }
        if (!is_array($templates)) {
            $templates = [];
        }

        $updated = $this->model->batch_update_notifications($statuses, $templates);

        ms([
            'status' => 'success',
            'message' => "Successfully updated {$updated} notification templates"
        ]);
    }

    /**
     * Send test message via AJAX
     */
    public function test_message() {
        if ($this->input->method() !== 'post') {
            ms(['status' => 'error', 'message' => 'Invalid method']);
        }

        $phone = trim($this->input->post('phone', true));
        $message = trim($this->input->post('message', true));

        if (empty($phone)) {
            // Use admin phone if no phone provided
            $phone = $this->model->get_admin_phone();
        }

        if (empty($phone)) {
            ms(['status' => 'error', 'message' => 'No phone number provided']);
        }

        if (empty($message)) {
            $message = 'This is a test message from your SMM Panel WhatsApp integration.';
        }

        $result = $this->model->send_message($phone, $message);

        if ($result['success']) {
            ms([
                'status' => 'success',
                'message' => 'Test message sent successfully!'
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => isset($result['error']) ? $result['error'] : 'Failed to send test message'
            ]);
        }
    }

    // ==================== LOGS ====================

    /**
     * Message logs page
     */
    public function logs() {
        $page = max(1, (int)$this->input->get('page', true));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'status' => $this->input->get('status', true),
            'event_type' => $this->input->get('event_type', true),
            'phone' => $this->input->get('phone', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
            'search' => $this->input->get('search', true)
        ];

        $logs = $this->model->get_logs($filters, $limit, $offset);
        $total = $this->model->count_logs($filters);
        $stats = $this->model->get_log_stats();

        // Pagination
        $config = [
            'base_url' => cn('whatsapp/logs'),
            'total_rows' => $total,
            'per_page' => $limit,
            'use_page_numbers' => true,
            'prev_link' => '<i class="fe fe-chevron-left"></i>',
            'first_link' => '<i class="fe fe-chevrons-left"></i>',
            'next_link' => '<i class="fe fe-chevron-right"></i>',
            'last_link' => '<i class="fe fe-chevrons-right"></i>',
        ];
        $this->pagination->initialize($config);
        $links = $this->pagination->create_links();

        $data = [
            'module' => get_class($this),
            'tab' => 'logs',
            'logs' => $logs,
            'stats' => $stats,
            'filters' => $filters,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'links' => $links,
            'is_configured' => $this->model->is_configured()
        ];

        $this->template->build('logs', $data);
    }

    /**
     * Get logs via AJAX
     */
    public function get_logs_ajax() {
        $page = max(1, (int)$this->input->get('page', true));
        $limit = (int)$this->input->get('limit', true);
        $limit = $limit > 0 ? min($limit, 100) : 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'status' => $this->input->get('status', true),
            'event_type' => $this->input->get('event_type', true),
            'phone' => $this->input->get('phone', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
            'search' => $this->input->get('search', true)
        ];

        $logs = $this->model->get_logs($filters, $limit, $offset);
        $total = $this->model->count_logs($filters);

        ms([
            'status' => 'success',
            'data' => [
                'logs' => $logs,
                'total' => $total,
                'page' => $page,
                'pages' => $limit > 0 ? ceil($total / $limit) : 1
            ]
        ]);
    }

    /**
     * Delete old logs
     */
    public function delete_old_logs() {
        if ($this->input->method() !== 'post') {
            ms(['status' => 'error', 'message' => 'Invalid method']);
        }

        $days = (int)$this->input->post('days', true);
        if ($days < 1) {
            $days = 30;
        }

        $this->model->delete_old_logs($days);

        ms([
            'status' => 'success',
            'message' => "Deleted logs older than {$days} days"
        ]);
    }
}
