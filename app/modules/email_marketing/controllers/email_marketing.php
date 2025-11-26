<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_marketing extends MX_Controller {
    
    public $module_name;
    public $module;
    public $module_icon;
    
    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Config Module
        $this->module_name = 'Email Marketing';
        $this->module = strtolower(get_class($this));
        $this->module_icon = "fa fa-envelope";
        
        // Check if user is admin
        if (!get_role("admin")) {
            _validation('error', "Permission Denied!");
        }
    }
    
    // ========================================
    // MAIN DASHBOARD
    // ========================================
    
    public function index(){
        // Get overall statistics
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
        $smtp_configs = $this->model->get_smtp_configs(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "templates" => $templates,
            "smtp_configs" => $smtp_configs
        );
        $this->load->view('campaigns/create', $data);
    }
    
    public function ajax_campaign_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $template_id = post("template_id");
        $smtp_config_ids = $this->input->post("smtp_config_ids"); // Array of SMTP IDs
        $sending_limit_hourly = post("sending_limit_hourly");
        $sending_limit_daily = post("sending_limit_daily");
        
        // Validation
        if(empty($name)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        if(empty($template_id) || empty($smtp_config_ids)){
            ms(array(
                "status" => "error",
                "message" => "Please select template and at least one SMTP configuration"
            ));
        }
        
        // Ensure smtp_config_ids is an array of integers
        if(!is_array($smtp_config_ids)){
            $smtp_config_ids = array($smtp_config_ids);
        }
        $smtp_config_ids = array_map('intval', $smtp_config_ids);
        $smtp_config_ids = array_filter($smtp_config_ids); // Remove zeros
        
        if(empty($smtp_config_ids)){
            ms(array(
                "status" => "error",
                "message" => "Please select at least one valid SMTP configuration"
            ));
        }
        
        $campaign_data = array(
            'name' => $name,
            'template_id' => $template_id,
            'smtp_config_id' => $smtp_config_ids[0], // Primary SMTP (first selected) for backward compatibility
            'smtp_config_ids' => json_encode($smtp_config_ids), // All selected SMTPs for rotation
            'smtp_rotation_index' => 0,
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
        $smtp_configs = $this->model->get_smtp_configs(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "templates" => $templates,
            "smtp_configs" => $smtp_configs
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
        $smtp_config_ids = $this->input->post("smtp_config_ids"); // Array of SMTP IDs
        $sending_limit_hourly = post("sending_limit_hourly");
        $sending_limit_daily = post("sending_limit_daily");
        
        if(empty($name)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        if(empty($smtp_config_ids)){
            ms(array(
                "status" => "error",
                "message" => "Please select at least one SMTP configuration"
            ));
        }
        
        // Ensure smtp_config_ids is an array of integers
        if(!is_array($smtp_config_ids)){
            $smtp_config_ids = array($smtp_config_ids);
        }
        $smtp_config_ids = array_map('intval', $smtp_config_ids);
        $smtp_config_ids = array_filter($smtp_config_ids); // Remove zeros
        
        if(empty($smtp_config_ids)){
            ms(array(
                "status" => "error",
                "message" => "Please select at least one valid SMTP configuration"
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'template_id' => $template_id,
            'smtp_config_id' => $smtp_config_ids[0], // Primary SMTP for backward compatibility
            'smtp_config_ids' => json_encode($smtp_config_ids), // All selected SMTPs for rotation
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
        
        // Check if campaign has recipients
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
        
        // Reset failed recipients to pending
        $reset_count = $this->model->reset_failed_recipients($campaign->id);
        
        if($reset_count > 0){
            // Update campaign stats
            $this->model->update_campaign_stats($campaign->id);
            
            // If campaign is completed, set it back to running
            if($campaign->status == 'completed'){
                $this->model->update_campaign($ids, array('status' => 'running'));
            }
            
            ms(array(
                "status" => "success",
                "message" => "Reset {$reset_count} failed email(s) for resending"
            ));
        } else {
            ms(array(
                "status" => "error",
                "message" => "No failed emails found to resend"
            ));
        }
    }
    
    public function ajax_resend_single_email(){
        _is_ajax($this->module);
        
        $recipient_id = post("recipient_id");
        
        if(!$recipient_id){
            ms(array(
                "status" => "error",
                "message" => "Recipient ID is required"
            ));
        }
        
        // Get recipient
        $this->db->where('id', $recipient_id);
        $recipient = $this->db->get('email_recipients')->row();
        
        if(!$recipient){
            ms(array(
                "status" => "error",
                "message" => "Recipient not found"
            ));
        }
        
        // Only allow resending failed emails
        if($recipient->status != 'failed'){
            ms(array(
                "status" => "error",
                "message" => "Only failed emails can be resent"
            ));
        }
        
        // Reset recipient to pending
        $this->db->where('id', $recipient_id);
        $this->db->update('email_recipients', [
            'status' => 'pending',
            'sent_at' => null,
            'error_message' => null,
            'updated_at' => NOW
        ]);
        
        // Update campaign stats
        $this->model->update_campaign_stats($recipient->campaign_id);
        
        // Get campaign
        $this->db->where('id', $recipient->campaign_id);
        $campaign = $this->db->get('email_campaigns')->row();
        
        // If campaign is completed, set it back to running
        if($campaign && $campaign->status == 'completed'){
            $this->db->where('id', $recipient->campaign_id);
            $this->db->update('email_campaigns', array('status' => 'running'));
        }
        
        ms(array(
            "status" => "success",
            "message" => "Email reset for resending"
        ));
    }
    
    public function campaign_details($ids = ""){
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        // Update campaign stats
        $this->model->update_campaign_stats($campaign->id);
        $campaign = $this->model->get_campaign($ids); // Refresh data
        
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
        $subject = post("subject");
        $body = post("body", false); // Don't XSS clean HTML content
        $description = post("description");
        
        if(empty($name) || empty($subject) || empty($body)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $template_data = array(
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
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
        $subject = post("subject");
        $body = post("body", false);
        $description = post("description");
        
        if(empty($name) || empty($subject) || empty($body)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
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
    // SMTP CONFIGURATIONS
    // ========================================
    
    public function smtp($page = 1){
        $page = max(1, (int)$page);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $smtp_configs = $this->model->get_smtp_configs($per_page, $offset);
        $total = $this->model->get_smtp_configs();
        
        $data = array(
            "module" => $this->module,
            "smtp_configs" => $smtp_configs,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("smtp/index", $data);
    }
    
    public function smtp_create(){
        $data = array(
            "module" => $this->module
        );
        $this->load->view('smtp/create', $data);
    }
    
    public function ajax_smtp_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $host = post("host");
        $port = post("port");
        $username = post("username");
        $password = post("password");
        $encryption = post("encryption");
        $from_name = post("from_name");
        $from_email = post("from_email");
        $reply_to = post("reply_to");
        $is_default = post("is_default");
        $status = post("status");
        
        if(empty($name) || empty($host) || empty($port) || empty($username) || empty($from_email)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $smtp_data = array(
            'name' => $name,
            'host' => $host,
            'port' => (int)$port,
            'username' => $username,
            'password' => $password,
            'encryption' => $encryption,
            'from_name' => $from_name,
            'from_email' => $from_email,
            'reply_to' => $reply_to,
            'is_default' => $is_default ? 1 : 0,
            'status' => $status ? 1 : 0
        );
        
        if($this->model->create_smtp_config($smtp_data)){
            ms(array(
                "status" => "success",
                "message" => "SMTP configuration created successfully"
            ));
        }
    }
    
    public function smtp_edit($ids = ""){
        $smtp = $this->model->get_smtp_config($ids);
        if(!$smtp){
            redirect(cn($this->module . "/smtp"));
        }
        
        $data = array(
            "module" => $this->module,
            "smtp" => $smtp
        );
        $this->load->view('smtp/edit', $data);
    }
    
    public function ajax_smtp_edit($ids = ""){
        _is_ajax($this->module);
        
        $smtp = $this->model->get_smtp_config($ids);
        if(!$smtp){
            ms(array(
                "status" => "error",
                "message" => "SMTP configuration not found"
            ));
        }
        
        $name = post("name");
        $host = post("host");
        $port = post("port");
        $username = post("username");
        $password = post("password");
        $encryption = post("encryption");
        $from_name = post("from_name");
        $from_email = post("from_email");
        $reply_to = post("reply_to");
        $is_default = post("is_default");
        $status = post("status");
        
        if(empty($name) || empty($host) || empty($port) || empty($username) || empty($from_email)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'host' => $host,
            'port' => (int)$port,
            'username' => $username,
            'encryption' => $encryption,
            'from_name' => $from_name,
            'from_email' => $from_email,
            'reply_to' => $reply_to,
            'is_default' => $is_default ? 1 : 0,
            'status' => $status ? 1 : 0
        );
        
        // Only update password if provided
        if(!empty($password)){
            $update_data['password'] = $password;
        }
        
        if($this->model->update_smtp_config($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "SMTP configuration updated successfully"
            ));
        }
    }
    
    public function ajax_smtp_delete(){
        _is_ajax($this->module);
        
        $ids = post("ids");
        if($this->model->delete_smtp_config($ids)){
            ms(array(
                "status" => "success",
                "message" => "SMTP configuration deleted successfully"
            ));
        } else {
            ms(array(
                "status" => "error",
                "message" => "Cannot delete SMTP configuration that is in use by active campaigns"
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
        
        // Increase PHP timeout for this operation (importing all users may take longer)
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
            // Import all available users (no limit)
            $imported = $this->model->import_from_users($campaign->id, [], 0);
            
            // Update campaign stats
            $this->model->update_campaign_stats($campaign->id);
            
            if ($imported > 0) {
                ms(array(
                    "status" => "success",
                    "message" => "Successfully imported {$imported} users with order history"
                ));
            } else {
                ms(array(
                    "status" => "error",
                    "message" => "No users found with order history or all users already imported"
                ));
            }
        } catch (Exception $e) {
            log_message('error', 'Email Marketing Import Error: ' . $e->getMessage());
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
        
        // Handle file upload
        if(!empty($_FILES['csv_file']['name'])){
            $config['upload_path'] = TEMP_PATH;
            $config['allowed_types'] = 'csv|txt';
            $config['max_size'] = 5000; // 5MB
            
            $this->load->library('upload', $config);
            
            if($this->upload->do_upload('csv_file')){
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                
                $imported = $this->model->import_from_csv($campaign->id, $file_path);
                
                // Delete uploaded file
                @unlink($file_path);
                
                // Update campaign stats
                $this->model->update_campaign_stats($campaign->id);
                
                ms(array(
                    "status" => "success",
                    "message" => "Imported {$imported} emails successfully"
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
    // TRACKING
    // ========================================
    
    /**
     * Track email opens via tracking pixel
     * Public endpoint - no authentication required
     */
    public function track($token = ""){
        if(empty($token)){
            show_404();
            return;
        }
        
        // Find recipient by tracking token
        $this->db->where('tracking_token', $token);
        $recipient = $this->db->get('email_recipients')->row();
        
        if($recipient && $recipient->status == 'sent'){
            // Update recipient status to opened
            $this->model->update_recipient_status($recipient->id, 'opened');
            
            // Update log if exists
            $this->db->where('recipient_id', $recipient->id);
            $this->db->where('status', 'sent');
            $this->db->update('email_logs', [
                'status' => 'opened',
                'opened_at' => NOW
            ]);
            
            // Update campaign stats
            $this->model->update_campaign_stats($recipient->campaign_id);
        }
        
        // Return 1x1 transparent pixel
        header('Content-Type: image/gif');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        exit;
    }
    
    // ========================================
    // REPORTS
    // ========================================
    
    public function reports(){
        // Get overall statistics for reports page
        $stats = $this->model->get_overall_stats();
        
        // Get all campaigns with stats
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
        
        // Update stats first
        $this->model->update_campaign_stats($campaign->id);
        $campaign = $this->model->get_campaign($ids);
        
        // Get all recipients
        $recipients = $this->model->get_recipients($campaign->id, 10000, 0);
        
        // Create CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="campaign_' . $campaign->ids . '_report.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['Email', 'Name', 'Status', 'Sent At', 'Opened At', 'Error Message']);
        
        // Data
        foreach($recipients as $recipient){
            fputcsv($output, [
                $recipient->email,
                $recipient->name,
                $recipient->status,
                $recipient->sent_at,
                $recipient->opened_at,
                $recipient->error_message
            ]);
        }
        
        fclose($output);
        exit;
    }
}
