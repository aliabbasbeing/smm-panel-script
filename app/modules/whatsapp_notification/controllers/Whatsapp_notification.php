<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_notification extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Check if user is admin
        if (!get_role("admin")) {
            _validation('error', lang("You do not have permission to access this page"));
        }
    }

    public function index(){
        $this->api_settings();
    }

    /**
     * WhatsApp API Settings page
     */
    public function api_settings(){
        // Load WhatsApp API settings from whatsapp_config table
        $whatsapp_config = $this->db->get('whatsapp_config')->row();
        
        $data = [
            "module"           => strtolower(get_class($this)),
            "module_name"      => "WhatsApp API Settings",
            "whatsapp_config"  => $whatsapp_config,
        ];

        $this->template->build('api_settings', $data);
    }

    /**
     * Save WhatsApp API settings (url, api_key, admin_phone) to whatsapp_config table.
     */
    public function ajax_save_api_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $url         = trim($this->input->post('url', true));
        $api_key     = trim($this->input->post('api_key', true));
        $admin_phone = trim($this->input->post('admin_phone', true));

        // Basic validation
        if ($url === '' || $api_key === '' || $admin_phone === '') {
            ms([
                'status'  => 'error',
                'message' => 'All fields are required'
            ]);
        }

        // Normalize admin phone
        $normalized_phone = preg_replace('/[\s\-\(\)]+/', '', $admin_phone);
        if (!preg_match('/^\+?[0-9]{6,20}$/', $normalized_phone)) {
            ms([
                'status'  => 'error',
                'message' => 'Invalid admin phone format'
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
            $this->db->insert('whatsapp_config', $data);
        }

        if ($this->db->affected_rows() >= 0) {
            ms([
                'status'  => 'success',
                'message' => lang('Update_successfully')
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'Failed to update settings'
            ]);
        }
    }

    /**
     * Notification Templates page
     */
    public function notification_templates(){
        // Load the WhatsApp notification library
        $this->load->library('whatsapp_notification');
        $notifications = $this->whatsapp_notification->get_all_notifications();
        
        $data = [
            "module"        => strtolower(get_class($this)),
            "module_name"   => "WhatsApp Notification Templates",
            "notifications" => $notifications,
        ];

        $this->template->build('notification_templates', $data);
    }

    /**
     * Save WhatsApp notification settings
     */
    public function ajax_save_notification_templates() {
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

        // Load WhatsApp notification library
        $this->load->library('whatsapp_notification');

        // Get all notifications from database
        $all_notifications = $this->whatsapp_notification->get_all_notifications();

        $updated_count = 0;

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

            if ($this->db->affected_rows() > 0) {
                $updated_count++;
            }
        }

        ms([
            'status'  => 'success',
            'message' => lang('Update_successfully') . " ($updated_count notifications updated)"
        ]);
    }
}
