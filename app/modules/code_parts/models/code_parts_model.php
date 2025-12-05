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
     * @return bool
     */
    public function save($page_key, $content){
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
        return $this->db->where('page_key', $page_key)->update($this->table, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
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
