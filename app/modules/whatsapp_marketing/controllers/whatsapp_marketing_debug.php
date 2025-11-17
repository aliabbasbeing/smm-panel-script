<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing_debug extends MX_Controller {
    
    public function __construct(){
        parent::__construct();
        
        // Check if user is admin
        if (!get_role("admin")) {
            show_error('Permission Denied!');
        }
    }
    
    public function error_log(){
        $log_file = APPPATH . 'logs/whatsapp_marketing_errors.log';
        
        $content = '';
        if (file_exists($log_file)) {
            $content = file_get_contents($log_file);
        }
        
        header('Content-Type: text/plain; charset=utf-8');
        if (empty($content)) {
            echo "No errors logged yet.\n\n";
            echo "Log file path: " . $log_file . "\n";
            echo "File exists: " . (file_exists($log_file) ? 'Yes' : 'No') . "\n";
        } else {
            echo $content;
        }
    }
    
    public function clear_log(){
        $log_file = APPPATH . 'logs/whatsapp_marketing_errors.log';
        
        if (file_exists($log_file)) {
            @unlink($log_file);
            echo "Error log cleared!<br><br>";
        } else {
            echo "No log file to clear.<br><br>";
        }
        
        echo '<a href="' . base_url('whatsapp_marketing_debug/error_log') . '">View Error Log</a> | ';
        echo '<a href="' . base_url('whatsapp_marketing') . '">Back to WhatsApp Marketing</a>';
    }
    
    public function phpinfo_check(){
        if (!get_role("admin")) {
            show_error('Permission Denied!');
        }
        
        echo "<h2>PHP Configuration</h2>";
        echo "<p>PHP Version: " . phpversion() . "</p>";
        echo "<p>Display Errors: " . ini_get('display_errors') . "</p>";
        echo "<p>Error Reporting: " . error_reporting() . "</p>";
        echo "<p>Log Errors: " . ini_get('log_errors') . "</p>";
        echo "<p>Error Log: " . ini_get('error_log') . "</p>";
        echo "<br><hr><br>";
        
        echo "<h2>Database Check</h2>";
        $this->load->database();
        
        $tables = array(
            'whatsapp_campaigns',
            'whatsapp_templates', 
            'whatsapp_api_configs',
            'whatsapp_recipients',
            'whatsapp_logs',
            'whatsapp_settings'
        );
        
        foreach ($tables as $table) {
            $exists = $this->db->table_exists($table);
            echo "<p><strong>$table:</strong> " . ($exists ? '✓ Exists' : '✗ Missing') . "</p>";
            
            if ($exists) {
                $count = $this->db->count_all($table);
                echo "<p style='margin-left: 20px;'>Row count: $count</p>";
            }
        }
        
        echo "<br><hr><br>";
        echo '<a href="' . base_url('whatsapp_marketing_debug/error_log') . '">View Error Log</a> | ';
        echo '<a href="' . base_url('whatsapp_marketing') . '">Back to WhatsApp Marketing</a>';
    }
}
