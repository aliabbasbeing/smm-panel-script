<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_logs_model extends MY_Model {
    
    public $tb_cron_logs;
    public $tb_cron_settings;
    
    public function __construct() {
        parent::__construct();
        $this->tb_cron_logs = 'cron_logs';
        $this->tb_cron_settings = 'cron_settings';
    }
    
    /**
     * Get paginated cron logs with filters
     */
    public function get_logs($params = array()) {
        $this->db->select('*');
        $this->db->from($this->tb_cron_logs);
        
        // Apply filters
        if (!empty($params['cron_name'])) {
            $this->db->like('cron_name', $params['cron_name']);
        }
        
        if (!empty($params['status'])) {
            $this->db->where('status', $params['status']);
        }
        
        if (!empty($params['date_from'])) {
            $this->db->where('executed_at >=', $params['date_from']);
        }
        
        if (!empty($params['date_to'])) {
            $this->db->where('executed_at <=', $params['date_to']);
        }
        
        // Search
        if (!empty($params['search'])) {
            $this->db->group_start();
            $this->db->like('cron_name', $params['search']);
            $this->db->or_like('response_message', $params['search']);
            $this->db->group_end();
        }
        
        // Order
        $order_by = !empty($params['order_by']) ? $params['order_by'] : 'executed_at';
        $order_dir = !empty($params['order_dir']) ? $params['order_dir'] : 'DESC';
        $this->db->order_by($order_by, $order_dir);
        
        // Pagination
        if (isset($params['limit']) && isset($params['offset'])) {
            $this->db->limit($params['limit'], $params['offset']);
        }
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Count total logs with filters
     */
    public function count_logs($params = array()) {
        $this->db->from($this->tb_cron_logs);
        
        // Apply same filters as get_logs
        if (!empty($params['cron_name'])) {
            $this->db->like('cron_name', $params['cron_name']);
        }
        
        if (!empty($params['status'])) {
            $this->db->where('status', $params['status']);
        }
        
        if (!empty($params['date_from'])) {
            $this->db->where('executed_at >=', $params['date_from']);
        }
        
        if (!empty($params['date_to'])) {
            $this->db->where('executed_at <=', $params['date_to']);
        }
        
        if (!empty($params['search'])) {
            $this->db->group_start();
            $this->db->like('cron_name', $params['search']);
            $this->db->or_like('response_message', $params['search']);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get all unique cron names
     */
    public function get_cron_names() {
        $this->db->select('cron_name');
        $this->db->distinct();
        $this->db->from($this->tb_cron_logs);
        $this->db->order_by('cron_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get statistics for each cron
     */
    public function get_cron_statistics() {
        $this->db->select('
            cron_name,
            COUNT(*) as total_runs,
            SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
            MAX(executed_at) as last_run,
            AVG(execution_time) as avg_execution_time
        ');
        $this->db->from($this->tb_cron_logs);
        $this->db->group_by('cron_name');
        $this->db->order_by('last_run', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get log by ID
     */
    public function get_log_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->tb_cron_logs);
        return $query->row();
    }
    
    /**
     * Delete old logs
     */
    public function delete_old_logs($days = 30) {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('executed_at <', $cutoff_date);
        $this->db->delete($this->tb_cron_logs);
        return $this->db->affected_rows();
    }
    
    /**
     * Delete log by ID
     */
    public function delete_log($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tb_cron_logs);
        return $this->db->affected_rows();
    }
    
    /**
     * Get setting
     */
    public function get_setting($key, $default = null) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->tb_cron_settings);
        
        if ($query->num_rows() > 0) {
            return $query->row()->setting_value;
        }
        
        return $default;
    }
    
    /**
     * Update setting
     */
    public function update_setting($key, $value) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->tb_cron_settings);
        
        if ($query->num_rows() > 0) {
            // Update existing
            $this->db->where('setting_key', $key);
            $this->db->update($this->tb_cron_settings, array(
                'setting_value' => $value,
                'changed' => date('Y-m-d H:i:s')
            ));
        } else {
            // Insert new
            $this->db->insert($this->tb_cron_settings, array(
                'setting_key' => $key,
                'setting_value' => $value,
                'created' => date('Y-m-d H:i:s')
            ));
        }
        
        return true;
    }
    
    /**
     * Get all settings
     */
    public function get_all_settings() {
        $query = $this->db->get($this->tb_cron_settings);
        $settings = array();
        
        foreach ($query->result() as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        
        return $settings;
    }
}
