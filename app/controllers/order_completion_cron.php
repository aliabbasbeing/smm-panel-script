<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Order Completion Time Cron Controller
 * 
 * This controller calculates and updates average completion times
 * for services based on the last 10 completed orders
 */
class Order_completion_cron extends MX_Controller {
    
    public $tb_orders;
    public $tb_services;
    
    public function __construct() {
        parent::__construct();
        $this->tb_orders = ORDER;
        $this->tb_services = SERVICES;
        $this->load->library('cron_logger');
    }
    
    /**
     * Main cron entry point - calculates avg completion times for all services
     * Route: /cron/completion_time
     */
    public function calculate_avg_completion() {
        // Start logging
        $this->cron_logger->start('cron/completion_time');
        
        try {
            echo "Starting average completion time calculation...<br>";
            
            // Get all active services
            $this->db->select('id');
            $this->db->from($this->tb_services);
            $services = $this->db->get()->result();
            
            if (empty($services)) {
                echo "No services found.<br>Successfully";
                $this->cron_logger->end('No services found');
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
            
            // Log success
            $this->cron_logger->end("Updated {$updated_count} services successfully");
            
        } catch (Exception $e) {
            $this->cron_logger->fail($e->getMessage());
            echo "Error: " . $e->getMessage();
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
