<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logger Library
 * 
 * This library provides centralized logging for all cron jobs.
 * It automatically tracks execution time, status, and responses.
 * 
 * Usage:
 *   $this->load->library('cron_logger');
 *   $log_id = $this->cron_logger->start('cron/order');
 *   // ... cron execution ...
 *   $this->cron_logger->end($log_id, 'Success', 200, 'Completed successfully');
 * 
 * @package    SMM Panel
 * @subpackage Libraries
 */
class Cron_logger {
    
    protected $CI;
    protected $table = 'cron_logs';
    protected $active_logs = array();
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    /**
     * Start logging a cron execution
     * 
     * @param string $cron_name The cron identifier (e.g., 'cron/order', '/cron/status')
     * @return int|false The log ID or false on failure
     */
    public function start($cron_name) {
        try {
            // Normalize cron name (remove leading slash if present)
            $cron_name = ltrim($cron_name, '/');
            
            $data = array(
                'cron_name' => $cron_name,
                'executed_at' => NOW,
                'status' => 'Success', // Default, will be updated on end
                'created' => NOW
            );
            
            $this->CI->db->insert($this->table, $data);
            $log_id = $this->CI->db->insert_id();
            
            // Store start time for this log
            $this->active_logs[$log_id] = microtime(true);
            
            return $log_id;
        } catch (Exception $e) {
            // Log error but don't break cron execution
            log_message('error', 'Cron_logger::start failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * End logging a cron execution
     * 
     * @param int $log_id The log ID from start()
     * @param string $status 'Success' or 'Failed'
     * @param int $response_code HTTP response code or custom code
     * @param string $response_message Optional message or output
     * @return bool
     */
    public function end($log_id, $status = 'Success', $response_code = 200, $response_message = null) {
        try {
            if (!$log_id) {
                return false;
            }
            
            // Calculate execution time
            $execution_time = null;
            if (isset($this->active_logs[$log_id])) {
                $execution_time = microtime(true) - $this->active_logs[$log_id];
                unset($this->active_logs[$log_id]);
            }
            
            // Ensure status is valid
            $status = ($status === 'Failed') ? 'Failed' : 'Success';
            
            $data = array(
                'status' => $status,
                'response_code' => $response_code,
                'response_message' => $response_message,
                'execution_time' => $execution_time
            );
            
            $this->CI->db->where('id', $log_id);
            $this->CI->db->update($this->table, $data);
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::end failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log a complete cron execution in one call
     * 
     * @param string $cron_name The cron identifier
     * @param string $status 'Success' or 'Failed'
     * @param int $response_code HTTP response code
     * @param string $response_message Optional message
     * @param float $execution_time Execution time in seconds
     * @return bool
     */
    public function log($cron_name, $status = 'Success', $response_code = 200, $response_message = null, $execution_time = null) {
        try {
            // Normalize cron name
            $cron_name = ltrim($cron_name, '/');
            
            // Ensure status is valid
            $status = ($status === 'Failed') ? 'Failed' : 'Success';
            
            $data = array(
                'cron_name' => $cron_name,
                'executed_at' => NOW,
                'status' => $status,
                'response_code' => $response_code,
                'response_message' => $response_message,
                'execution_time' => $execution_time,
                'created' => NOW
            );
            
            $this->CI->db->insert($this->table, $data);
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::log failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the last execution log for a specific cron
     * 
     * @param string $cron_name The cron identifier
     * @return object|null
     */
    public function get_last_log($cron_name) {
        try {
            $cron_name = ltrim($cron_name, '/');
            
            $this->CI->db->where('cron_name', $cron_name);
            $this->CI->db->order_by('executed_at', 'DESC');
            $this->CI->db->limit(1);
            $query = $this->CI->db->get($this->table);
            
            return $query->row();
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::get_last_log failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Clean up old logs (optional maintenance)
     * 
     * @param int $days Keep logs for this many days (default 30)
     * @return int Number of deleted records
     */
    public function cleanup($days = 30) {
        try {
            $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            
            $this->CI->db->where('executed_at <', $cutoff_date);
            $this->CI->db->delete($this->table);
            
            return $this->CI->db->affected_rows();
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::cleanup failed: ' . $e->getMessage());
            return 0;
        }
    }
}
