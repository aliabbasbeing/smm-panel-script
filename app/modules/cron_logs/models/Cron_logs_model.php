<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_logs_model extends MY_Model {
    
    private $tb_cron_logs = 'cron_logs';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get all cron logs with pagination and filters
     */
    public function get_logs($params = array()) {
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
        
        // Count total before pagination
        $total = $this->db->count_all_results('', false);
        
        // Apply pagination
        $limit = isset($params['limit']) ? (int)$params['limit'] : 20;
        $offset = isset($params['offset']) ? (int)$params['offset'] : 0;
        
        $this->db->order_by('executed_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        $logs = $this->db->get()->result();
        
        return array(
            'logs' => $logs,
            'total' => $total
        );
    }
    
    /**
     * Get summary of all unique cron jobs
     */
    public function get_cron_summary() {
        try {
            $query = "
                SELECT 
                    cron_name,
                    MAX(executed_at) as last_run,
                    COUNT(*) as total_executions,
                    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited_count,
                    AVG(execution_time) as avg_execution_time,
                    (
                        SELECT status 
                        FROM {$this->tb_cron_logs} l2 
                        WHERE l2.cron_name = l1.cron_name 
                        ORDER BY executed_at DESC 
                        LIMIT 1
                    ) as last_status
                FROM {$this->tb_cron_logs} l1
                GROUP BY cron_name
                ORDER BY last_run DESC
            ";
            
            return $this->db->query($query)->result();
        } catch (Exception $e) {
            log_message('error', 'Cron_logs_model::get_cron_summary - ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get specific log by ID
     */
    public function get_log($id) {
        return $this->db->where('id', $id)->get($this->tb_cron_logs)->row();
    }
    
    /**
     * Delete old logs
     */
    public function delete_old_logs($days = 30) {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->db->where('executed_at <', $cutoff_date)->delete($this->tb_cron_logs);
    }
    
    /**
     * Delete all logs
     */
    public function delete_all_logs() {
        return $this->db->truncate($this->tb_cron_logs);
    }
    
    /**
     * Get statistics for dashboard
     */
    public function get_statistics() {
        try {
            // Last 24 hours stats
            $yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));
            
            $stats = array(
                'total_executions_24h' => $this->db
                    ->where('executed_at >=', $yesterday)
                    ->count_all_results($this->tb_cron_logs),
                
                'successful_24h' => $this->db
                    ->where('executed_at >=', $yesterday)
                    ->where('status', 'success')
                    ->count_all_results($this->tb_cron_logs),
                
                'failed_24h' => $this->db
                    ->where('executed_at >=', $yesterday)
                    ->where('status', 'failed')
                    ->count_all_results($this->tb_cron_logs),
                
                'avg_execution_time_24h' => $this->db
                    ->select('AVG(execution_time) as avg_time')
                    ->where('executed_at >=', $yesterday)
                    ->get($this->tb_cron_logs)
                    ->row()->avg_time ?? 0,
                
                'total_crons' => $this->db
                    ->select('COUNT(DISTINCT cron_name) as count')
                    ->get($this->tb_cron_logs)
                    ->row()->count ?? 0
            );
            
            return $stats;
        } catch (Exception $e) {
            log_message('error', 'Cron_logs_model::get_statistics - ' . $e->getMessage());
            return array(
                'total_executions_24h' => 0,
                'successful_24h' => 0,
                'failed_24h' => 0,
                'avg_execution_time_24h' => 0,
                'total_crons' => 0
            );
        }
    }
}
