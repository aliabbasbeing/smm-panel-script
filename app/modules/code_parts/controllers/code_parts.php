<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Code Parts & Blocks Manager Controller
 * A dedicated module for managing custom HTML code blocks for different pages
 */
class code_parts extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Ensure only admin can access this module
        if (!get_role('admin')) {
            redirect(cn('home'));
        }
    }

    /**
     * Main index page - displays all code parts with tab-based UI
     */
    public function index($active_tab = ''){
        // Initialize default code parts if table exists but is empty
        if ($this->db->table_exists('code_parts')) {
            $code_parts = $this->model->get_all();
            if (empty($code_parts)) {
                $this->model->initialize_defaults();
            }
        }
        
        $data = array(
            "module"      => get_class($this),
            "active_tab"  => $active_tab ?: 'dashboard'
        );

        $this->template->build('index', $data);
    }

    /**
     * AJAX endpoint to save code part content
     * Uses the standard actionForm pattern
     */
    public function ajax_save(){
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

        $page_key = $this->input->post('page_key', true);
        $content = $this->input->post('content', false); // false to allow HTML

        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        // Sanitize HTML content using shared helper function
        $sanitized_content = sanitize_code_part_html($content);

        // Save using model
        $result = $this->model->save($page_key, $sanitized_content);

        if ($result) {
            ms([
                "status"  => "success",
                "message" => lang('Update_successfully')
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'Failed to save code part'
            ]);
        }
    }
}
