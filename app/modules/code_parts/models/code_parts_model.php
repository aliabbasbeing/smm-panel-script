<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Code Parts Model
 * Handles database operations for code parts/HTML blocks
 * Enhanced version with caching and optimized queries
 */
class Code_parts_model extends MY_Model {
    
    protected $table = 'code_parts';
    
    // Cache for code parts to reduce database queries
    private static $cache = [];
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Get code part by page key
     * @param string $page_key The page identifier
     * @param bool $active_only Only return active code parts
     * @return object|null
     */
    public function get_by_key($page_key, $active_only = true){
        $cache_key = $page_key . '_' . ($active_only ? '1' : '0');
        
        if (isset(self::$cache[$cache_key])) {
            return self::$cache[$cache_key];
        }
        
        $this->db->where('page_key', $page_key);
        if ($active_only) {
            $this->db->where('status', 1);
        }
        
        $result = $this->db->get($this->table)->row();
        self::$cache[$cache_key] = $result;
        
        return $result;
    }
    
    /**
     * Get raw content by page key (without variable processing)
     * @param string $page_key The page identifier
     * @return string
     */
    public function get_content($page_key){
        $result = $this->db->select('content')
                           ->where('page_key', $page_key)
                           ->get($this->table)
                           ->row();
        return ($result && !empty($result->content)) ? $result->content : '';
    }
    
    /**
     * Save code part content
     * @param string $page_key The page identifier
     * @param string $content The HTML content
     * @return bool
     */
    public function save($page_key, $content){
        // Clear cache for this key
        $this->clear_cache($page_key);
        
        $existing = $this->db->where('page_key', $page_key)->get($this->table)->row();
        
        if ($existing) {
            return $this->db->where('page_key', $page_key)->update($this->table, [
                'content' => $content,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->db->insert($this->table, [
                'page_key' => $page_key,
                'page_name' => ucwords(str_replace('_', ' ', $page_key)) . ' Page',
                'content' => $content,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Get all code parts
     * @return array
     */
    public function get_all(){
        return $this->db->order_by('page_key', 'ASC')
                        ->get($this->table)
                        ->result();
    }
    
    /**
     * Update code part status
     * @param string $page_key The page identifier
     * @param int $status 1 for active, 0 for inactive
     * @return bool
     */
    public function update_status($page_key, $status){
        // Clear cache for this key
        $this->clear_cache($page_key);
        
        return $this->db->where('page_key', $page_key)->update($this->table, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Check if a code part exists
     * @param string $page_key The page identifier
     * @return bool
     */
    public function exists($page_key){
        $result = $this->db->where('page_key', $page_key)->get($this->table)->row();
        return !empty($result);
    }
    
    /**
     * Create a new code part
     * @param string $page_key The page identifier
     * @param string $page_name Human readable name
     * @param string $content Initial content
     * @return bool
     */
    public function create($page_key, $page_name, $content = ''){
        return $this->db->insert($this->table, [
            'page_key' => $page_key,
            'page_name' => $page_name,
            'content' => $content,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Delete code part by page key
     * @param string $page_key The page identifier
     * @return bool
     */
    public function delete_by_key($page_key){
        // Clear cache for this key
        $this->clear_cache($page_key);
        
        return $this->db->where('page_key', $page_key)->delete($this->table);
    }
    
    /**
     * Initialize default code parts if table is empty
     * @return bool
     */
    public function initialize_defaults(){
        $defaults = [
            ['page_key' => 'dashboard', 'page_name' => 'Dashboard Page'],
            ['page_key' => 'new_order', 'page_name' => 'New Order Page'],
            ['page_key' => 'orders', 'page_name' => 'Order Logs Page'],
            ['page_key' => 'services', 'page_name' => 'Services Page'],
            ['page_key' => 'add_funds', 'page_name' => 'Add Funds Page'],
            ['page_key' => 'api', 'page_name' => 'API Page'],
            ['page_key' => 'tickets', 'page_name' => 'Tickets Page'],
            ['page_key' => 'child_panel', 'page_name' => 'Child Panel Page'],
            ['page_key' => 'transactions', 'page_name' => 'Transactions Page'],
            ['page_key' => 'signin', 'page_name' => 'Sign In Page'],
            ['page_key' => 'signup', 'page_name' => 'Sign Up Page']
        ];
        
        foreach ($defaults as $default) {
            if (!$this->exists($default['page_key'])) {
                $this->create($default['page_key'], $default['page_name'], '');
            }
        }
        
        return true;
    }
    
    /**
     * Get count of active code parts
     * @return int
     */
    public function count_active(){
        return $this->db->where('status', 1)
                        ->where('content !=', '')
                        ->count_all_results($this->table);
    }
    
    /**
     * Clear cache for a specific key or all
     * @param string|null $page_key
     */
    private function clear_cache($page_key = null){
        if ($page_key === null) {
            self::$cache = [];
        } else {
            unset(self::$cache[$page_key . '_0']);
            unset(self::$cache[$page_key . '_1']);
        }
    }
}
