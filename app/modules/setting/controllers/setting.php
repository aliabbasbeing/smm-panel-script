<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class setting extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        $this->load->model('language/language_model', 'sub_model');
    }

    public function index($tab = ""){
        $path              = APPPATH.'./modules/setting/views/';
        $path_integrations = APPPATH.'./modules/setting/views/integrations/';
        $tabs = array_merge(
            get_name_of_files_in_dir($path, ['.php']),
            get_name_of_files_in_dir($path_integrations, ['.php'])
        );
        if (($idx = array_search('index', $tabs, true)) !== false) {
            unset($tabs[$idx]);
        }

        if ($tab == "") {
            $tab = "website_setting";
        }
        if (!in_array($tab, $tabs)) {
            redirect(cn('setting'));
        }

        // Load WhatsApp API settings from whatsapp_config (single-row pattern)
        $whatsapp_api = $this->db->get('whatsapp_config')->row();
        $data = [
            "module"       => get_class($this),
            "tab"          => $tab,
            "whatsapp_api" => $whatsapp_api,  // may be null if not created yet
        ];

        $this->template->build('index', $data);
    }

    public function get_content($tab = ""){
        $path              = APPPATH.'./modules/setting/views/';
        $path_integrations = APPPATH.'./modules/setting/views/integrations/';
        $tabs = array_merge(
            get_name_of_files_in_dir($path, ['.php']),
            get_name_of_files_in_dir($path_integrations, ['.php'])
        );
        if (($idx = array_search('index', $tabs, true)) !== false) {
            unset($tabs[$idx]);
        }

        if ($tab == "") {
            $tab = "website_setting";
        }
        if (!in_array($tab, $tabs)) {
            redirect(cn('setting'));
        }

        // Also supply API settings here if partial loads happen via AJAX tab switching
        $whatsapp_api = $this->db->get('whatsapp_config')->row();
        $data = [
            "module"       => get_class($this),
            "tab"          => $tab,
            "whatsapp_api" => $whatsapp_api,
        ];
        $this->template->build('index', $data);
    }

    /**
     * Generic settings saver (existing logic).
     * Saves POST keys as options, including whatsapp_number.
     */
    public function ajax_general_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $data              = $this->input->post(NULL, true);
        $default_home_page = $this->input->post("default_home_page", true);

        if (is_array($data)) {
            foreach ($data as $key => $value) {

                if (in_array($key, ['csrf_token_name','csrf_test_name'], true)) {
                    continue;
                }

                if (in_array($key, ['embed_javascript', 'embed_head_javascript', 'manual_payment_content'])) {
                    $value = htmlspecialchars(@$_POST[$key], ENT_QUOTES);
                }

                if (in_array($key, ['midtrans_payment_channels', 'coinpayments_acceptance', 'freekassa_acceptance'], true)) {
                    $value = json_encode($value);
                }

                if ($key === 'new_currecry_rate') {
                    $value = (double)$value;
                    if ($value <= 0) $value = 1;
                }

                if ($key === 'whatsapp_number') {
                    $value = trim($value);
                    $normalized = preg_replace('/[\s\-\(\)]+/', '', $value);
                    if ($normalized !== '' && !preg_match('/^\+?[0-9]{6,20}$/', $normalized)) {
                        ms([
                            'status'  => 'error',
                            'message' => 'Invalid WhatsApp number format'
                        ]);
                    }
                    $value = $normalized;
                }

                update_option($key, $value);
            }
        }

        if ($default_home_page != "") {
            $theme_file_path = APPPATH."../themes/config.json";
            if (is_writable(dirname($theme_file_path))) {
                if ($theme_file = @fopen($theme_file_path, "w")) {
                    $txt = '{ "theme" : "'.$default_home_page.'" }';
                    fwrite($theme_file, $txt);
                    fclose($theme_file);
                }
            }
        }

        ms([
            "status"  => "success",
            "message" => lang('Update_successfully')
        ]);
    }

    /**
     * Save WhatsApp API settings (url, api_key, admin_phone) to whatsapp_config table.
     * Table schema expected:
     * id (INT PK, usually 1), url VARCHAR, api_key VARCHAR, admin_phone VARCHAR
     */
    public function ajax_whatsapp_api_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $url         = trim($this->input->post('url', true));
        $api_key     = trim($this->input->post('api_key', true));
        $admin_phone = trim($this->input->post('admin_phone', true));

        // Basic validation (adjust as needed)
        if ($url === '' || $api_key === '' || $admin_phone === '') {
            ms([
                'status'  => 'error',
                'message' => 'All fields are required'
            ]);
        }

        // Normalize admin phone (optional)
        $normalized_phone = preg_replace('/[\s\-\(\)]+/', '', $admin_phone);
        if (!preg_match('/^\+?[0-9]{6,20}$/', $normalized_phone)) {
            ms([
                'status'  => 'error',
                'message' => 'Invalid admin phone format'
            ]);
        }

        // Check if table exists
        if (!$this->db->table_exists('whatsapp_config')) {
            ms([
                'status'  => 'error',
                'message' => 'WhatsApp config table not found. Please check your database setup.'
            ]);
        }

        // Ensure single row pattern
        $existing = $this->db->get('whatsapp_config')->row();
        $data = [
            'url'         => $url,
            'api_key'     => $api_key,
            'admin_phone' => $normalized_phone,
        ];

        if ($existing) {
            $this->db->where('id', $existing->id)->update('whatsapp_config', $data);
        } else {
            // Force id=1 (optional) or let auto-increment
            $this->db->insert('whatsapp_config', $data);
        }

        ms([
            'status'  => 'success',
            'message' => lang('Update_successfully'),
            'data'    => $data
        ]);
    }

    /**
     * Save WhatsApp notification settings
     */
    public function ajax_whatsapp_notifications() {
        // Check if it's a POST request
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $notification_status = $this->input->post('notification_status', true);
        $notification_template = $this->input->post('notification_template', true);

        if (!is_array($notification_status)) {
            $notification_status = array();
        }
        if (!is_array($notification_template)) {
            $notification_template = array();
        }

        // Check if table exists first
        if (!$this->db->table_exists('whatsapp_notifications')) {
            ms([
                'status'  => 'error',
                'message' => 'WhatsApp notifications table not found. Please run the database migration: /database/whatsapp-notifications.sql'
            ]);
        }

        // Get all notifications from database
        $all_notifications = $this->db->order_by('id', 'ASC')->get('whatsapp_notifications')->result();

        if (empty($all_notifications)) {
            ms([
                'status'  => 'error',
                'message' => 'No notification templates found. Please run the database migration.'
            ]);
        }

        $updated_count = 0;
        $total_count = count($all_notifications);

        foreach ($all_notifications as $notification) {
            $event_type = $notification->event_type;
            
            // Update status (1 if checked, 0 if not)
            $status = isset($notification_status[$event_type]) ? 1 : 0;
            
            // Update template if provided
            $template = isset($notification_template[$event_type]) ? trim($notification_template[$event_type]) : $notification->template;

            // Update in database
            $update_data = array(
                'status' => $status,
                'template' => $template
            );

            $this->db->where('event_type', $event_type);
            $this->db->update('whatsapp_notifications', $update_data);

            // Count as updated even if no rows changed (same data)
            $updated_count++;
        }

        ms([
            'status'  => 'success',
            'message' => lang('Update_successfully') . " ({$updated_count}/{$total_count} notifications processed)"
        ]);
    }

    /**
     * Save Code Parts HTML settings.
     * Stores HTML content without escaping for full HTML rendering on frontend pages.
     */
    public function ajax_code_parts() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        // List of valid code parts option keys
        $valid_code_parts = [
            'dashboard_code_part',
            'new_order_code_part',
            'orders_code_part',
            'services_code_part',
            'add_funds_code_part',
            'api_code_part',
            'tickets_code_part',
            'child_panel_code_part',
            'transactions_code_part',
            'signin_code_part',
            'signup_code_part'
        ];

        $data = $this->input->post(NULL, false); // Get raw POST data without XSS filtering for HTML content

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Skip CSRF token
                if (in_array($key, ['csrf_token_name', 'csrf_test_name'], true)) {
                    continue;
                }

                // Only process valid code parts keys
                if (in_array($key, $valid_code_parts, true)) {
                    // Store the HTML content (allow full HTML)
                    update_option($key, $value);
                }
            }
        }

        ms([
            "status"  => "success",
            "message" => lang('Update_successfully')
        ]);
    }
}