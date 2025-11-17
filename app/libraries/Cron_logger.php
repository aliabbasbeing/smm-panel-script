<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logger Library
 * Automatically logs all cron job executions
 * 
 * Usage:
 *   $this->load->library('cron_logger');
 *   $log_id = $this->cron_logger->start('cron_name');
 *   // ... execute cron logic ...
 *   $this->cron_logger->end($log_id, 'success', 200, 'Optional message');
 */
class Cron_logger {
    
    protected $CI;
    protected $table = 'cron_logs';
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    /**
     * Start logging a cron execution
     * 
     * @param string $cron_name Name or URL of the cron job
     * @return int Log ID
     */
    public function start($cron_name) {
        $data = array(
            'cron_name' => $cron_name,
            'executed_at' => date('Y-m-d H:i:s'),
            'status' => 'running',
            'created' => date('Y-m-d H:i:s')
        );
        
        $this->CI->db->insert($this->table, $data);
        return $this->CI->db->insert_id();
    }
    
    /**
     * End logging a cron execution
     * 
     * @param int $log_id Log ID from start()
     * @param string $status 'success' or 'failed'
     * @param int $response_code HTTP status code or custom code
     * @param string $response_message Optional message
     * @param float $execution_time Time taken in seconds
     */
    public function end($log_id, $status = 'success', $response_code = 200, $response_message = null, $execution_time = null) {
        if (!$log_id) {
            return false;
        }
        
        $data = array(
            'status' => $status,
            'response_code' => $response_code,
            'response_message' => $response_message,
            'execution_time' => $execution_time
        );
        
        $this->CI->db->where('id', $log_id);
        $this->CI->db->update($this->table, $data);
        
        // Send notification if failed and notifications enabled
        if ($status === 'failed') {
            $this->send_failure_notification($log_id);
        }
        
        return true;
    }
    
    /**
     * Log a complete cron execution in one call
     * 
     * @param string $cron_name Name or URL of the cron job
     * @param string $status 'success' or 'failed'
     * @param int $response_code HTTP status code or custom code
     * @param string $response_message Optional message
     * @param float $execution_time Time taken in seconds
     * @return int Log ID
     */
    public function log($cron_name, $status = 'success', $response_code = 200, $response_message = null, $execution_time = null) {
        $data = array(
            'cron_name' => $cron_name,
            'executed_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'response_code' => $response_code,
            'response_message' => $response_message,
            'execution_time' => $execution_time,
            'created' => date('Y-m-d H:i:s')
        );
        
        $this->CI->db->insert($this->table, $data);
        $log_id = $this->CI->db->insert_id();
        
        // Send notification if failed and notifications enabled
        if ($status === 'failed') {
            $this->send_failure_notification($log_id);
        }
        
        return $log_id;
    }
    
    /**
     * Execute and log a cron function
     * 
     * @param string $cron_name Name or URL of the cron job
     * @param callable $callback Function to execute
     * @return mixed Return value from callback
     */
    public function execute($cron_name, $callback) {
        $start_time = microtime(true);
        $log_id = $this->start($cron_name);
        
        try {
            $result = call_user_func($callback);
            $execution_time = microtime(true) - $start_time;
            
            // Determine status based on result
            $status = 'success';
            $response_code = 200;
            $response_message = null;
            
            if (is_array($result) && isset($result['status'])) {
                $status = ($result['status'] === 'error' || $result['status'] === 'failed') ? 'failed' : 'success';
                $response_code = isset($result['code']) ? $result['code'] : 200;
                $response_message = isset($result['message']) ? $result['message'] : null;
            }
            
            $this->end($log_id, $status, $response_code, $response_message, $execution_time);
            
            return $result;
        } catch (Exception $e) {
            $execution_time = microtime(true) - $start_time;
            $this->end($log_id, 'failed', 500, $e->getMessage(), $execution_time);
            throw $e;
        }
    }
    
    /**
     * Get recent logs
     * 
     * @param int $limit Number of logs to retrieve
     * @return array
     */
    public function get_recent_logs($limit = 50) {
        $this->CI->db->order_by('executed_at', 'DESC');
        $this->CI->db->limit($limit);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }
    
    /**
     * Get logs by cron name
     * 
     * @param string $cron_name
     * @param int $limit
     * @return array
     */
    public function get_logs_by_name($cron_name, $limit = 50) {
        $this->CI->db->where('cron_name', $cron_name);
        $this->CI->db->order_by('executed_at', 'DESC');
        $this->CI->db->limit($limit);
        $query = $this->CI->db->get($this->table);
        return $query->result();
    }
    
    /**
     * Get last execution status for a cron
     * 
     * @param string $cron_name
     * @return object|null
     */
    public function get_last_status($cron_name) {
        $this->CI->db->where('cron_name', $cron_name);
        $this->CI->db->order_by('executed_at', 'DESC');
        $this->CI->db->limit(1);
        $query = $this->CI->db->get($this->table);
        return $query->row();
    }
    
    /**
     * Clean old logs based on retention settings
     */
    public function cleanup_old_logs() {
        // Get retention days from settings
        $retention_days = $this->get_setting('log_retention_days', 30);
        
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));
        
        $this->CI->db->where('executed_at <', $cutoff_date);
        $this->CI->db->delete($this->table);
        
        return $this->CI->db->affected_rows();
    }
    
    /**
     * Send failure notification email
     * 
     * @param int $log_id
     */
    private function send_failure_notification($log_id) {
        // Check if notifications are enabled
        if ($this->get_setting('enable_email_notifications', 0) != 1) {
            return false;
        }
        
        $notification_email = $this->get_setting('notification_email', '');
        if (empty($notification_email)) {
            return false;
        }
        
        // Get log details
        $this->CI->db->where('id', $log_id);
        $log = $this->CI->db->get($this->table)->row();
        
        if (!$log) {
            return false;
        }
        
        // Send email notification
        $this->CI->load->library('email');
        
        $subject = 'Cron Job Failed: ' . $log->cron_name;
        $message = "A cron job has failed:\n\n";
        $message .= "Cron Name: {$log->cron_name}\n";
        $message .= "Executed At: {$log->executed_at}\n";
        $message .= "Response Code: {$log->response_code}\n";
        $message .= "Error Message: {$log->response_message}\n";
        $message .= "Execution Time: {$log->execution_time}s\n";
        
        $this->CI->email->from(get_option('email_from', 'noreply@example.com'), get_option('website_name', 'SMM Panel'));
        $this->CI->email->to($notification_email);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);
        
        return $this->CI->email->send();
    }
    
    /**
     * Get setting value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function get_setting($key, $default = null) {
        $this->CI->db->where('setting_key', $key);
        $query = $this->CI->db->get('cron_settings');
        
        if ($query->num_rows() > 0) {
            return $query->row()->setting_value;
        }
        
        return $default;
    }
}
