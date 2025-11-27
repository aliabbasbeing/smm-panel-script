<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Notification Queue Cron Controller
 * 
 * Processes queued WhatsApp notifications to ensure order processing
 * is not delayed by slow/unavailable WhatsApp API.
 * 
 * Route: /cron/whatsapp_notifications
 */
class Whatsapp_notification_cron extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('cron_logger');
        $this->load->library('whatsapp_notification');
    }
    
    /**
     * Main cron entry point - processes queued WhatsApp notifications
     * Route: /cron/whatsapp_notifications
     * 
     * Optional parameters:
     * - limit: Max notifications to process (default: 20)
     * - timeout: Max seconds per API call (default: 3)
     */
    public function process() {
        // Start logging
        $log_id = $this->cron_logger->start('cron/whatsapp_notifications');
        
        try {
            $limit = $this->input->get('limit', true);
            $timeout = $this->input->get('timeout', true);
            
            $limit = is_numeric($limit) && $limit > 0 ? (int)$limit : 20;
            $timeout = is_numeric($timeout) && $timeout > 0 ? (int)$timeout : 3;
            
            // Ensure reasonable limits
            if ($limit > 100) $limit = 100;
            if ($timeout > 10) $timeout = 10;
            
            echo "Processing WhatsApp notification queue...<br>";
            echo "Limit: {$limit}, Timeout: {$timeout}s<br>";
            
            // Process the queue
            $results = $this->whatsapp_notification->process_queue($limit, $timeout);
            
            echo "Processed: {$results['processed']}<br>";
            echo "Sent: {$results['sent']}<br>";
            echo "Failed: {$results['failed']}<br>";
            echo "Skipped: {$results['skipped']}<br>";
            echo "Successfully";
            
            // Log success
            $message = sprintf(
                "Processed %d notifications: %d sent, %d failed, %d skipped",
                $results['processed'],
                $results['sent'],
                $results['failed'],
                $results['skipped']
            );
            $this->cron_logger->end($log_id, 'Success', 200, $message);
            
        } catch (Exception $e) {
            log_message('error', 'WhatsApp notification cron error: ' . $e->getMessage());
            echo "Error: " . $e->getMessage() . "<br>";
            echo "Failed";
            
            $this->cron_logger->end($log_id, 'Failed', 500, $e->getMessage());
        }
    }
    
    /**
     * Cleanup old queue entries
     * Route: /cron/whatsapp_notifications_cleanup
     * 
     * Optional parameters:
     * - days: Number of days to keep (default: 7)
     */
    public function cleanup() {
        // Start logging
        $log_id = $this->cron_logger->start('cron/whatsapp_notifications_cleanup');
        
        try {
            $days = $this->input->get('days', true);
            $days = is_numeric($days) && $days > 0 ? (int)$days : 7;
            
            echo "Cleaning up WhatsApp notification queue...<br>";
            echo "Removing entries older than {$days} days<br>";
            
            $deleted = $this->whatsapp_notification->cleanup_queue($days);
            
            echo "Deleted: {$deleted} entries<br>";
            echo "Successfully";
            
            $this->cron_logger->end($log_id, 'Success', 200, "Deleted {$deleted} old queue entries");
            
        } catch (Exception $e) {
            log_message('error', 'WhatsApp notification cleanup error: ' . $e->getMessage());
            echo "Error: " . $e->getMessage() . "<br>";
            echo "Failed";
            
            $this->cron_logger->end($log_id, 'Failed', 500, $e->getMessage());
        }
    }
}
