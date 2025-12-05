<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class menus_model extends MY_Model {

    private $option_name = 'header_menu_items';

    public function __construct(){
        parent::__construct();
    }

    /**
     * Get all menu items from general_options table
     * @return array
     */
    public function get_menu_items(){
        $menu_data = get_option($this->option_name, '[]');
        $items = json_decode($menu_data, true);
        
        if (!is_array($items)) {
            $items = [];
        }

        // Sort by sort_order
        usort($items, function($a, $b) {
            return ($a['sort_order'] ?? 0) - ($b['sort_order'] ?? 0);
        });

        return $items;
    }

    /**
     * Get menu items visible to current user based on role
     * @return array
     */
    public function get_visible_menu_items(){
        $items = $this->get_menu_items();
        $visible_items = [];

        $current_role = $this->get_current_user_role();

        foreach ($items as $item) {
            // Skip disabled items
            if (empty($item['status']) || $item['status'] != 1) {
                continue;
            }

            // Check role visibility
            $roles = isset($item['roles']) ? $item['roles'] : ['everyone'];
            
            if (in_array('everyone', $roles) || in_array($current_role, $roles)) {
                $visible_items[] = $item;
            }
        }

        return $visible_items;
    }

    /**
     * Get current user role
     * @return string
     */
    private function get_current_user_role(){
        if (!session('uid')) {
            return 'guest';
        }

        if (get_role('admin')) {
            return 'admin';
        }

        if (get_role('supporter')) {
            return 'supporter';
        }

        if (get_role('user')) {
            return 'user';
        }

        return 'user';
    }

    /**
     * Add a new menu item
     * @param array $data
     * @return bool
     */
    public function add_menu_item($data){
        $items = $this->get_menu_items();
        
        // Generate unique ID
        $max_id = 0;
        foreach ($items as $item) {
            if (isset($item['id']) && $item['id'] > $max_id) {
                $max_id = $item['id'];
            }
        }

        // Get highest sort order
        $max_sort = 0;
        foreach ($items as $item) {
            if (isset($item['sort_order']) && $item['sort_order'] > $max_sort) {
                $max_sort = $item['sort_order'];
            }
        }

        $new_item = [
            'id' => $max_id + 1,
            'title' => $data['title'],
            'url' => $data['url'],
            'icon' => isset($data['icon']) ? $data['icon'] : '',
            'roles' => isset($data['roles']) ? $data['roles'] : ['everyone'],
            'new_tab' => isset($data['new_tab']) ? (int)$data['new_tab'] : 0,
            'status' => isset($data['status']) ? (int)$data['status'] : 1,
            'sort_order' => $max_sort + 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $items[] = $new_item;

        return $this->save_menu_items($items);
    }

    /**
     * Update existing menu item
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_menu_item($id, $data){
        $items = $this->get_menu_items();
        $updated = false;

        foreach ($items as $key => $item) {
            if ($item['id'] == $id) {
                $items[$key]['title'] = $data['title'];
                $items[$key]['url'] = $data['url'];
                $items[$key]['icon'] = isset($data['icon']) ? $data['icon'] : '';
                $items[$key]['roles'] = isset($data['roles']) ? $data['roles'] : ['everyone'];
                $items[$key]['new_tab'] = isset($data['new_tab']) ? (int)$data['new_tab'] : 0;
                $items[$key]['status'] = isset($data['status']) ? (int)$data['status'] : 1;
                $items[$key]['updated_at'] = date('Y-m-d H:i:s');
                $updated = true;
                break;
            }
        }

        if ($updated) {
            return $this->save_menu_items($items);
        }

        return false;
    }

    /**
     * Delete a menu item
     * @param int $id
     * @return bool
     */
    public function delete_menu_item($id){
        $items = $this->get_menu_items();
        $new_items = [];

        foreach ($items as $item) {
            if ($item['id'] != $id) {
                $new_items[] = $item;
            }
        }

        return $this->save_menu_items($new_items);
    }

    /**
     * Reorder menu items
     * @param array $order Array of item IDs in new order
     * @return bool
     */
    public function reorder_menu_items($order){
        $items = $this->get_menu_items();
        
        // Create lookup array
        $item_lookup = [];
        foreach ($items as $item) {
            $item_lookup[$item['id']] = $item;
        }

        // Reorder items
        $sort = 1;
        foreach ($order as $id) {
            if (isset($item_lookup[$id])) {
                $item_lookup[$id]['sort_order'] = $sort;
                $sort++;
            }
        }

        return $this->save_menu_items(array_values($item_lookup));
    }

    /**
     * Toggle menu item status
     * @param int $id
     * @return int|bool New status or false on failure
     */
    public function toggle_menu_status($id){
        $items = $this->get_menu_items();
        $new_status = false;

        foreach ($items as $key => $item) {
            if ($item['id'] == $id) {
                $items[$key]['status'] = $items[$key]['status'] == 1 ? 0 : 1;
                $new_status = $items[$key]['status'];
                break;
            }
        }

        if ($new_status !== false) {
            if ($this->save_menu_items($items)) {
                return $new_status;
            }
        }

        return false;
    }

    /**
     * Save menu items to database
     * @param array $items
     * @return bool
     */
    private function save_menu_items($items){
        $json_data = json_encode($items, JSON_UNESCAPED_UNICODE);
        update_option($this->option_name, $json_data);
        return true;
    }

    /**
     * Get available roles for visibility settings
     * @return array
     */
    public function get_available_roles(){
        return [
            'everyone' => lang('Everyone'),
            'guest' => lang('Guest (Not logged in)'),
            'user' => lang('User'),
            'supporter' => lang('Supporter'),
            'admin' => lang('Admin'),
        ];
    }

    /**
     * Initialize default menu items if none exist
     * @return bool
     */
    public function init_default_menu(){
        $items = $this->get_menu_items();
        
        if (!empty($items)) {
            return false; // Already has items
        }

        $default_items = [
            [
                'id' => 1,
                'title' => 'Dashboard',
                'url' => 'statistics',
                'icon' => 'fe fe-bar-chart-2',
                'roles' => ['user', 'admin', 'supporter'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'id' => 2,
                'title' => 'New Order',
                'url' => 'order/add',
                'icon' => 'fe fe-shopping-cart',
                'roles' => ['user', 'admin', 'supporter'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 2
            ],
            [
                'id' => 3,
                'title' => 'Orders',
                'url' => 'order/log',
                'icon' => 'fa fa-shopping-cart',
                'roles' => ['user', 'admin', 'supporter'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 3
            ],
            [
                'id' => 4,
                'title' => 'Refill',
                'url' => 'refill/log',
                'icon' => 'fa fa-recycle',
                'roles' => ['user', 'admin', 'supporter'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 4
            ],
            [
                'id' => 5,
                'title' => 'Services',
                'url' => 'services',
                'icon' => 'fe fe-list',
                'roles' => ['everyone'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 5
            ],
            [
                'id' => 6,
                'title' => 'Add Funds',
                'url' => 'add_funds',
                'icon' => 'fa fa-money',
                'roles' => ['user', 'admin'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 6
            ],
            [
                'id' => 7,
                'title' => 'Tickets',
                'url' => 'tickets',
                'icon' => 'fa fa-comments-o',
                'roles' => ['user', 'admin', 'supporter'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 7
            ],
            [
                'id' => 8,
                'title' => 'Account',
                'url' => 'profile',
                'icon' => 'fa fa-user',
                'roles' => ['user', 'admin', 'supporter'],
                'new_tab' => 0,
                'status' => 1,
                'sort_order' => 8
            ]
        ];

        return $this->save_menu_items($default_items);
    }
}
