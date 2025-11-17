<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logger Library
 * 
 * Centralized logging system for all cron job executions.
 * Automatically logs start time, end time, status, and response details.
 */
class Cron_logger {
    
    protected $CI;
    private $start_time;
    private $cron_name;
    private $log_id;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    /**
     * Start logging a cron execution
     * 
     * @param string $cron_name The name/URL of the cron job
     * @return int The log ID for this execution
     */
    public function start($cron_name) {
        $this->cron_name = $cron_name;
        $this->start_time = microtime(true);
        
        // Insert initial log entry
        $data = [
            'cron_name' => $cron_name,
            'executed_at' => date('Y-m-d H:i:s'),
            'status' => 'success', // Default to success, will update if failed
        ];
        
        $this->CI->db->insert('cron_logs', $data);
        $this->log_id = $this->CI->db->insert_id();
        
        return $this->log_id;
    }
    
    /**
     * End logging with success status
     * 
     * @param string $message Optional success message
     * @param int $response_code Optional HTTP response code
     */
    public function end($message = null, $response_code = 200) {
        if (!$this->log_id) {
            return;
        }
        
        $execution_time = microtime(true) - $this->start_time;
        
        $data = [
            'status' => 'success',
            'response_code' => $response_code,
            'response_message' => $message,
            'execution_time' => round($execution_time, 3)
        ];
        
        $this->CI->db->where('id', $this->log_id);
        $this->CI->db->update('cron_logs', $data);
        
        $this->reset();
    }
    
    /**
     * End logging with failure status
     * 
     * @param string $error_message Error message
     * @param int $response_code Optional HTTP response code
     */
    public function fail($error_message, $response_code = 500) {
        if (!$this->log_id) {
            return;
        }
        
        $execution_time = microtime(true) - $this->start_time;
        
        $data = [
            'status' => 'failed',
            'response_code' => $response_code,
            'response_message' => $error_message,
            'execution_time' => round($execution_time, 3)
        ];
        
        $this->CI->db->where('id', $this->log_id);
        $this->CI->db->update('cron_logs', $data);
        
        // Optional: Send notification to admin
        $this->send_failure_notification($error_message);
        
        $this->reset();
    }
    
    /**
     * Log rate limiting
     * 
     * @param string $message Rate limit message
     */
    public function rate_limit($message) {
        if (!$this->log_id) {
            return;
        }
        
        $execution_time = microtime(true) - $this->start_time;
        
        $data = [
            'status' => 'rate_limited',
            'response_code' => 429,
            'response_message' => $message,
            'execution_time' => round($execution_time, 3)
        ];
        
        $this->CI->db->where('id', $this->log_id);
        $this->CI->db->update('cron_logs', $data);
        
        $this->reset();
    }
    
    /**
     * Quick log for simple cron executions
     * Wraps start and end in one call
     * 
     * @param string $cron_name The name/URL of the cron job
     * @param callable $callback Function to execute
     * @return mixed Result of the callback
     */
    public function log($cron_name, $callback) {
        $this->start($cron_name);
        
        try {
            $result = $callback();
            $this->end('Execution completed successfully');
            return $result;
        } catch (Exception $e) {
            $this->fail($e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get the last execution status for a cron
     * 
     * @param string $cron_name The name/URL of the cron job
     * @return object|null Last log entry
     */
    public function get_last_run($cron_name) {
        $this->CI->db->where('cron_name', $cron_name);
        $this->CI->db->order_by('executed_at', 'DESC');
        $this->CI->db->limit(1);
        return $this->CI->db->get('cron_logs')->row();
    }
    
    /**
     * Get statistics for a cron job
     * 
     * @param string $cron_name The name/URL of the cron job
     * @param int $days Number of days to look back (default 7)
     * @return object Statistics
     */
    public function get_stats($cron_name, $days = 7) {
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $this->CI->db->select('
            COUNT(*) as total_runs,
            SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN status = "rate_limited" THEN 1 ELSE 0 END) as rate_limited_count,
            AVG(execution_time) as avg_execution_time,
            MAX(executed_at) as last_run
        ');
        $this->CI->db->where('cron_name', $cron_name);
        $this->CI->db->where('executed_at >=', $since);
        return $this->CI->db->get('cron_logs')->row();
    }
    
    /**
     * Clean old logs
     * 
     * @param int $days Keep logs newer than this many days (default 30)
     * @return int Number of deleted records
     */
    public function cleanup($days = 30) {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->CI->db->where('executed_at <', $cutoff);
        $this->CI->db->delete('cron_logs');
        return $this->CI->db->affected_rows();
    }
    
    /**
     * Send failure notification to admin
     * 
     * @param string $error_message
     */
    private function send_failure_notification($error_message) {
        // Check if notifications are enabled
        $notify_enabled = get_option('cron_failure_notifications', 0);
        if (!$notify_enabled) {
            return;
        }
        
        // Get admin email
        $admin_email = get_option('admin_email', '');
        if (!$admin_email) {
            return;
        }
        
        // Send email notification
        $this->CI->load->library('email');
        
        $subject = 'Cron Job Failure Alert - ' . $this->cron_name;
        $message = "A cron job has failed:\n\n";
        $message .= "Cron: {$this->cron_name}\n";
        $message .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $message .= "Error: {$error_message}\n";
        
        $this->CI->email->from($admin_email, get_option('website_name', 'SMM Panel'));
        $this->CI->email->to($admin_email);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);
        
        @$this->CI->email->send();
    }
    
    /**
     * Reset internal state
     */
    private function reset() {
        $this->start_time = null;
        $this->cron_name = null;
        $this->log_id = null;
    }
}
