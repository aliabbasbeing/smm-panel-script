<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing extends MX_Controller {
    
    public $module_name;
    public $module;
    public $module_icon;
    
    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Config Module
        $this->module_name = 'WhatsApp Marketing';
        $this->module = strtolower(get_class($this));
        $this->module_icon = "fa fa-whatsapp";
        
        // Check if user is admin
        if (!get_role("admin")) {
            _validation('error', "Permission Denied!");
        }
        
        // Check if required database tables exist
        if (!$this->_check_tables_exist()) {
            $this->_show_installation_required();
        }
    }
    
    /**
     * Check if all required database tables exist
     */
    private function _check_tables_exist() {
        $required_tables = array(
            'whatsapp_campaigns',
            'whatsapp_templates',
            'whatsapp_api_configs',
            'whatsapp_recipients',
            'whatsapp_logs',
            'whatsapp_settings'
        );
        
        foreach ($required_tables as $table) {
            if (!$this->db->table_exists($table)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Display installation required message
     */
    private function _show_installation_required() {
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            "sql_file" => "database/whatsapp-marketing.sql"
        );
        $this->template->build("installation_required", $data);
        exit;
    }
    
    // ========================================
    // MAIN DASHBOARD
    // ========================================
    
    public function index(){
        $stats = $this->model->get_overall_stats();
        $recent_logs = $this->model->get_recent_logs(10);
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            "stats" => $stats,
            "recent_logs" => $recent_logs
        );
        $this->template->build("index", $data);
    }
    
    // ========================================
    // CAMPAIGNS
    // ========================================
    
    public function campaigns($page = 1){
        $page = max(1, (int)$page);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $campaigns = $this->model->get_campaigns($per_page, $offset);
        $total = $this->model->get_campaigns();
        
        $data = array(
            "module" => $this->module,
            "campaigns" => $campaigns,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("campaigns/index", $data);
    }
    
    public function campaign_create(){
        $templates = $this->model->get_templates(1000, 0);
        $api_configs = $this->model->get_api_configs(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "templates" => $templates,
            "api_configs" => $api_configs
        );
        $this->load->view('campaigns/create', $data);
    }
    
    public function ajax_campaign_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $template_id = post("template_id");
        $api_config_id = post("api_config_id");
        $sending_limit_hourly = post("sending_limit_hourly");
        $sending_limit_daily = post("sending_limit_daily");
        
        if(empty($name)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        if(empty($template_id) || empty($api_config_id)){
            ms(array(
                "status" => "error",
                "message" => "Please select template and API configuration"
            ));
        }
        
        $campaign_data = array(
            'name' => $name,
            'template_id' => $template_id,
            'api_config_id' => $api_config_id,
            'status' => 'pending',
            'sending_limit_hourly' => $sending_limit_hourly ? (int)$sending_limit_hourly : null,
            'sending_limit_daily' => $sending_limit_daily ? (int)$sending_limit_daily : null
        );
        
        if($this->model->create_campaign($campaign_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign created successfully"
            ));
        }
    }
    
    public function campaign_edit($ids = ""){
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $templates = $this->model->get_templates(1000, 0);
        $api_configs = $this->model->get_api_configs(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "templates" => $templates,
            "api_configs" => $api_configs
        );
        $this->load->view('campaigns/edit', $data);
    }
    
    public function ajax_campaign_edit($ids = ""){
        _is_ajax($this->module);
        
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        $name = post("name");
        $template_id = post("template_id");
        $api_config_id = post("api_config_id");
        $sending_limit_hourly = post("sending_limit_hourly");
        $sending_limit_daily = post("sending_limit_daily");
        
        if(empty($name)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'template_id' => $template_id,
            'api_config_id' => $api_config_id,
            'sending_limit_hourly' => $sending_limit_hourly ? (int)$sending_limit_hourly : null,
            'sending_limit_daily' => $sending_limit_daily ? (int)$sending_limit_daily : null
        );
        
        if($this->model->update_campaign($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign updated successfully"
            ));
        }
    }
    
    public function ajax_campaign_delete(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        if($this->model->delete_campaign($ids)){
            ms(array(
                "status" => "success",
                "message" => "Campaign deleted successfully"
            ));
        }
    }
    
    public function ajax_campaign_start(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        $campaign = $this->model->get_campaign($ids);
        
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        $recipient_count = $this->model->get_recipients($campaign->id);
        if($recipient_count == 0){
            ms(array(
                "status" => "error",
                "message" => "Cannot start campaign without recipients"
            ));
        }
        
        $update_data = array(
            'status' => 'running',
            'started_at' => NOW
        );
        
        if($this->model->update_campaign($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign started successfully"
            ));
        }
    }
    
    public function ajax_campaign_pause(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        $update_data = array('status' => 'paused');
        
        if($this->model->update_campaign($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign paused successfully"
            ));
        }
    }
    
    public function ajax_campaign_resume(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        $update_data = array('status' => 'running');
        
        if($this->model->update_campaign($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign resumed successfully"
            ));
        }
    }
    
    public function ajax_campaign_resend_failed(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        $campaign = $this->model->get_campaign($ids);
        
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        $reset_count = $this->model->reset_failed_recipients($campaign->id);
        
        if($reset_count > 0){
            $this->model->update_campaign_stats($campaign->id);
            
            if($campaign->status == 'completed'){
                $this->model->update_campaign($ids, array('status' => 'running'));
            }
            
            ms(array(
                "status" => "success",
                "message" => "Reset {$reset_count} failed message(s) for resending"
            ));
        } else {
            ms(array(
                "status" => "error",
                "message" => "No failed messages found to resend"
            ));
        }
    }
    
    public function ajax_resend_single_message(){
        _is_ajax($this->module);
        
        $recipient_id = post("recipient_id");
        
        if(!$recipient_id){
            ms(array(
                "status" => "error",
                "message" => "Recipient ID is required"
            ));
        }
        
        $this->db->where('id', $recipient_id);
        $recipient = $this->db->get('whatsapp_recipients')->row();
        
        if(!$recipient){
            ms(array(
                "status" => "error",
                "message" => "Recipient not found"
            ));
        }
        
        if($recipient->status != 'failed'){
            ms(array(
                "status" => "error",
                "message" => "Only failed messages can be resent"
            ));
        }
        
        $this->db->where('id', $recipient_id);
        $this->db->update('whatsapp_recipients', [
            'status' => 'pending',
            'sent_at' => null,
            'error_message' => null,
            'updated_at' => NOW
        ]);
        
        $this->model->update_campaign_stats($recipient->campaign_id);
        
        $this->db->where('id', $recipient->campaign_id);
        $campaign = $this->db->get('whatsapp_campaigns')->row();
        
        if($campaign && $campaign->status == 'completed'){
            $this->db->where('id', $recipient->campaign_id);
            $this->db->update('whatsapp_campaigns', array('status' => 'running'));
        }
        
        ms(array(
            "status" => "success",
            "message" => "Message reset for resending"
        ));
    }
    
    public function campaign_details($ids = ""){
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $this->model->update_campaign_stats($campaign->id);
        $campaign = $this->model->get_campaign($ids);
        
        $recipients = $this->model->get_recipients($campaign->id, 100, 0);
        $logs = $this->model->get_logs($campaign->id, 50, 0);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "recipients" => $recipients,
            "logs" => $logs
        );
        $this->template->build("campaigns/details", $data);
    }
    
    // ========================================
    // TEMPLATES
    // ========================================
    
    public function templates($page = 1){
        $page = max(1, (int)$page);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $templates = $this->model->get_templates($per_page, $offset);
        $total = $this->model->get_templates();
        
        $data = array(
            "module" => $this->module,
            "templates" => $templates,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("templates/index", $data);
    }
    
    public function template_create(){
        $data = array(
            "module" => $this->module
        );
        $this->load->view('templates/create', $data);
    }
    
    public function ajax_template_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $message = post("message");
        $description = post("description");
        
        if(empty($name) || empty($message)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $template_data = array(
            'name' => $name,
            'message' => $message,
            'description' => $description,
            'status' => 1
        );
        
        if($this->model->create_template($template_data)){
            ms(array(
                "status" => "success",
                "message" => "Template created successfully"
            ));
        }
    }
    
    public function template_edit($ids = ""){
        $template = $this->model->get_template($ids);
        if(!$template){
            redirect(cn($this->module . "/templates"));
        }
        
        $data = array(
            "module" => $this->module,
            "template" => $template
        );
        $this->load->view('templates/edit', $data);
    }
    
    public function ajax_template_edit($ids = ""){
        _is_ajax($this->module);
        
        $template = $this->model->get_template($ids);
        if(!$template){
            ms(array(
                "status" => "error",
                "message" => "Template not found"
            ));
        }
        
        $name = post("name");
        $message = post("message");
        $description = post("description");
        
        if(empty($name) || empty($message)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'message' => $message,
            'description' => $description
        );
        
        if($this->model->update_template($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Template updated successfully"
            ));
        }
    }
    
    public function ajax_template_delete(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        if($this->model->delete_template($ids)){
            ms(array(
                "status" => "success",
                "message" => "Template deleted successfully"
            ));
        } else {
            ms(array(
                "status" => "error",
                "message" => "Cannot delete template that is in use by active campaigns"
            ));
        }
    }
    
    // ========================================
    // API CONFIGURATIONS
    // ========================================
    
    public function api($page = 1){
        $page = max(1, (int)$page);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $api_configs = $this->model->get_api_configs($per_page, $offset);
        $total = $this->model->get_api_configs();
        
        $data = array(
            "module" => $this->module,
            "api_configs" => $api_configs,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("api/index", $data);
    }
    
    public function api_create(){
        $data = array(
            "module" => $this->module
        );
        $this->load->view('api/create', $data);
    }
    
    public function ajax_api_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $api_url = post("api_url");
        $api_key = post("api_key");
        $is_default = post("is_default");
        $status = post("status");
        
        if(empty($name) || empty($api_url) || empty($api_key)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $api_data = array(
            'name' => $name,
            'api_url' => $api_url,
            'api_key' => $api_key,
            'is_default' => $is_default ? 1 : 0,
            'status' => $status ? 1 : 0
        );
        
        if($this->model->create_api_config($api_data)){
            ms(array(
                "status" => "success",
                "message" => "API configuration created successfully"
            ));
        }
    }
    
    public function api_edit($ids = ""){
        $api = $this->model->get_api_config($ids);
        if(!$api){
            redirect(cn($this->module . "/api"));
        }
        
        $data = array(
            "module" => $this->module,
            "api" => $api
        );
        $this->load->view('api/edit', $data);
    }
    
    public function ajax_api_edit($ids = ""){
        _is_ajax($this->module);
        
        $api = $this->model->get_api_config($ids);
        if(!$api){
            ms(array(
                "status" => "error",
                "message" => "API configuration not found"
            ));
        }
        
        $name = post("name");
        $api_url = post("api_url");
        $api_key = post("api_key");
        $is_default = post("is_default");
        $status = post("status");
        
        if(empty($name) || empty($api_url) || empty($api_key)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'api_url' => $api_url,
            'api_key' => $api_key,
            'is_default' => $is_default ? 1 : 0,
            'status' => $status ? 1 : 0
        );
        
        if($this->model->update_api_config($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "API configuration updated successfully"
            ));
        }
    }
    
    public function ajax_api_delete(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        if($this->model->delete_api_config($ids)){
            ms(array(
                "status" => "success",
                "message" => "API configuration deleted successfully"
            ));
        } else {
            ms(array(
                "status" => "error",
                "message" => "Cannot delete API configuration that is in use by active campaigns"
            ));
        }
    }
    
    // ========================================
    // RECIPIENTS
    // ========================================
    
    public function recipients($campaign_ids = ""){
        $campaign = $this->model->get_campaign($campaign_ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $recipients = $this->model->get_recipients($campaign->id, 100, 0);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "recipients" => $recipients
        );
        $this->template->build("recipients/index", $data);
    }
    
    public function ajax_import_from_users(){
        _is_ajax($this->module);
        
        @set_time_limit(300);
        @ini_set('max_execution_time', 300);
        @ini_set('memory_limit', '256M');
        
        $campaign_ids = post("campaign_ids");
        $campaign = $this->model->get_campaign($campaign_ids);
        
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        try {
            $imported = $this->model->import_from_users($campaign->id, [], 0);
            
            $this->model->update_campaign_stats($campaign->id);
            
            if ($imported > 0) {
                ms(array(
                    "status" => "success",
                    "message" => "Successfully imported {$imported} users with WhatsApp numbers"
                ));
            } else {
                ms(array(
                    "status" => "error",
                    "message" => "No users found with WhatsApp numbers or all users already imported"
                ));
            }
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Marketing Import Error: ' . $e->getMessage());
            ms(array(
                "status" => "error",
                "message" => "Error importing users: " . $e->getMessage()
            ));
        }
    }
    
    public function ajax_import_from_csv(){
        _is_ajax($this->module);
        
        $campaign_ids = post("campaign_ids");
        $campaign = $this->model->get_campaign($campaign_ids);
        
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        if(!empty($_FILES['csv_file']['name'])){
            $config['upload_path'] = TEMP_PATH;
            $config['allowed_types'] = 'csv|txt';
            $config['max_size'] = 5000;
            
            $this->load->library('upload', $config);
            
            if($this->upload->do_upload('csv_file')){
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                
                $imported = $this->model->import_from_csv($campaign->id, $file_path);
                
                @unlink($file_path);
                
                $this->model->update_campaign_stats($campaign->id);
                
                ms(array(
                    "status" => "success",
                    "message" => "Imported {$imported} phone numbers successfully"
                ));
            } else {
                ms(array(
                    "status" => "error",
                    "message" => $this->upload->display_errors('', '')
                ));
            }
        } else {
            ms(array(
                "status" => "error",
                "message" => "Please select a CSV file"
            ));
        }
    }
    
    // ========================================
    // REPORTS
    // ========================================
    
    public function reports(){
        $stats = $this->model->get_overall_stats();
        $campaigns = $this->model->get_campaigns(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "stats" => $stats,
            "campaigns" => $campaigns
        );
        $this->template->build("reports/index", $data);
    }
    
    public function export_campaign_report($ids = ""){
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $this->model->update_campaign_stats($campaign->id);
        $campaign = $this->model->get_campaign($ids);
        
        $recipients = $this->model->get_recipients($campaign->id, 10000, 0);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="whatsapp_campaign_' . $campaign->ids . '_report.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['Phone Number', 'Name', 'Status', 'Sent At', 'Error Message']);
        
        foreach($recipients as $recipient){
            fputcsv($output, [
                $recipient->phone_number,
                $recipient->name,
                $recipient->status,
                $recipient->sent_at,
                $recipient->error_message
            ]);
        }
        
        fclose($output);
        exit;
    }
}
