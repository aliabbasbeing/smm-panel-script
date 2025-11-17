<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Cron Controller
 * 
 * All cron controllers should extend this class to get automatic logging.
 * This provides a wrapper that logs all cron executions automatically.
 */
class Base_cron extends CI_Controller {
    
    protected $cron_logger;
    protected $cron_name;
    
    public function __construct() {
        parent::__construct();
        $this->load->library('cron_logger');
    }
    
    /**
     * Execute a cron with automatic logging
     * 
     * @param string $cron_name The name/URL of the cron
     * @param callable $callback The function to execute
     * @param bool $echo_output Whether to echo output (default true)
     */
    protected function execute_cron($cron_name, $callback, $echo_output = true) {
        $this->cron_name = $cron_name;
        $this->cron_logger->start($cron_name);
        
        try {
            // Capture output
            if ($echo_output) {
                ob_start();
            }
            
            // Execute the callback
            $result = $callback();
            
            // Get output
            $output = '';
            if ($echo_output) {
                $output = ob_get_clean();
                echo $output;
            }
            
            // Determine if successful
            $success = true;
            $message = $output;
            
            // Check if result indicates failure
            if (is_array($result) && isset($result['status'])) {
                if ($result['status'] === 'failed' || $result['status'] === 'error') {
                    $success = false;
                    $message = $result['message'] ?? $output;
                } elseif ($result['status'] === 'rate_limited') {
                    $this->cron_logger->rate_limit($result['message'] ?? 'Rate limited');
                    return $result;
                }
            }
            
            // Log the result
            if ($success) {
                $this->cron_logger->end($message);
            } else {
                $this->cron_logger->fail($message);
            }
            
            return $result;
            
        } catch (Exception $e) {
            if ($echo_output && ob_get_level() > 0) {
                ob_end_clean();
            }
            
            $error_message = $e->getMessage();
            $this->cron_logger->fail($error_message);
            
            if ($echo_output) {
                echo "Error: " . $error_message;
            }
            
            throw $e;
        }
    }
    
    /**
     * Quick start logging (for manual control)
     */
    protected function start_logging($cron_name) {
        $this->cron_name = $cron_name;
        return $this->cron_logger->start($cron_name);
    }
    
    /**
     * Quick end logging with success
     */
    protected function end_logging($message = null, $response_code = 200) {
        $this->cron_logger->end($message, $response_code);
    }
    
    /**
     * Quick end logging with failure
     */
    protected function fail_logging($error_message, $response_code = 500) {
        $this->cron_logger->fail($error_message, $response_code);
    }
    
    /**
     * Quick rate limit logging
     */
    protected function rate_limit_logging($message) {
        $this->cron_logger->rate_limit($message);
    }
}
