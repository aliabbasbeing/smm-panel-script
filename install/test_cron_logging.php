<?php
/**
 * Test Script for Cron Logging System
 * 
 * This script demonstrates and tests the cron logging functionality.
 * Run this to verify the system is working correctly.
 */

// Load CodeIgniter
define('BASEPATH', true);
$_SERVER['REQUEST_METHOD'] = 'GET';

require_once(__DIR__ . '/../index.php');

$CI =& get_instance();
$CI->load->library('cron_logger');
$CI->load->database();

echo "<html><head><title>Cron Logging Test</title>";
echo "<style>
    body { font-family: Arial; max-width: 900px; margin: 20px auto; padding: 20px; }
    .test { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; }
    .success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; }
    .error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; }
    pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
</style></head><body>";

echo "<h1>🧪 Cron Logging System - Test Suite</h1>";

// Test 1: Database connection
echo "<div class='test'>";
echo "<h3>Test 1: Database Table Check</h3>";
try {
    $query = $CI->db->query("SHOW TABLES LIKE 'cron_logs'");
    if ($query->num_rows() > 0) {
        echo "<div class='success'>✓ Database table 'cron_logs' exists</div>";
        
        $count = $CI->db->count_all('cron_logs');
        echo "<p>Current log entries: <strong>{$count}</strong></p>";
    } else {
        echo "<div class='error'>✗ Database table 'cron_logs' does not exist. Please run installation.</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 2: Library loading
echo "<div class='test'>";
echo "<h3>Test 2: Cron Logger Library</h3>";
if (isset($CI->cron_logger)) {
    echo "<div class='success'>✓ Cron_logger library loaded successfully</div>";
} else {
    echo "<div class='error'>✗ Failed to load Cron_logger library</div>";
}
echo "</div>";

// Test 3: Log a successful cron
echo "<div class='test'>";
echo "<h3>Test 3: Log Success Entry</h3>";
try {
    $CI->cron_logger->start('test/success_cron');
    sleep(1); // Simulate work
    $CI->cron_logger->end('Test successful cron completed', 200);
    
    echo "<div class='success'>✓ Successfully logged a test cron execution</div>";
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 4: Log a failed cron
echo "<div class='test'>";
echo "<h3>Test 4: Log Failed Entry</h3>";
try {
    $CI->cron_logger->start('test/failed_cron');
    sleep(0.5);
    $CI->cron_logger->fail('Test error message', 500);
    
    echo "<div class='success'>✓ Successfully logged a failed cron execution</div>";
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 5: Log a rate-limited cron
echo "<div class='test'>";
echo "<h3>Test 5: Log Rate Limited Entry</h3>";
try {
    $CI->cron_logger->start('test/rate_limited_cron');
    $CI->cron_logger->rate_limit('Rate limit exceeded. Try again later.');
    
    echo "<div class='success'>✓ Successfully logged a rate-limited cron execution</div>";
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 6: Retrieve last run
echo "<div class='test'>";
echo "<h3>Test 6: Retrieve Last Run</h3>";
try {
    $last_run = $CI->cron_logger->get_last_run('test/success_cron');
    
    if ($last_run) {
        echo "<div class='success'>✓ Retrieved last run information</div>";
        echo "<pre>";
        echo "Cron Name: {$last_run->cron_name}\n";
        echo "Status: {$last_run->status}\n";
        echo "Executed At: {$last_run->executed_at}\n";
        echo "Execution Time: {$last_run->execution_time}s\n";
        echo "</pre>";
    } else {
        echo "<div class='error'>✗ No last run found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 7: Get statistics
echo "<div class='test'>";
echo "<h3>Test 7: Get Statistics</h3>";
try {
    $stats = $CI->cron_logger->get_stats('test/success_cron', 7);
    
    if ($stats) {
        echo "<div class='success'>✓ Retrieved statistics</div>";
        echo "<pre>";
        echo "Total Runs: {$stats->total_runs}\n";
        echo "Success Count: {$stats->success_count}\n";
        echo "Failed Count: {$stats->failed_count}\n";
        echo "Avg Execution Time: " . number_format($stats->avg_execution_time, 3) . "s\n";
        echo "</pre>";
    } else {
        echo "<div class='error'>✗ No statistics found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 8: Quick log wrapper
echo "<div class='test'>";
echo "<h3>Test 8: Quick Log Wrapper</h3>";
try {
    $result = $CI->cron_logger->log('test/quick_cron', function() {
        sleep(0.2);
        return 'Quick test completed';
    });
    
    echo "<div class='success'>✓ Quick log wrapper works correctly</div>";
    echo "<p>Result: <strong>{$result}</strong></p>";
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test 9: Display recent logs
echo "<div class='test'>";
echo "<h3>Test 9: Recent Test Logs</h3>";
try {
    $CI->db->select('*');
    $CI->db->from('cron_logs');
    $CI->db->like('cron_name', 'test/');
    $CI->db->order_by('executed_at', 'DESC');
    $CI->db->limit(10);
    $logs = $CI->db->get()->result();
    
    if (!empty($logs)) {
        echo "<div class='success'>✓ Retrieved recent test logs</div>";
        echo "<table border='1' cellpadding='8' style='width:100%; border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Cron Name</th><th>Status</th><th>Executed At</th><th>Time</th></tr>";
        
        foreach ($logs as $log) {
            $status_color = $log->status == 'success' ? '#28a745' : ($log->status == 'failed' ? '#dc3545' : '#ffc107');
            echo "<tr>";
            echo "<td>{$log->id}</td>";
            echo "<td>{$log->cron_name}</td>";
            echo "<td style='color: {$status_color}; font-weight: bold;'>{$log->status}</td>";
            echo "<td>{$log->executed_at}</td>";
            echo "<td>" . number_format($log->execution_time, 3) . "s</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<div class='error'>✗ No test logs found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Cleanup test logs
echo "<div class='test'>";
echo "<h3>Cleanup</h3>";
try {
    $CI->db->like('cron_name', 'test/');
    $CI->db->delete('cron_logs');
    $deleted = $CI->db->affected_rows();
    
    echo "<p>Cleaned up <strong>{$deleted}</strong> test log entries.</p>";
} catch (Exception $e) {
    echo "<p>Cleanup error: " . $e->getMessage() . "</p>";
}
echo "</div>";

echo "<hr>";
echo "<h2>✅ Test Suite Complete</h2>";
echo "<p>All tests have been executed. Check the results above for any errors.</p>";
echo "<p><a href='" . base_url('cron_logs/dashboard') . "'>Go to Cron Logs Dashboard</a></p>";

echo "</body></html>";
