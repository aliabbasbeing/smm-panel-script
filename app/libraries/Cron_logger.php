<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logger Library
 * 
 * Simple library to track cron job last run times.
 * Only stores cron name and last execution timestamp.
 * 
 * Usage:
 *   $this->load->library('cron_logger');
 *   $this->cron_logger->log('cron/order');
 * 
 * @package    SMM Panel
 * @subpackage Libraries
 */
class Cron_logger {
    
    protected $CI;
    protected $table = 'cron_logs';
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    /**
     * Log a cron execution - updates or inserts last run time
     * 
     * @param string $cron_name The cron identifier (e.g., 'cron/order', '/cron/status')
     * @return bool
     */
    public function log($cron_name) {
        try {
            // Normalize cron name (remove leading slash if present)
            $cron_name = ltrim($cron_name, '/');
            
            // Check if cron already exists
            $this->CI->db->where('cron_name', $cron_name);
            $existing = $this->CI->db->get($this->table)->row();
            
            if ($existing) {
                // Update existing record
                $this->CI->db->where('cron_name', $cron_name);
                $this->CI->db->update($this->table, array('executed_at' => NOW));
            } else {
                // Insert new record (minimal fields)
                $data = array(
                    'cron_name' => $cron_name,
                    'executed_at' => NOW,
                    'created' => NOW
                );
                $this->CI->db->insert($this->table, $data);
            }
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::log failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Start logging - simplified to just call log()
     * Kept for backward compatibility with existing cron controllers
     * 
     * @param string $cron_name The cron identifier
     * @return bool
     */
    public function start($cron_name) {
        return $this->log($cron_name);
    }
    
    /**
     * End logging - no-op for backward compatibility
     * 
     * @param mixed $log_id Ignored
     * @param string $status Ignored
     * @param int $response_code Ignored
     * @param string $response_message Ignored
     * @return bool
     */
    public function end($log_id = null, $status = 'Success', $response_code = 200, $response_message = null) {
        // No-op - we only track last run time now
        return true;
    }
    
    /**
     * Get the last execution time for a specific cron
     * 
     * @param string $cron_name The cron identifier
     * @return object|null
     */
    public function get_last_log($cron_name) {
        try {
            $cron_name = ltrim($cron_name, '/');
            
            $this->CI->db->where('cron_name', $cron_name);
            $this->CI->db->order_by('executed_at', 'DESC');
            $this->CI->db->limit(1);
            $query = $this->CI->db->get($this->table);
            
            return $query->row();
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::get_last_log failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all cron logs (one per cron name)
     * 
     * @return array
     */
    public function get_all_logs() {
        try {
            $this->CI->db->order_by('cron_name', 'ASC');
            $query = $this->CI->db->get($this->table);
            
            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Cron_logger::get_all_logs failed: ' . $e->getMessage());
            return array();
        }
    }
}
