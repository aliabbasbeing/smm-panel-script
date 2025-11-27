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
     * Stores sanitized HTML content for rendering on frontend pages.
     * Only accessible by admin users.
     */
    public function ajax_code_parts() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        // Ensure only admin can access this feature
        if (!get_role('admin')) {
            ms([
                'status'  => 'error',
                'message' => 'Access denied. Admin only.'
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

        $data = $this->input->post(NULL, false); // Get raw POST data for HTML content

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Skip CSRF token
                if (in_array($key, ['csrf_token_name', 'csrf_test_name'], true)) {
                    continue;
                }

                // Only process valid code parts keys
                if (in_array($key, $valid_code_parts, true)) {
                    // Sanitize HTML: remove dangerous tags while allowing styling elements
                    $sanitized_value = $this->sanitize_html_code_part($value);
                    update_option($key, $sanitized_value);
                }
            }
        }

        ms([
            "status"  => "success",
            "message" => lang('Update_successfully')
        ]);
    }

    /**
     * Sanitize HTML code parts - remove dangerous elements while allowing styling.
     * Uses DOMDocument for robust HTML parsing and sanitization.
     * @param string $html The HTML content to sanitize
     * @return string Sanitized HTML
     */
    private function sanitize_html_code_part($html) {
        if (empty($html)) {
            return '';
        }

        // First pass: regex-based removal of common attack vectors
        // Remove script tags and their content (handles malformed HTML too)
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<script[^>]*>/i', '', $html);
        $html = preg_replace('/<\/script>/i', '', $html);
        
        // Remove noscript tags
        $html = preg_replace('/<noscript[^>]*>.*?<\/noscript>/is', '', $html);
        
        // Remove various dangerous protocols from any attribute
        $html = preg_replace('/\b(href|src|action|formaction|data|poster|background)\s*=\s*["\']?\s*(javascript|vbscript|data):/i', '$1="#"', $html);
        
        // Remove event handlers - comprehensive patterns for various encodings
        $html = preg_replace('/\s+on[a-z]+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s+on[a-z]+\s*=\s*[^\s>]*/i', '', $html);
        
        // Remove style expressions (IE-specific XSS vector)
        $html = preg_replace('/expression\s*\([^)]*\)/i', '', $html);
        $html = preg_replace('/behavior\s*:[^;}"\']+/i', '', $html);
        $html = preg_replace('/-moz-binding\s*:[^;}"\']+/i', '', $html);
        
        // Remove iframe, object, embed, applet tags
        $html = preg_replace('/<(iframe|object|embed|applet)[^>]*>.*?<\/\1>/is', '', $html);
        $html = preg_replace('/<(iframe|object|embed|applet)[^>]*\/?>/i', '', $html);
        
        // Remove form elements (forms themselves and input elements)
        $html = preg_replace('/<form[^>]*>(.*?)<\/form>/is', '$1', $html);
        $html = preg_replace('/<(input|button|select|textarea)[^>]*\/?>/i', '', $html);
        $html = preg_replace('/<\/(input|button|select|textarea)>/i', '', $html);
        
        // Remove base and meta refresh tags
        $html = preg_replace('/<base[^>]*\/?>/i', '', $html);
        $html = preg_replace('/<meta[^>]*http-equiv\s*=\s*["\']?refresh[^>]*>/i', '', $html);
        
        // Remove XML/HTML comments that might contain exploits
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        
        // Second pass: Use DOMDocument for thorough attribute cleaning
        if (class_exists('DOMDocument')) {
            $html = $this->sanitize_with_dom($html);
        }
        
        return trim($html);
    }

    /**
     * Additional sanitization using DOMDocument for thorough attribute cleaning.
     * @param string $html The HTML to process
     * @return string Sanitized HTML
     */
    private function sanitize_with_dom($html) {
        // Suppress libxml errors
        libxml_use_internal_errors(true);
        
        $dom = new DOMDocument();
        // Wrap in a div to handle fragments and preserve encoding
        $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="sanitize-wrapper">' . $html . '</div></body></html>';
        
        if (!@$dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            libxml_clear_errors();
            return $html; // Return original if parsing fails
        }
        
        $xpath = new DOMXPath($dom);
        
        // Remove all event handler attributes
        $dangerous_attrs = ['onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 
            'onmousemove', 'onmouseout', 'onmouseenter', 'onmouseleave', 'onkeydown', 'onkeyup', 
            'onkeypress', 'onload', 'onunload', 'onerror', 'onabort', 'onblur', 'onfocus', 
            'onchange', 'onsubmit', 'onreset', 'onselect', 'oninput', 'oncontextmenu',
            'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 
            'ondrop', 'onscroll', 'onwheel', 'oncopy', 'oncut', 'onpaste', 'onbeforeunload',
            'onhashchange', 'onmessage', 'onoffline', 'ononline', 'onpagehide', 'onpageshow',
            'onpopstate', 'onstorage', 'onresize', 'ontouchstart', 'ontouchmove', 'ontouchend',
            'ontouchcancel', 'onanimationstart', 'onanimationend', 'onanimationiteration',
            'ontransitionend', 'onpointerdown', 'onpointerup', 'onpointermove', 'onpointerenter',
            'onpointerleave', 'onpointerover', 'onpointerout', 'onpointercancel', 'ongotpointercapture',
            'onlostpointercapture', 'formaction'];
        
        foreach ($dangerous_attrs as $attr) {
            $nodes = $xpath->query('//*[@' . $attr . ']');
            foreach ($nodes as $node) {
                $node->removeAttribute($attr);
            }
        }
        
        // Remove javascript: and data: from href/src attributes
        $link_nodes = $xpath->query('//*[@href or @src or @action]');
        foreach ($link_nodes as $node) {
            foreach (['href', 'src', 'action'] as $attr) {
                if ($node->hasAttribute($attr)) {
                    $value = $node->getAttribute($attr);
                    if (preg_match('/^\s*(javascript|vbscript|data):/i', $value)) {
                        $node->removeAttribute($attr);
                    }
                }
            }
        }
        
        // Get sanitized content from wrapper div
        $wrapper = $dom->getElementById('sanitize-wrapper');
        if ($wrapper) {
            $result = '';
            foreach ($wrapper->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }
            libxml_clear_errors();
            return $result;
        }
        
        libxml_clear_errors();
        return $html;
    }
}