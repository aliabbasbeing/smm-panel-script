<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Suppress PHP 8.x deprecation warnings for this cron endpoint
// These are compatibility issues with CodeIgniter 3.x running on PHP 8.x
@error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
@ini_set('display_errors', '0');

/**
 * Order Completion Time Cron Controller
 * 
 * This controller calculates and updates average completion times
 * for services based on the last 10 completed orders.
 * 
 * This controller is fully independent and loads all necessary dependencies
 * (database, models, helpers) on its own without relying on other cron jobs.
 * 
 * Route: /cron/completion_time
 */
class Order_completion_cron extends CI_Controller {
    
    public $tb_orders;
    public $tb_services;
    
    public function __construct() {
        // Suppress errors during parent construction (session issues on PHP 8.x)
        $error_level = error_reporting();
        error_reporting(0);
        
        try {
            parent::__construct();
        } catch (Exception $e) {
            // Session initialization may fail on PHP 8.x, but we don't need sessions for cron
            // Continue anyway
        } catch (TypeError $e) {
            // PHP 8.x type errors from session callbacks - safe to ignore for cron
        }
        
        // Restore error reporting for our code
        error_reporting($error_level);
        
        // Load database library
        $this->load->database();
        
        $this->tb_orders = ORDER;
        $this->tb_services = SERVICES;
    }
    
    /**
     * Main cron entry point - calculates avg completion times for all services
     * Route: /cron/completion_time
     */
    public function calculate_avg_completion() {
        try {
            echo "Starting average completion time calculation...<br>";
            
            // Verify database connection
            if (!$this->db->conn_id) {
                throw new Exception("Database connection failed");
            }
            
            // Get all active services
            $this->db->select('id');
            $this->db->from($this->tb_services);
            $services = $this->db->get()->result();
            
            if (empty($services)) {
                echo "No services found.<br>Successfully";
                return;
            }
            
            $updated_count = 0;
            
            foreach ($services as $service) {
                // Calculate average completion time for last 10 completed orders
                $avg_time = $this->calculate_service_avg_time($service->id);
                
                // Update service table with new average
                $this->db->update($this->tb_services, 
                    ['avg_completion_time' => $avg_time], 
                    ['id' => $service->id]
                );
                
                // Update recent orders with the new average
                $this->update_recent_orders_avg($service->id, $avg_time);
                
                $updated_count++;
                
                if ($updated_count % 50 == 0) {
                    echo "Processed {$updated_count} services...<br>";
                }
            }
            
            echo "Completed! Updated {$updated_count} services.<br>";
            echo "Successfully";
            
        } catch (Exception $e) {
            // Log the error
            log_message('error', 'Order completion cron error: ' . $e->getMessage());
            
            // Output error message
            echo "Error: " . $e->getMessage() . "<br>";
            echo "Failed";
            
            // Write to error log if logging is available
            if (function_exists('error_log')) {
                error_log("Order completion cron error: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Calculate average completion time for a service based on last 10 completed orders
     * 
     * @param int $service_id
     * @return int|null Average time in seconds, or NULL if no completed orders
     */
    private function calculate_service_avg_time($service_id) {
        $this->db->select('TIMESTAMPDIFF(SECOND, created, completed_at) AS completion_time');
        $this->db->from($this->tb_orders);
        $this->db->where('service_id', $service_id);
        $this->db->where('status', 'completed');
        $this->db->where('completed_at IS NOT NULL', NULL, FALSE);
        $this->db->order_by('completed_at', 'DESC');
        $this->db->limit(10);
        
        $query = $this->db->get();
        $results = $query->result();
        
        if (empty($results)) {
            return NULL;
        }
        
        // Calculate average
        $total_time = 0;
        $count = 0;
        
        foreach ($results as $row) {
            if ($row->completion_time !== NULL && $row->completion_time > 0) {
                $total_time += $row->completion_time;
                $count++;
            }
        }
        
        if ($count == 0) {
            return NULL;
        }
        
        return round($total_time / $count);
    }
    
    /**
     * Update last_10_avg_time for recent orders of this service
     * 
     * @param int $service_id
     * @param int|null $avg_time
     */
    private function update_recent_orders_avg($service_id, $avg_time) {
        if ($avg_time === NULL) {
            return;
        }
        
        // Update last 50 completed orders for this service
        // (we update more than 10 to ensure we capture recent activity)
        $this->db->select('id');
        $this->db->from($this->tb_orders);
        $this->db->where('service_id', $service_id);
        $this->db->where('status', 'completed');
        $this->db->order_by('completed_at', 'DESC');
        $this->db->limit(50);
        
        $query = $this->db->get();
        $orders = $query->result();
        
        if (!empty($orders)) {
            $order_ids = array_map(function($order) {
                return $order->id;
            }, $orders);
            
            $this->db->where_in('id', $order_ids);
            $this->db->update($this->tb_orders, ['last_10_avg_time' => $avg_time]);
        }
    }
}
