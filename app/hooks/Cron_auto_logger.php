<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Auto Logger Hook
 * Automatically detects and logs all cron job executions
 * 
 * This hook intercepts controller method calls that look like cron jobs
 * and automatically logs them without requiring manual code changes
 */
class Cron_auto_logger {
    
    protected $CI;
    protected $cron_patterns = array(
        '/^cron/',           // Starts with "cron"
        '/cron$/',           // Ends with "cron"
        '/^run$/',           // Method named "run" (common for cron)
        '/_cron/',           // Contains "_cron"
    );
    
    public function __construct() {
        $this->CI =& get_instance();
    }
    
    /**
     * Post controller constructor hook
     * Check if the current request is to a cron endpoint
     */
    public function check_cron_execution() {
        $controller = $this->CI->router->fetch_class();
        $method = $this->CI->router->fetch_method();
        
        // Check if this looks like a cron endpoint
        if ($this->is_cron_endpoint($controller, $method)) {
            // Load cron logger if not already loaded
            if (!isset($this->CI->cron_logger)) {
                $this->CI->load->library('cron_logger');
            }
            
            // Store start time in case the controller doesn't log manually
            $this->CI->_cron_auto_log_start = microtime(true);
            $this->CI->_cron_auto_log_name = $controller . '/' . $method;
        }
    }
    
    /**
     * Check if controller/method combination looks like a cron endpoint
     */
    private function is_cron_endpoint($controller, $method) {
        // Check controller name
        foreach ($this->cron_patterns as $pattern) {
            if (preg_match($pattern, $controller)) {
                return true;
            }
        }
        
        // Check method name
        foreach ($this->cron_patterns as $pattern) {
            if (preg_match($pattern, $method)) {
                return true;
            }
        }
        
        // Check known cron controllers
        $known_cron_controllers = array('email_cron', 'whatsapp_cron', 'imap_cron');
        if (in_array($controller, $known_cron_controllers)) {
            return true;
        }
        
        return false;
    }
}
