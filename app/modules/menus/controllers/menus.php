<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class menus extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Only admin can access this module
        if (!get_role("admin")) {
            redirect(cn());
        }
    }

    /**
     * Display menu management page
     */
    public function index(){
        $data = [
            "module" => get_class($this),
            "menu_items" => $this->model->get_menu_items(),
            "available_roles" => $this->model->get_available_roles(),
        ];
        $this->template->build('index', $data);
    }

    /**
     * Get menu item for editing (AJAX)
     */
    public function ajax_get_item(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $id = $this->input->post('id', true);
        $menu_items = $this->model->get_menu_items();
        
        $item = null;
        foreach ($menu_items as $menu_item) {
            if ($menu_item['id'] == $id) {
                $item = $menu_item;
                break;
            }
        }

        if ($item) {
            ms([
                'status' => 'success',
                'data' => $item
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => 'Menu item not found'
            ]);
        }
    }

    /**
     * Add new menu item (AJAX)
     */
    public function ajax_add(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $title = trim($this->input->post('title', true));
        $url = trim($this->input->post('url', true));
        $icon = trim($this->input->post('icon', true));
        $roles = $this->input->post('roles', true);
        $new_tab = $this->input->post('new_tab', true) == '1' ? 1 : 0;
        $status = $this->input->post('status', true) == '1' ? 1 : 0;

        // Validation
        if (empty($title)) {
            ms([
                'status' => 'error',
                'message' => lang('Title is required')
            ]);
        }

        if (empty($url)) {
            ms([
                'status' => 'error',
                'message' => lang('URL is required')
            ]);
        }

        // Process roles
        if (!is_array($roles)) {
            $roles = ['everyone'];
        }

        $result = $this->model->add_menu_item([
            'title' => $title,
            'url' => $url,
            'icon' => $icon,
            'roles' => $roles,
            'new_tab' => $new_tab,
            'status' => $status
        ]);

        if ($result) {
            ms([
                'status' => 'success',
                'message' => lang('Menu item added successfully')
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => lang('Failed to add menu item')
            ]);
        }
    }

    /**
     * Update menu item (AJAX)
     */
    public function ajax_update(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $id = $this->input->post('id', true);
        $title = trim($this->input->post('title', true));
        $url = trim($this->input->post('url', true));
        $icon = trim($this->input->post('icon', true));
        $roles = $this->input->post('roles', true);
        $new_tab = $this->input->post('new_tab', true) == '1' ? 1 : 0;
        $status = $this->input->post('status', true) == '1' ? 1 : 0;

        // Validation
        if (empty($id)) {
            ms([
                'status' => 'error',
                'message' => lang('Invalid menu item')
            ]);
        }

        if (empty($title)) {
            ms([
                'status' => 'error',
                'message' => lang('Title is required')
            ]);
        }

        if (empty($url)) {
            ms([
                'status' => 'error',
                'message' => lang('URL is required')
            ]);
        }

        // Process roles
        if (!is_array($roles)) {
            $roles = ['everyone'];
        }

        $result = $this->model->update_menu_item($id, [
            'title' => $title,
            'url' => $url,
            'icon' => $icon,
            'roles' => $roles,
            'new_tab' => $new_tab,
            'status' => $status
        ]);

        if ($result) {
            ms([
                'status' => 'success',
                'message' => lang('Menu item updated successfully')
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => lang('Failed to update menu item')
            ]);
        }
    }

    /**
     * Delete menu item (AJAX)
     */
    public function ajax_delete(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $id = $this->input->post('id', true);

        if (empty($id)) {
            ms([
                'status' => 'error',
                'message' => lang('Invalid menu item')
            ]);
        }

        $result = $this->model->delete_menu_item($id);

        if ($result) {
            ms([
                'status' => 'success',
                'message' => lang('Menu item deleted successfully')
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => lang('Failed to delete menu item')
            ]);
        }
    }

    /**
     * Reorder menu items (AJAX)
     */
    public function ajax_reorder(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $order = $this->input->post('order', true);

        if (!is_array($order)) {
            ms([
                'status' => 'error',
                'message' => lang('Invalid order data')
            ]);
        }

        $result = $this->model->reorder_menu_items($order);

        if ($result) {
            ms([
                'status' => 'success',
                'message' => lang('Menu order updated successfully')
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => lang('Failed to update menu order')
            ]);
        }
    }

    /**
     * Toggle menu item status (AJAX)
     */
    public function ajax_toggle_status(){
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $id = $this->input->post('id', true);

        if (empty($id)) {
            ms([
                'status' => 'error',
                'message' => lang('Invalid menu item')
            ]);
        }

        $result = $this->model->toggle_menu_status($id);

        if ($result !== false) {
            ms([
                'status' => 'success',
                'message' => lang('Status updated successfully'),
                'new_status' => $result
            ]);
        } else {
            ms([
                'status' => 'error',
                'message' => lang('Failed to update status')
            ]);
        }
    }
}
