<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_logs extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Check admin access first
        if (!get_role('admin')) {
            redirect(cn('statistics'));
            return;
        }
        
        // Try to load model with error handling
        try {
            $this->load->model('cron_logs_model', 'model');
        } catch (Exception $e) {
            log_message('error', 'Cron_logs controller - Failed to load model: ' . $e->getMessage());
            show_error('Unable to load cron logs. Please ensure the database table is created.');
        }
    }
    
    /**
     * Main view - List all cron logs with summary
     */
    public function index() {
        try {
            $data = array(
                'module' => get_class($this),
                'title' => lang('cron_logs'),
                'summary' => $this->model->get_cron_summary(),
                'statistics' => $this->model->get_statistics()
            );
            
            $this->template->build('index', $data);
        } catch (Exception $e) {
            log_message('error', 'Cron_logs index - Database error: ' . $e->getMessage());
            show_error('Unable to load cron logs. Please ensure the cron_logs table exists in the database. Run the SQL migration file: database/cron-logging-system.sql');
        }
    }
    
    /**
     * Get logs via AJAX for pagination/filtering
     */
    public function ajax_get_logs() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        // Get filter parameters
        $params = array(
            'cron_name' => $this->input->get('cron_name', true),
            'status' => $this->input->get('status', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
            'limit' => $this->input->get('limit', true) ?: 20,
            'offset' => $this->input->get('offset', true) ?: 0
        );
        
        $result = $this->model->get_logs($params);
        
        ms(array(
            'status' => 'success',
            'data' => $result
        ));
    }
    
    /**
     * View detailed log entry
     */
    public function view($id) {
        $log = $this->model->get_log($id);
        
        if (!$log) {
            show_404();
        }
        
        $data = array(
            'module' => get_class($this),
            'log' => $log
        );
        
        $this->load->view('view', $data);
    }
    
    /**
     * Manually trigger a cron job
     */
    public function trigger() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $cron_url = $this->input->post('cron_url', true);
        
        if (empty($cron_url)) {
            ms(array(
                'status' => 'error',
                'message' => lang('cron_url_required')
            ));
        }
        
        // Validate that it's a local URL
        $base_url = base_url();
        if (strpos($cron_url, '/') === 0) {
            $cron_url = rtrim($base_url, '/') . $cron_url;
        } elseif (strpos($cron_url, $base_url) !== 0) {
            ms(array(
                'status' => 'error',
                'message' => lang('invalid_cron_url')
            ));
        }
        
        // Trigger the cron using cURL
        try {
            $ch = curl_init($cron_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes timeout
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $start_time = microtime(true);
            $response = curl_exec($ch);
            $execution_time = microtime(true) - $start_time;
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                ms(array(
                    'status' => 'error',
                    'message' => lang('cron_trigger_failed') . ': ' . $error
                ));
            }
            
            ms(array(
                'status' => 'success',
                'message' => lang('cron_triggered_successfully'),
                'data' => array(
                    'http_code' => $http_code,
                    'execution_time' => round($execution_time, 2),
                    'response' => substr($response, 0, 500) // First 500 chars
                )
            ));
            
        } catch (Exception $e) {
            ms(array(
                'status' => 'error',
                'message' => lang('cron_trigger_failed') . ': ' . $e->getMessage()
            ));
        }
    }
    
    /**
     * Delete old logs
     */
    public function delete_old() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $days = $this->input->post('days', true) ?: 30;
        
        try {
            $this->model->delete_old_logs($days);
            
            ms(array(
                'status' => 'success',
                'message' => lang('old_logs_deleted')
            ));
        } catch (Exception $e) {
            ms(array(
                'status' => 'error',
                'message' => lang('delete_failed') . ': ' . $e->getMessage()
            ));
        }
    }
    
    /**
     * Clear all logs
     */
    public function clear_all() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        try {
            $this->model->delete_all_logs();
            
            ms(array(
                'status' => 'success',
                'message' => lang('all_logs_cleared')
            ));
        } catch (Exception $e) {
            ms(array(
                'status' => 'error',
                'message' => lang('clear_failed') . ': ' . $e->getMessage()
            ));
        }
    }
    
    /**
     * Export logs to CSV
     */
    public function export() {
        // Get all logs without pagination
        $params = array(
            'cron_name' => $this->input->get('cron_name', true),
            'status' => $this->input->get('status', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
            'limit' => 10000 // Max export limit
        );
        
        $result = $this->model->get_logs($params);
        $logs = $result['logs'];
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="cron_logs_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, array('ID', 'Cron Name', 'Executed At', 'Status', 'Response Code', 'Execution Time (s)', 'Response Message'));
        
        // CSV rows
        foreach ($logs as $log) {
            fputcsv($output, array(
                $log->id,
                $log->cron_name,
                $log->executed_at,
                $log->status,
                $log->response_code,
                $log->execution_time,
                $log->response_message
            ));
        }
        
        fclose($output);
        exit;
    }
}
