<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Logs Controller
 * 
 * Admin interface for viewing and managing cron logs
 */
class Cron_logs extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        $this->load->library('cron_logger');
    }
    
    /**
     * Main view - display cron logs
     */
    public function index() {
        // Get filter parameters
        $params = [
            'cron_name' => $this->input->get('cron_name', true),
            'status' => $this->input->get('status', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
            'page' => $this->input->get('page', true) ?: 1,
            'per_page' => 50
        ];
        
        // Get logs with filters
        $logs = $this->model->get_logs($params);
        
        // Get unique cron names for filter dropdown
        $cron_names = $this->model->get_cron_names();
        
        // Get summary stats
        $summary = $this->model->get_dashboard_summary(7);
        
        $data = [
            'module' => get_class($this),
            'logs' => $logs,
            'cron_names' => $cron_names,
            'summary' => $summary,
            'filters' => $params
        ];
        
        $this->template->build('index', $data);
    }
    
    /**
     * View detailed log entry
     */
    public function view($id) {
        $log = $this->model->get_log($id);
        
        if (!$log) {
            redirect(cn('cron_logs'));
        }
        
        $data = [
            'module' => get_class($this),
            'log' => $log
        ];
        
        $this->template->build('view', $data);
    }
    
    /**
     * Overview dashboard
     */
    public function dashboard() {
        $days = $this->input->get('days', true) ?: 7;
        
        // Get all cron last runs
        $last_runs = $this->model->get_all_last_runs();
        
        // Get statistics for all crons
        $stats = $this->model->get_all_stats($days);
        
        // Get summary
        $summary = $this->model->get_dashboard_summary($days);
        
        $data = [
            'module' => get_class($this),
            'last_runs' => $last_runs,
            'stats' => $stats,
            'summary' => $summary,
            'days' => $days
        ];
        
        $this->template->build('dashboard', $data);
    }
    
    /**
     * Manually trigger a cron job
     */
    public function trigger() {
        // Check if POST request
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $cron_url = $this->input->post('cron_url', true);
        
        if (empty($cron_url)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Cron URL is required'
                ]));
            return;
        }
        
        // Start logging
        $this->cron_logger->start($cron_url);
        
        try {
            // Make HTTP request to the cron URL
            $ch = curl_init($cron_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                $this->cron_logger->fail($error, 0);
                
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Cron execution failed: ' . $error
                    ]));
                return;
            }
            
            // Log success
            $this->cron_logger->end($response, $http_code);
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Cron executed successfully',
                    'response_code' => $http_code,
                    'response' => substr($response, 0, 500)
                ]));
                
        } catch (Exception $e) {
            $this->cron_logger->fail($e->getMessage());
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Exception: ' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * Clean up old logs
     */
    public function cleanup() {
        // Check if POST request
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }
        
        $days = $this->input->post('days', true) ?: 30;
        $deleted = $this->model->cleanup_old_logs($days);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Deleted {$deleted} old log entries",
                'deleted' => $deleted
            ]));
    }
    
    /**
     * AJAX: Get stats for a specific cron
     */
    public function ajax_get_stats() {
        $cron_name = $this->input->get('cron_name', true);
        $days = $this->input->get('days', true) ?: 7;
        
        if (!$cron_name) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Cron name required']));
            return;
        }
        
        $stats = $this->cron_logger->get_stats($cron_name, $days);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($stats));
    }
}
