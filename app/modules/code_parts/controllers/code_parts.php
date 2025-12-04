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
     * Main index page - displays all code parts with a modern UI
     */
    public function index($active_tab = ''){
        // Get all code parts from database
        $code_parts = $this->model->get_all();
        
        // If no code parts exist, initialize default ones
        if (empty($code_parts)) {
            $this->model->initialize_defaults();
            $code_parts = $this->model->get_all();
        }
        
        $data = array(
            "module"      => get_class($this),
            "code_parts"  => $code_parts,
            "active_tab"  => $active_tab ?: 'dashboard'
        );

        $this->template->build('index', $data);
    }

    /**
     * AJAX endpoint to get code part content for editing
     * This allows lazy loading of content for better performance
     */
    public function ajax_get_content(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
            return;
        }

        $page_key = $this->input->post('page_key', true);
        
        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        $content = $this->model->get_content($page_key);
        $code_part = $this->model->get_by_key($page_key, false); // Get even if inactive
        
        ms([
            'status'  => 'success',
            'data'    => [
                'page_key' => $page_key,
                'content'  => $content,
                'status'   => $code_part ? $code_part->status : 1
            ]
        ]);
    }

    /**
     * AJAX endpoint to save code part content
     */
    public function ajax_save(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
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

    /**
     * AJAX endpoint to toggle code part status (enable/disable)
     */
    public function ajax_toggle_status(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
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

        $result = $this->model->update_status($page_key, $status ? 1 : 0);

        if ($result) {
            ms([
                "status"  => "success",
                "message" => $status ? lang('Enabled_successfully') : lang('Disabled_successfully')
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'Failed to update status'
            ]);
        }
    }

    /**
     * AJAX endpoint to create a new custom code part
     */
    public function ajax_create(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
            return;
        }

        $page_key = $this->input->post('page_key', true);
        $page_name = $this->input->post('page_name', true);

        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        // Sanitize page_key to only allow alphanumeric and underscore
        $page_key = preg_replace('/[^a-z0-9_]/', '', strtolower($page_key));
        
        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Invalid page key format'
            ]);
            return;
        }

        // Check if already exists
        if ($this->model->exists($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'A code part with this key already exists'
            ]);
            return;
        }

        $page_name = !empty($page_name) ? $page_name : ucwords(str_replace('_', ' ', $page_key)) . ' Page';

        $result = $this->model->create($page_key, $page_name);

        if ($result) {
            ms([
                "status"  => "success",
                "message" => lang('Created_successfully')
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'Failed to create code part'
            ]);
        }
    }

    /**
     * AJAX endpoint to delete a custom code part
     */
    public function ajax_delete(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
            return;
        }

        $page_key = $this->input->post('page_key', true);

        if (empty($page_key)) {
            ms([
                'status'  => 'error',
                'message' => 'Page key is required'
            ]);
            return;
        }

        // Prevent deletion of system code parts
        $system_parts = ['dashboard', 'new_order', 'orders', 'services', 'add_funds', 'api', 'tickets', 'child_panel', 'transactions', 'signin', 'signup'];
        if (in_array($page_key, $system_parts)) {
            ms([
                'status'  => 'error',
                'message' => 'Cannot delete system code parts. You can only disable them.'
            ]);
            return;
        }

        $result = $this->model->delete_by_key($page_key);

        if ($result) {
            ms([
                "status"  => "success",
                "message" => lang('Deleted_successfully')
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'Failed to delete code part'
            ]);
        }
    }

    /**
     * Get all code parts as JSON for AJAX refresh
     */
    public function ajax_list(){
        $code_parts = $this->model->get_all();
        
        ms([
            'status' => 'success',
            'data'   => $code_parts
        ]);
    }
}
