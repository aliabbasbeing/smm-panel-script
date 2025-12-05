<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Code Parts Module Controller
 * Dedicated module for managing custom HTML code blocks for different pages.
 * Separated from settings for cleaner architecture and easier management.
 */
class code_parts extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
    }

    /**
     * Display the Code Parts management page
     */
    public function index(){
        // Ensure only admin can access this feature
        if (!get_role('admin')) {
            redirect(cn('statistics'));
        }

        $code_parts = $this->model->get_all();
        
        $data = array(
            "module"     => get_class($this),
            "code_parts" => $code_parts,
        );
        
        $this->template->build("index", $data);
    }

    /**
     * Save Code Parts HTML content.
     * Stores sanitized HTML content in the code_parts database table.
     * Only accessible by admin users.
     */
    public function ajax_save() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
            return;
        }

        // Ensure only admin can access this feature
        if (!get_role('admin')) {
            ms([
                'status'  => 'error',
                'message' => 'Access denied. Admin only.'
            ]);
            return;
        }

        // Check if code_parts table exists
        if (!$this->db->table_exists('code_parts')) {
            ms([
                'status'  => 'error',
                'message' => 'Code parts table not found. Please run the database migration: /database/code-parts.sql'
            ]);
            return;
        }

        // Get page_key and content from POST
        $page_key = $this->input->post('page_key', true);
        $content = $this->input->post('content', false); // false to allow HTML

        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        // Basic sanitization - remove dangerous scripts but keep styling
        $sanitized_content = $this->sanitize_html($content);

        // Save using the model
        $result = $this->model->save($page_key, $sanitized_content);

        if ($result) {
            ms([
                "status"  => "success",
                "message" => lang('Update_successfully')
            ]);
        } else {
            ms([
                "status"  => "error",
                "message" => lang('There_was_an_error_processing_your_request_Please_try_again_later')
            ]);
        }
    }

    /**
     * Toggle code part status (enable/disable)
     */
    public function ajax_toggle_status() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
            return;
        }

        // Ensure only admin can access this feature
        if (!get_role('admin')) {
            ms([
                'status'  => 'error',
                'message' => 'Access denied. Admin only.'
            ]);
            return;
        }

        $page_key = $this->input->post('page_key', true);
        $status = (int)$this->input->post('status', true);

        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        $result = $this->model->update_status($page_key, $status);

        if ($result) {
            ms([
                "status"  => "success",
                "message" => lang('Update_successfully')
            ]);
        } else {
            ms([
                "status"  => "error",
                "message" => lang('There_was_an_error_processing_your_request_Please_try_again_later')
            ]);
        }
    }

    /**
     * Get code part content via AJAX
     */
    public function ajax_get_content() {
        // Ensure only admin can access this feature
        if (!get_role('admin')) {
            ms([
                'status'  => 'error',
                'message' => 'Access denied. Admin only.'
            ]);
            return;
        }

        $page_key = $this->input->get('page_key', true);

        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        $content = $this->model->get_content($page_key);

        ms([
            'status'  => 'success',
            'content' => $content
        ]);
    }

    /**
     * Sanitize HTML code parts - remove dangerous elements while allowing styling.
     * @param string $html The HTML content to sanitize
     * @return string Sanitized HTML
     */
    private function sanitize_html($html) {
        if (empty($html)) {
            return '';
        }

        // Remove script tags and their content
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        
        // Remove noscript tags
        $html = preg_replace('/<noscript\b[^>]*>(.*?)<\/noscript>/is', '', $html);
        
        // Remove javascript: protocol from attributes
        $html = preg_replace('/\b(href|src|action)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '$1="#"', $html);
        
        // Remove event handlers (onclick, onload, etc.)
        $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*[^\s>]*/i', '', $html);
        
        // Remove iframe, object, embed tags
        $html = preg_replace('/<(iframe|object|embed)\b[^>]*>(.*?)<\/\1>/is', '', $html);
        $html = preg_replace('/<(iframe|object|embed)\b[^>]*\/?>/i', '', $html);
        
        return trim($html);
    }
}
