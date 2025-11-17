<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Monitor Hook
 * 
 * Automatically detects cron endpoints and initializes logging
 * This ensures all current and future cron jobs are automatically logged
 */
class Cron_monitor {
    
    private $CI;
    private $cron_patterns = array(
        '/cron/',
        '_cron/',
        'cron_',
    );
    
    public function __construct() {
        $this->CI =& get_instance();
    }
    
    /**
     * Check if current request is a cron and initialize logging
     * This method should be called on post_controller_constructor hook
     */
    public function monitor() {
        try {
            $uri = $this->CI->uri->uri_string();
            $controller = $this->CI->router->fetch_class();
            $method = $this->CI->router->fetch_method();
            
            // Check if this is a cron endpoint
            if ($this->is_cron_endpoint($uri, $controller, $method)) {
                // Log cron detection
                log_message('info', 'Cron detected: /' . $uri . ' (Controller: ' . $controller . ', Method: ' . $method . ')');
                
                // Load the cron logger
                $this->CI->load->library('cron_logger');
                
                // Determine the cron name
                $cron_name = $this->get_cron_name($uri, $controller, $method);
                
                // Start logging
                $this->CI->cron_logger->start($cron_name);
                
                // Set a shutdown function to auto-log if the cron doesn't explicitly log
                register_shutdown_function(array($this, 'auto_log_on_shutdown'));
            }
        } catch (Exception $e) {
            // Log error to file for debugging
            log_message('error', 'Cron_monitor hook failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        }
    }
    
    /**
     * Determine if the current request is a cron endpoint
     * 
     * @param string $uri Current URI
     * @param string $controller Controller name
     * @param string $method Method name
     * @return bool
     */
    private function is_cron_endpoint($uri, $controller, $method) {
        // Exclude the cron_logs admin interface itself
        if ($controller === 'cron_logs' || stripos($uri, 'cron_logs') !== false) {
            return false;
        }
        
        // Check URI patterns
        foreach ($this->cron_patterns as $pattern) {
            if (stripos($uri, $pattern) !== false) {
                return true;
            }
            if (stripos($controller, $pattern) !== false) {
                return true;
            }
            if (stripos($method, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get a friendly name for the cron job
     * 
     * @param string $uri Current URI
     * @param string $controller Controller name
     * @param string $method Method name
     * @return string
     */
    private function get_cron_name($uri, $controller, $method) {
        // Use the full URI as the primary identifier
        return '/' . $uri;
    }
    
    /**
     * Auto-log on script shutdown if no explicit logging was done
     * This is a fallback to ensure all cron executions are logged
     */
    public function auto_log_on_shutdown() {
        // Check if cron_logger is loaded and was started
        if (isset($this->CI->cron_logger) && isset($this->CI->cron_logger->start_time)) {
            // Only auto-log if no log entry was created yet
            // This is a fallback mechanism
            $error = error_get_last();
            if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
                // Fatal error occurred
                $message = $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'];
                $this->CI->cron_logger->log_failure($message, 500);
            }
        }
    }
}
