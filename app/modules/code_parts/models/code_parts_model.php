<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Code Parts Model
 * Handles database operations for code parts/HTML blocks.
 * Uses the existing 'code_parts' database table.
 */
class code_parts_model extends MY_Model {
    
    protected $table = 'code_parts';
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Get code part by page key
     * @param string $page_key The page identifier
     * @return object|null
     */
    public function get_by_key($page_key){
        return $this->db->where('page_key', $page_key)
                        ->where('status', 1)
                        ->get($this->table)
                        ->row();
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
     * @param array $settings Additional settings (device_visibility, display_position, etc.)
     * @return bool
     */
    public function save($page_key, $content, $settings = []){
        $existing = $this->db->where('page_key', $page_key)->get($this->table)->row();
        
        $data = [
            'content' => $content,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Add advanced settings if provided
        if (isset($settings['device_visibility'])) {
            $data['device_visibility'] = $settings['device_visibility'];
        }
        if (isset($settings['display_position'])) {
            $data['display_position'] = $settings['display_position'];
        }
        if (isset($settings['show_on_mobile'])) {
            $data['show_on_mobile'] = (int)$settings['show_on_mobile'];
        }
        if (isset($settings['show_on_desktop'])) {
            $data['show_on_desktop'] = (int)$settings['show_on_desktop'];
        }
        
        if ($existing) {
            return $this->db->where('page_key', $page_key)->update($this->table, $data);
        } else {
            $data['page_key'] = $page_key;
            $data['page_name'] = ucwords(str_replace('_', ' ', $page_key)) . ' Page';
            $data['status'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            
            // Set defaults for new records if not provided
            if (!isset($data['device_visibility'])) $data['device_visibility'] = 'both';
            if (!isset($data['display_position'])) $data['display_position'] = 'top';
            if (!isset($data['show_on_mobile'])) $data['show_on_mobile'] = 1;
            if (!isset($data['show_on_desktop'])) $data['show_on_desktop'] = 1;
            
            return $this->db->insert($this->table, $data);
        }
    }
    
    /**
     * Get all code parts (optimized for performance)
     * @param bool $activeOnly Whether to fetch only active code parts
     * @return array
     */
    public function get_all($activeOnly = false){
        $this->db->select('id, page_key, page_name, status, device_visibility, display_position, show_on_mobile, show_on_desktop, updated_at')
                 ->order_by('page_key', 'ASC');
        
        if ($activeOnly) {
            $this->db->where('status', 1);
        }
        
        return $this->db->get($this->table)->result();
    }
    
    /**
     * Update code part status
     * @param string $page_key The page identifier
     * @param int $status 1 for active, 0 for inactive
     * @return bool
     */
    public function update_status($page_key, $status){
        return $this->db->where('page_key', $page_key)->update($this->table, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get code part settings by page key
     * @param string $page_key The page identifier
     * @return object|null
     */
    public function get_settings($page_key){
        return $this->db->select('device_visibility, display_position, show_on_mobile, show_on_desktop')
                        ->where('page_key', $page_key)
                        ->get($this->table)
                        ->row();
    }
    
    /**
     * Delete code part by page key
     * @param string $page_key The page identifier
     * @return bool
     */
    public function delete_by_key($page_key){
        return $this->db->where('page_key', $page_key)->delete($this->table);
    }
}
