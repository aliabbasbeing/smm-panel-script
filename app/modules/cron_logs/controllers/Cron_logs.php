<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_logs extends MX_Controller {
    
    public $tb_cron_logs;
    public $module;
    public $module_name;
    public $module_icon;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('cron_logs_model', 'model');
        $this->load->library('cron_logger');
        
        $this->tb_cron_logs = 'cron_logs';
        $this->module = get_class($this);
        $this->module_name = 'Cron Logs';
        $this->module_icon = 'fa fa-clock-o';
        
        // Check admin access
        if (!get_role('admin')) {
            redirect(cn('auth'));
        }
    }
    
    /**
     * Main logs listing page
     */
    public function index() {
        $data = array(
            'module' => $this->module,
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
            'cron_names' => $this->model->get_cron_names(),
            'statistics' => $this->model->get_cron_statistics(),
        );
        
        $this->template->build('index', $data);
    }
    
    /**
     * Get logs via AJAX for DataTable
     */
    public function ajax_get_logs() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        // Get request parameters
        $draw = $this->input->post('draw', true);
        $start = $this->input->post('start', true);
        $length = $this->input->post('length', true);
        $search = $this->input->post('search', true);
        $order = $this->input->post('order', true);
        
        // Filters
        $cron_name = $this->input->post('filter_cron_name', true);
        $status = $this->input->post('filter_status', true);
        $date_from = $this->input->post('filter_date_from', true);
        $date_to = $this->input->post('filter_date_to', true);
        
        $params = array(
            'limit' => $length,
            'offset' => $start,
        );
        
        if (!empty($search['value'])) {
            $params['search'] = $search['value'];
        }
        
        if (!empty($cron_name)) {
            $params['cron_name'] = $cron_name;
        }
        
        if (!empty($status)) {
            $params['status'] = $status;
        }
        
        if (!empty($date_from)) {
            $params['date_from'] = $date_from . ' 00:00:00';
        }
        
        if (!empty($date_to)) {
            $params['date_to'] = $date_to . ' 23:59:59';
        }
        
        // Order
        if (!empty($order)) {
            $columns = array('id', 'cron_name', 'executed_at', 'status', 'response_code', 'execution_time');
            $order_column = isset($columns[$order[0]['column']]) ? $columns[$order[0]['column']] : 'executed_at';
            $order_dir = $order[0]['dir'];
            
            $params['order_by'] = $order_column;
            $params['order_dir'] = $order_dir;
        }
        
        $logs = $this->model->get_logs($params);
        $total_records = $this->model->count_logs(array());
        $filtered_records = $this->model->count_logs($params);
        
        $data = array();
        foreach ($logs as $log) {
            $status_badge = $this->get_status_badge($log->status);
            $execution_time = $log->execution_time ? number_format($log->execution_time, 4) . 's' : '-';
            
            $data[] = array(
                'id' => $log->id,
                'cron_name' => esc($log->cron_name),
                'executed_at' => date('Y-m-d H:i:s', strtotime($log->executed_at)),
                'status' => $status_badge,
                'response_code' => $log->response_code ? $log->response_code : '-',
                'execution_time' => $execution_time,
                'actions' => $this->get_action_buttons($log->id)
            );
        }
        
        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        );
        
        echo json_encode($response);
    }
    
    /**
     * View log details
     */
    public function view($id = null) {
        if (empty($id)) {
            redirect(cn('cron_logs'));
        }
        
        $log = $this->model->get_log_by_id($id);
        
        if (!$log) {
            _e('message', 'error', 'Log not found');
            redirect(cn('cron_logs'));
        }
        
        $data = array(
            'module' => $this->module,
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
            'log' => $log,
        );
        
        $this->template->build('view', $data);
    }
    
    /**
     * Delete log
     */
    public function delete($id = null) {
        if (empty($id)) {
            ms(array(
                'status' => 'error',
                'message' => 'Invalid log ID'
            ));
        }
        
        $deleted = $this->model->delete_log($id);
        
        if ($deleted) {
            ms(array(
                'status' => 'success',
                'message' => 'Log deleted successfully'
            ));
        } else {
            ms(array(
                'status' => 'error',
                'message' => 'Failed to delete log'
            ));
        }
    }
    
    /**
     * Settings page
     */
    public function settings() {
        $data = array(
            'module' => $this->module,
            'module_name' => 'Cron Settings',
            'module_icon' => $this->module_icon,
            'settings' => $this->model->get_all_settings(),
        );
        
        $this->template->build('settings', $data);
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        if ($this->input->method() !== 'post') {
            ms(array(
                'status' => 'error',
                'message' => 'Invalid request method'
            ));
        }
        
        $enable_notifications = $this->input->post('enable_email_notifications', true);
        $notification_email = $this->input->post('notification_email', true);
        $retention_days = $this->input->post('log_retention_days', true);
        
        $this->model->update_setting('enable_email_notifications', $enable_notifications);
        $this->model->update_setting('notification_email', $notification_email);
        $this->model->update_setting('log_retention_days', $retention_days);
        
        ms(array(
            'status' => 'success',
            'message' => 'Settings saved successfully'
        ));
    }
    
    /**
     * Cleanup old logs
     */
    public function cleanup() {
        $retention_days = $this->model->get_setting('log_retention_days', 30);
        $deleted = $this->model->delete_old_logs($retention_days);
        
        ms(array(
            'status' => 'success',
            'message' => "Cleaned up {$deleted} old log(s)"
        ));
    }
    
    /**
     * Manually trigger a cron job
     */
    public function trigger($cron_name = null) {
        if (empty($cron_name)) {
            ms(array(
                'status' => 'error',
                'message' => 'Cron name is required'
            ));
        }
        
        // Map cron names to their URLs
        $cron_routes = array(
            'order' => 'api_provider/cron/order',
            'status' => 'api_provider/cron/status',
            'sync_services' => 'api_provider/cron/sync_services',
            'status_subscriptions' => 'api_provider/cron/status_subscriptions',
            'refill' => 'api_provider/cron/refill',
            'email_marketing' => 'email_cron/run',
            'whatsapp_marketing' => 'whatsapp_cron/run',
            'currency_rates' => 'currencies/cron_fetch_rates',
        );
        
        if (!isset($cron_routes[$cron_name])) {
            ms(array(
                'status' => 'error',
                'message' => 'Unknown cron job'
            ));
        }
        
        // Redirect to the cron URL
        redirect($cron_routes[$cron_name]);
    }
    
    /**
     * Get status badge HTML
     */
    private function get_status_badge($status) {
        $badges = array(
            'success' => '<span class="badge badge-success">Success</span>',
            'failed' => '<span class="badge badge-danger">Failed</span>',
            'running' => '<span class="badge badge-warning">Running</span>',
        );
        
        return isset($badges[$status]) ? $badges[$status] : '<span class="badge badge-secondary">' . esc($status) . '</span>';
    }
    
    /**
     * Get action buttons HTML
     */
    private function get_action_buttons($id) {
        $view_url = cn('cron_logs/view/' . $id);
        $delete_url = cn('cron_logs/delete/' . $id);
        
        $html = '<div class="btn-group">';
        $html .= '<a href="' . $view_url . '" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a>';
        $html .= '<a href="' . $delete_url . '" class="btn btn-sm btn-danger actionDelete" title="Delete"><i class="fa fa-trash"></i></a>';
        $html .= '</div>';
        
        return $html;
    }
}
