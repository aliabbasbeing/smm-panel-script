<?php
/**
 * Cron Logs Installation Script
 * 
 * This script handles the database installation for the cron logging system.
 * It should be run once to set up the cron_logs table.
 */

// Load CodeIgniter bootstrap
define('BASEPATH', true);
require_once(__DIR__ . '/../index.php');

// Get database instance
$CI =& get_instance();
$CI->load->database();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'install') {
        try {
            // SQL for creating the cron_logs table
            $sql = "
                CREATE TABLE IF NOT EXISTS `cron_logs` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `cron_name` varchar(255) NOT NULL COMMENT 'URL or identifier of the cron job',
                  `executed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the cron was executed',
                  `status` enum('success','failed','rate_limited') NOT NULL DEFAULT 'success' COMMENT 'Execution status',
                  `response_code` int(11) DEFAULT NULL COMMENT 'HTTP response code if applicable',
                  `response_message` text DEFAULT NULL COMMENT 'Output or error message',
                  `execution_time` decimal(10,3) DEFAULT NULL COMMENT 'Total execution time in seconds',
                  PRIMARY KEY (`id`),
                  KEY `idx_cron_name` (`cron_name`),
                  KEY `idx_executed_at` (`executed_at`),
                  KEY `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Logs for cron job executions';
            ";
            
            $CI->db->query($sql);
            
            // Add additional indices
            $index_sql = "
                ALTER TABLE `cron_logs` 
                ADD INDEX IF NOT EXISTS `idx_cron_status` (`cron_name`, `status`),
                ADD INDEX IF NOT EXISTS `idx_date_status` (`executed_at`, `status`);
            ";
            
            // Try to add indices (may fail if they already exist)
            @$CI->db->query($index_sql);
            
            echo json_encode([
                'success' => true,
                'message' => 'Database table created successfully! You can now use the cron logging system.'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create table: ' . $e->getMessage()
            ]);
        }
        
    } elseif ($action === 'verify') {
        try {
            // Check if table exists
            $query = $CI->db->query("SHOW TABLES LIKE 'cron_logs'");
            
            if ($query->num_rows() > 0) {
                // Check table structure
                $columns = $CI->db->query("SHOW COLUMNS FROM cron_logs")->result();
                $column_names = array_map(function($col) { return $col->Field; }, $columns);
                
                $required_columns = ['id', 'cron_name', 'executed_at', 'status', 'response_code', 'response_message', 'execution_time'];
                $missing_columns = array_diff($required_columns, $column_names);
                
                if (empty($missing_columns)) {
                    // Get row count
                    $count = $CI->db->count_all('cron_logs');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => "Installation verified! Table exists with all required columns. Current log entries: {$count}"
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Table exists but missing columns: ' . implode(', ', $missing_columns)
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Table does not exist. Please run the installation first.'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ]);
        }
    }
    
    exit;
}

// If accessed directly without AJAX, show error
header('HTTP/1.1 400 Bad Request');
echo json_encode([
    'success' => false,
    'message' => 'This script should only be called via the installation UI.'
]);
