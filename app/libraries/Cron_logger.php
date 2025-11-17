<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logger Library
 * 
 * Centralized logging system for all cron jobs
 * Automatically captures execution details and stores them in the database
 */
class Cron_logger {
    
    private $CI;
    private $start_time;
    private $cron_name;
    private $db_table = 'cron_logs';
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    /**
     * Start logging a cron execution
     * 
     * @param string $cron_name Name or URL of the cron job
     * @return void
     */
    public function start($cron_name) {
        $this->cron_name = $cron_name;
        $this->start_time = microtime(true);
    }
    
    /**
     * Log a successful cron execution
     * 
     * @param string $message Optional success message
     * @param int $response_code HTTP response code (default 200)
     * @return void
     */
    public function log_success($message = null, $response_code = 200) {
        $this->log('success', $response_code, $message);
    }
    
    /**
     * Log a failed cron execution
     * 
     * @param string $message Error message
     * @param int $response_code HTTP response code (default 500)
     * @return void
     */
    public function log_failure($message, $response_code = 500) {
        $this->log('failed', $response_code, $message);
        $this->send_failure_notification($message);
    }
    
    /**
     * Log a rate-limited cron execution
     * 
     * @param string $message Rate limit message
     * @param int $response_code HTTP response code (default 429)
     * @return void
     */
    public function log_rate_limited($message, $response_code = 429) {
        $this->log('rate_limited', $response_code, $message);
    }
    
    /**
     * Log an info status (e.g., no items to process)
     * 
     * @param string $message Info message
     * @param int $response_code HTTP response code (default 200)
     * @return void
     */
    public function log_info($message, $response_code = 200) {
        $this->log('info', $response_code, $message);
    }
    
    /**
     * Internal logging method
     * 
     * @param string $status Status of execution
     * @param int $response_code HTTP response code
     * @param string|null $message Optional message
     * @return void
     */
    private function log($status, $response_code, $message = null) {
        $execution_time = microtime(true) - $this->start_time;
        
        $data = array(
            'cron_name' => $this->cron_name,
            'executed_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'response_code' => $response_code,
            'response_message' => $message,
            'execution_time' => round($execution_time, 4),
            'created' => date('Y-m-d H:i:s')
        );
        
        try {
            $this->CI->db->insert($this->db_table, $data);
        } catch (Exception $e) {
            // Silently fail to avoid breaking cron execution
            log_message('error', 'Cron logger failed: ' . $e->getMessage());
        }
        
        // Clean up old logs based on retention setting
        $this->cleanup_old_logs();
    }
    
    /**
     * Send email notification for failed cron
     * 
     * @param string $message Error message
     * @return void
     */
    private function send_failure_notification($message) {
        try {
            $enable_notifications = get_option('cron_enable_notifications', '0');
            $notification_email = get_option('cron_notification_email', '');
            
            if ($enable_notifications != '1' || empty($notification_email)) {
                return;
            }
            
            $this->CI->load->library('email');
            
            $subject = 'Cron Job Failed: ' . $this->cron_name;
            $body = "A cron job has failed:\n\n";
            $body .= "Cron Name: " . $this->cron_name . "\n";
            $body .= "Time: " . date('Y-m-d H:i:s') . "\n";
            $body .= "Error: " . $message . "\n\n";
            $body .= "Please check the cron logs in the admin panel for more details.";
            
            $this->CI->email->from(get_option('email_from', 'noreply@example.com'), get_option('website_name', 'SMM Panel'));
            $this->CI->email->to($notification_email);
            $this->CI->email->subject($subject);
            $this->CI->email->message($body);
            $this->CI->email->send();
        } catch (Exception $e) {
            // Silently fail
            log_message('error', 'Cron notification failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Clean up old logs based on retention setting
     * 
     * @return void
     */
    private function cleanup_old_logs() {
        // Only run cleanup occasionally (5% chance)
        if (rand(1, 100) > 5) {
            return;
        }
        
        try {
            $retention_days = (int)get_option('cron_log_retention_days', '30');
            if ($retention_days > 0) {
                $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));
                $this->CI->db->where('executed_at <', $cutoff_date);
                $this->CI->db->delete($this->db_table);
            }
        } catch (Exception $e) {
            // Silently fail
            log_message('error', 'Cron log cleanup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the last execution details for a specific cron
     * 
     * @param string $cron_name Name of the cron job
     * @return object|null
     */
    public function get_last_execution($cron_name) {
        try {
            return $this->CI->db
                ->where('cron_name', $cron_name)
                ->order_by('executed_at', 'DESC')
                ->limit(1)
                ->get($this->db_table)
                ->row();
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get execution history for a specific cron
     * 
     * @param string $cron_name Name of the cron job
     * @param int $limit Number of records to return
     * @return array
     */
    public function get_execution_history($cron_name, $limit = 10) {
        try {
            return $this->CI->db
                ->where('cron_name', $cron_name)
                ->order_by('executed_at', 'DESC')
                ->limit($limit)
                ->get($this->db_table)
                ->result();
        } catch (Exception $e) {
            return array();
        }
    }
}
