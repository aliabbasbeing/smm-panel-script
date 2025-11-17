<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logs Model
 * 
 * Handles database operations for cron logs
 */
class Cron_logs_model extends MY_Model {
    
    public $tb_cron_logs = 'cron_logs';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get paginated cron logs with filters
     * 
     * @param array $params Filter parameters
     * @return object Query result with pagination
     */
    public function get_logs($params = []) {
        $this->db->from($this->tb_cron_logs);
        
        // Apply filters
        if (!empty($params['cron_name'])) {
            $this->db->like('cron_name', $params['cron_name']);
        }
        
        if (!empty($params['status'])) {
            $this->db->where('status', $params['status']);
        }
        
        if (!empty($params['date_from'])) {
            $this->db->where('executed_at >=', $params['date_from'] . ' 00:00:00');
        }
        
        if (!empty($params['date_to'])) {
            $this->db->where('executed_at <=', $params['date_to'] . ' 23:59:59');
        }
        
        // Order by most recent first
        $this->db->order_by('executed_at', 'DESC');
        
        // Pagination
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $per_page = isset($params['per_page']) ? (int)$params['per_page'] : 50;
        $offset = ($page - 1) * $per_page;
        
        // Get total count
        $total = $this->db->count_all_results('', false);
        
        // Get paginated results
        $this->db->limit($per_page, $offset);
        $results = $this->db->get()->result();
        
        return (object)[
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'pages' => ceil($total / $per_page)
        ];
    }
    
    /**
     * Get all unique cron names
     * 
     * @return array List of cron names
     */
    public function get_cron_names() {
        $this->db->select('DISTINCT cron_name');
        $this->db->from($this->tb_cron_logs);
        $this->db->order_by('cron_name', 'ASC');
        $result = $this->db->get()->result();
        
        return array_map(function($row) {
            return $row->cron_name;
        }, $result);
    }
    
    /**
     * Get last run information for all crons
     * 
     * @return array Array of cron last runs
     */
    public function get_all_last_runs() {
        $sql = "
            SELECT cl.*
            FROM {$this->tb_cron_logs} cl
            INNER JOIN (
                SELECT cron_name, MAX(executed_at) as max_executed
                FROM {$this->tb_cron_logs}
                GROUP BY cron_name
            ) latest ON cl.cron_name = latest.cron_name 
                AND cl.executed_at = latest.max_executed
            ORDER BY cl.cron_name ASC
        ";
        
        return $this->db->query($sql)->result();
    }
    
    /**
     * Get statistics for all crons
     * 
     * @param int $days Number of days to look back
     * @return array Statistics per cron
     */
    public function get_all_stats($days = 7) {
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $sql = "
            SELECT 
                cron_name,
                COUNT(*) as total_runs,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited_count,
                AVG(execution_time) as avg_execution_time,
                MAX(executed_at) as last_run
            FROM {$this->tb_cron_logs}
            WHERE executed_at >= ?
            GROUP BY cron_name
            ORDER BY cron_name ASC
        ";
        
        return $this->db->query($sql, [$since])->result();
    }
    
    /**
     * Get dashboard summary
     * 
     * @param int $days Number of days to look back
     * @return object Summary statistics
     */
    public function get_dashboard_summary($days = 7) {
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $sql = "
            SELECT 
                COUNT(*) as total_runs,
                COUNT(DISTINCT cron_name) as total_crons,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as total_success,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as total_failed,
                SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as total_rate_limited,
                AVG(execution_time) as avg_execution_time
            FROM {$this->tb_cron_logs}
            WHERE executed_at >= ?
        ";
        
        return $this->db->query($sql, [$since])->row();
    }
    
    /**
     * Delete old logs
     * 
     * @param int $days Keep logs newer than this many days
     * @return int Number of deleted records
     */
    public function cleanup_old_logs($days = 30) {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('executed_at <', $cutoff);
        $this->db->delete($this->tb_cron_logs);
        return $this->db->affected_rows();
    }
    
    /**
     * Get a single log entry
     * 
     * @param int $id Log ID
     * @return object|null Log entry
     */
    public function get_log($id) {
        return $this->get('*', $this->tb_cron_logs, ['id' => $id]);
    }
}
