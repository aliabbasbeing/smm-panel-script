<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Cron Controller
 * 
 * Handles background email processing with SMTP rotation, domain filtering,
 * and detailed observability. Structured for future job worker migration.
 * 
 * @package Email Marketing
 * @version 2.0.0
 */
class Email_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    // Metrics for observability
    private $metrics = [
        'queue_size' => 0,
        'processed' => 0,
        'sent' => 0,
        'failed' => 0,
        'rejected_domain' => 0,
        'start_time' => null,
        'end_time' => null
    ];
    
    public function __construct(){
        parent::__construct();
        $this->load->model('email_marketing/email_marketing_model', 'email_model');
        $this->load->library('email');
        $this->load->library('cron_logger');
        
        // Security token for cron access
        $this->requiredToken = get_option('email_cron_token', md5('email_marketing_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/email_cron_last_run.lock';
        
        // Initialize metrics
        $this->metrics['start_time'] = microtime(true);
    }
    
    /**
     * Main cron entry point
     * URL: /cron/email_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID (optional)
     */
    public function run(){
        // Log start of cron
        log_message('debug', '========== EMAIL CRON START ==========');
        
        $log_id = $this->cron_logger->start('cron/email_marketing');
        // Verify token
        $token = $this->input->get('token', true);
        if(!$token || !hash_equals($this->requiredToken, $token)){
            log_message('debug', 'Token verification failed');
            $this->cron_logger->end($log_id, 'Failed', 403, 'Invalid or missing token');
            show_404();
            return;
        }
        
        log_message('debug', 'Token verified OK');
        
        // Get optional campaign_id for campaign-specific cron
        $campaign_id = $this->input->get('campaign_id', true);
        
        // Rate limiting - prevent running too frequently
        $lockFileKey = $campaign_id ? 'campaign_' . $campaign_id : 'all';
        $lockFile = APPPATH.'cache/email_cron_' . $lockFileKey . '.lock';
        
        $minInterval = 60; // 60 seconds minimum between runs
        if(file_exists($lockFile)){
            $lastRun = (int)@file_get_contents($lockFile);
            $now = time();
            if($lastRun && ($now - $lastRun) < $minInterval){
                log_message('debug', sprintf('Rate limited: last_run=%d, now=%d', $lastRun, $now));
                $this->respond([
                    'status' => 'rate_limited',
                    'message' => 'Cron is rate limited. Please wait.',
                    'retry_after_sec' => $minInterval - ($now - $lastRun),
                    'campaign_id' => $campaign_id,
                    'time' => date('c')
                ]);
                return;
            }
        }
        
        // Update lock file with atomic write
        $this->atomic_lock_update($lockFile);
        
        // Process emails
        log_message('debug', 'Starting process_emails()');
        $result = $this->process_emails($campaign_id);
        log_message('debug', sprintf('process_emails() completed: %s', json_encode($result)));
        
        // Update metrics
        $this->update_cron_metrics();
        
        // Log the result
        $status = ($result['status'] == 'success' || $result['status'] == 'info') ? 'Success' : 'Failed';
        $response_code = ($status == 'Success') ? 200 : 500;
        $message = $result['message'] . ' (Sent: ' . $result['emails_sent'] . ', Failed: ' . $this->metrics['failed'] . ')';
        $this->cron_logger->end($log_id, $status, $response_code, $message);
        
        log_message('debug', '========== EMAIL CRON END ==========');
        
        $this->respond($result);
    }
    
    /**
     * Atomic lock file update to prevent race conditions
     */
    private function atomic_lock_update($lockFile) {
        $fp = @fopen($lockFile, 'c+');
        if ($fp && flock($fp, LOCK_EX | LOCK_NB)) {
            ftruncate($fp, 0);
            fwrite($fp, time());
            flock($fp, LOCK_UN);
            fclose($fp);
        } elseif ($fp) {
            fclose($fp);
        }
    }
    
    /**
     * Update cron metrics in settings table for observability
     */
    private function update_cron_metrics() {
        $this->metrics['end_time'] = microtime(true);
        $duration = round($this->metrics['end_time'] - $this->metrics['start_time'], 3);
        
        $this->email_model->update_setting('last_cron_run', date('Y-m-d H:i:s'));
        $this->email_model->update_setting('last_cron_duration_sec', $duration);
        $this->email_model->update_setting('last_cron_sent', $this->metrics['sent']);
        $this->email_model->update_setting('last_cron_failed', $this->metrics['failed']);
        $this->email_model->update_setting('last_cron_rejected_domain', $this->metrics['rejected_domain']);
    }
    
    /**
     * Process pending emails
     * Structured for future job worker migration - this method can be called
     * from any execution context (cron, queue worker, CLI)
     * 
     * @param string $campaign_id Optional campaign ID to process specific campaign only
     * @return array Result array with status and metrics
     */
    private function process_emails($campaign_id = null){
        log_message('debug', sprintf('process_emails() called with campaign_id=%s', $campaign_id ?: 'ALL'));
        
        // Get running campaigns
        $this->email_model->db->where('status', 'running');
        
        // If campaign_id specified, filter by it
        if($campaign_id){
            $this->email_model->db->where('ids', $campaign_id);
        }
        
        $campaigns = $this->email_model->db->get('email_campaigns')->result();
        
        log_message('debug', sprintf('Found %d running campaigns', count($campaigns)));
        
        if(empty($campaigns)){
            log_message('debug', 'No running campaigns found - returning');
            return [
                'status' => 'info',
                'message' => $campaign_id ? 'No active campaign found with ID: ' . $campaign_id : 'No active campaign found',
                'campaign_id' => $campaign_id,
                'campaigns_checked' => 0,
                'emails_sent' => 0,
                'emails_failed' => 0,
                'emails_rejected_domain' => 0,
                'metrics' => $this->metrics,
                'time' => date('c')
            ];
        }
        
        // Log campaign details
        foreach($campaigns as $c) {
            log_message('debug', sprintf(
                'Campaign: id=%d, ids=%s, name=%s, smtp_config_id=%s, smtp_config_ids=%s, smtp_rotation_index=%s',
                $c->id,
                $c->ids,
                $c->name,
                isset($c->smtp_config_id) ? $c->smtp_config_id : 'NOT_SET',
                isset($c->smtp_config_ids) ? $c->smtp_config_ids : 'NOT_SET',
                isset($c->smtp_rotation_index) ? $c->smtp_rotation_index : 'NOT_SET'
            ));
        }
        
        $totalSent = 0;
        $campaignsProcessed = 0;
        
        foreach($campaigns as $campaign){
            // Check sending limits
            if(!$this->can_send_email($campaign)){
                log_message('debug', sprintf('Campaign %d: skipped (rate limited)', $campaign->id));
                continue;
            }
            
            // Get next pending recipient (already sorted by priority, then created_at)
            $recipient = $this->email_model->get_next_pending_recipient($campaign->id);
            
            if(!$recipient){
                log_message('debug', sprintf('Campaign %d: no pending recipients, marking as completed', $campaign->id));
                // No more recipients - mark campaign as completed
                $this->email_model->update_campaign($campaign->ids, [
                    'status' => 'completed',
                    'completed_at' => NOW
                ]);
                $campaignsProcessed++;
                continue;
            }
            
            log_message('debug', sprintf('Campaign %d: processing recipient %d (%s)', $campaign->id, $recipient->id, $recipient->email));
            
            $this->metrics['queue_size']++;
            
            // Process the email through our sending engine
            $sent = $this->process_single_email($campaign, $recipient);
            
            if($sent){
                $totalSent++;
                $this->metrics['sent']++;
                
                // Update campaign last sent time
                $this->email_model->update_campaign($campaign->ids, [
                    'last_sent_at' => NOW
                ]);
            }
            
            $campaignsProcessed++;
            $this->metrics['processed']++;
            
            // Update campaign stats after each email
            $this->email_model->update_campaign_stats($campaign->id);
        }
        
        return [
            'status' => 'success',
            'message' => 'Email processing completed',
            'campaign_id' => $campaign_id,
            'campaigns_checked' => count($campaigns),
            'campaigns_processed' => $campaignsProcessed,
            'emails_sent' => $totalSent,
            'emails_failed' => $this->metrics['failed'],
            'emails_rejected_domain' => $this->metrics['rejected_domain'],
            'metrics' => $this->metrics,
            'time' => date('c')
        ];
    }
    
    /**
     * Check if campaign can send email based on limits
     */
    private function can_send_email($campaign){
        $now = time();
        
        // Check hourly limit
        if($campaign->sending_limit_hourly > 0){
            $hourAgo = date('Y-m-d H:i:s', $now - 3600);
            $this->email_model->db->where('campaign_id', $campaign->id);
            $this->email_model->db->where('sent_at >', $hourAgo);
            $this->email_model->db->where('status', 'sent');
            $sentLastHour = $this->email_model->db->count_all_results('email_recipients');
            
            if($sentLastHour >= $campaign->sending_limit_hourly){
                return false;
            }
        }
        
        // Check daily limit
        if($campaign->sending_limit_daily > 0){
            $dayAgo = date('Y-m-d H:i:s', $now - 86400);
            $this->email_model->db->where('campaign_id', $campaign->id);
            $this->email_model->db->where('sent_at >', $dayAgo);
            $this->email_model->db->where('status', 'sent');
            $sentLastDay = $this->email_model->db->count_all_results('email_recipients');
            
            if($sentLastDay >= $campaign->sending_limit_daily){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Process a single email - this is the core sending engine
     * Designed to be callable from any context (cron, queue worker, etc.)
     * 
     * @param object $campaign Campaign object
     * @param object $recipient Recipient object
     * @return bool True if sent successfully
     */
    private function process_single_email($campaign, $recipient) {
        $start_time = microtime(true);
        
        log_message('debug', sprintf(
            '=== PROCESSING EMAIL: campaign_id=%d, recipient_id=%d, email=%s ===',
            $campaign->id,
            $recipient->id,
            $recipient->email
        ));
        
        try {
            // Step 1: Domain filtering - configurable via settings
            if (!$this->validate_email_domain($recipient->email)) {
                $time_taken = round((microtime(true) - $start_time) * 1000, 2); // ms
                $this->reject_invalid_domain($campaign, $recipient, $time_taken);
                $this->metrics['rejected_domain']++;
                return false;
            }
            
            // Step 2: Get template
            $this->email_model->db->where('id', $campaign->template_id);
            $template = $this->email_model->db->get('email_templates')->row();
            
            if (!$template) {
                $time_taken = round((microtime(true) - $start_time) * 1000, 2);
                $this->log_failed_with_timing($campaign, $recipient, 'Template not found', null, $time_taken);
                $this->metrics['failed']++;
                return false;
            }
            
            // Step 3: Get SMTP configs for mandatory round-robin rotation
            $smtp_ids = $this->get_smtp_ids_for_campaign($campaign);
            
            log_message('debug', sprintf(
                'SMTP IDs for campaign: %s (count: %d)',
                implode(',', $smtp_ids),
                count($smtp_ids)
            ));
            
            if (empty($smtp_ids)) {
                $time_taken = round((microtime(true) - $start_time) * 1000, 2);
                $this->log_failed_with_timing($campaign, $recipient, 'No SMTP configurations available', null, $time_taken);
                $this->metrics['failed']++;
                return false;
            }
            
            // Step 4: Prepare template variables
            $variables = $this->prepare_template_variables($recipient);
            
            // Step 5: Process template
            $subject = $this->email_model->process_template_variables($template->subject, $variables);
            $body = $this->email_model->process_template_variables($template->body, $variables);
            
            // Step 6: Add tracking pixel if enabled
            if ($this->email_model->get_setting('enable_open_tracking', 1) == 1) {
                $body .= $variables['tracking_pixel'];
            }
            
            // Step 7: MANDATORY Round-Robin SMTP rotation
            // Always rotate to the next SMTP regardless of success/failure
            return $this->send_with_smtp_rotation($campaign, $recipient, $subject, $body, $smtp_ids, $start_time);
            
        } catch (Exception $e) {
            $time_taken = round((microtime(true) - $start_time) * 1000, 2);
            log_message('error', sprintf(
                'Email processing exception: %s',
                $e->getMessage()
            ));
            $this->log_failed_with_timing($campaign, $recipient, $e->getMessage(), null, $time_taken);
            $this->metrics['failed']++;
            return false;
        }
    }
    
    /**
     * Validate email domain against configured allowed domains
     * @param string $email Email address to validate
     * @return bool True if domain is allowed
     */
    private function validate_email_domain($email) {
        if (empty($email)) {
            return false;
        }
        
        // Get domain filter setting (default: gmail_only for backward compatibility)
        $domain_filter = $this->email_model->get_setting('email_domain_filter', 'gmail_only');
        
        // If filter is disabled, allow all domains
        if ($domain_filter === 'disabled') {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        // Get allowed domains from settings (comma-separated)
        $allowed_domains = $this->email_model->get_setting('email_allowed_domains', 'gmail.com');
        
        // Parse allowed domains
        $domains = array_map('trim', explode(',', strtolower($allowed_domains)));
        
        // Extract domain from email
        $email = strtolower(trim($email));
        $email_domain = substr(strrchr($email, "@"), 1);
        
        return in_array($email_domain, $domains);
    }
    
    /**
     * Reject and delete email with invalid domain
     */
    private function reject_invalid_domain($campaign, $recipient, $time_taken) {
        $allowed_domains = $this->email_model->get_setting('email_allowed_domains', 'gmail.com');
        $error_msg = "Email rejected: Domain not in allowed list ({$allowed_domains})";
        
        // Update recipient status to failed
        $this->email_model->update_recipient_status($recipient->id, 'failed', $error_msg);
        
        // Add detailed log entry
        $this->email_model->add_log_with_timing(
            $campaign->id,
            $recipient->id,
            $recipient->email,
            'Domain Filter',
            'failed',
            $error_msg,
            null,
            $time_taken
        );
    }
    
    /**
     * Send email with mandatory SMTP rotation
     * Every email uses the next SMTP in rotation, even on success
     */
    private function send_with_smtp_rotation($campaign, $recipient, $subject, $body, $smtp_ids, $start_time) {
        // CRITICAL: Fetch the latest smtp_rotation_index from DB to ensure proper rotation
        // This ensures correct rotation when multiple emails are sent in one cron run
        $current_index = $this->get_current_rotation_index($campaign->id);
        
        $total_smtps = count($smtp_ids);
        $attempts = 0;
        $last_error = '';
        $last_smtp_id = null;
        
        // Debug logging for rotation
        log_message('debug', sprintf(
            'SMTP Rotation Start: campaign_id=%d, current_index=%d, total_smtps=%d, smtp_ids=%s',
            $campaign->id,
            $current_index,
            $total_smtps,
            json_encode($smtp_ids)
        ));
        
        // Try each SMTP in rotation order, starting from current_index
        while ($attempts < $total_smtps) {
            $smtp_index = ($current_index + $attempts) % $total_smtps;
            $smtp_id = $smtp_ids[$smtp_index];
            $last_smtp_id = $smtp_id;
            
            log_message('debug', sprintf(
                'SMTP Attempt: attempt=%d, smtp_index=%d, smtp_id=%d',
                $attempts,
                $smtp_index,
                $smtp_id
            ));
            
            // Get SMTP config
            $this->email_model->db->where('id', $smtp_id);
            $smtp = $this->email_model->db->get('email_smtp_configs')->row();
            
            if (!$smtp || $smtp->status != 1) {
                $attempts++;
                $last_error = "SMTP ID {$smtp_id} not found or disabled";
                continue; // Skip to next SMTP
            }
            
            // Try sending with this SMTP
            $send_start = microtime(true);
            $result = $this->try_send_email($smtp, $recipient, $subject, $body);
            $send_time = round((microtime(true) - $send_start) * 1000, 2);
            $total_time = round((microtime(true) - $start_time) * 1000, 2);
            
            // MANDATORY: Always rotate to next SMTP for next email (round-robin)
            // This happens regardless of success or failure
            $next_index = ($smtp_index + 1) % $total_smtps;
            $this->email_model->update_campaign_rotation_index($campaign->id, $next_index);
            
            log_message('debug', sprintf(
                'SMTP Send Result: success=%s, smtp_id=%d, next_rotation_index=%d',
                $result['success'] ? 'true' : 'false',
                $smtp->id,
                $next_index
            ));
            
            if ($result['success']) {
                // Update recipient status
                $this->email_model->update_recipient_status($recipient->id, 'sent');
                
                // Add detailed log with timing and SMTP info
                log_message('debug', sprintf(
                    'Logging sent email: campaign_id=%d, recipient_id=%d, smtp_id=%d',
                    $campaign->id,
                    $recipient->id,
                    $smtp->id
                ));
                
                $this->email_model->add_log_with_timing(
                    $campaign->id,
                    $recipient->id,
                    $recipient->email,
                    $subject,
                    'sent',
                    null,
                    (int)$smtp->id,
                    $total_time
                );
                
                return true;
            } else {
                $last_error = "SMTP '{$smtp->name}': " . $result['error'];
                $attempts++;
                // Continue to try next SMTP as fallback
            }
        }
        
        // All SMTPs failed
        $total_time = round((microtime(true) - $start_time) * 1000, 2);
        $this->log_failed_with_timing(
            $campaign, 
            $recipient, 
            "All SMTP servers failed. Last error: " . $last_error,
            $last_smtp_id,
            $total_time
        );
        $this->metrics['failed']++;
        return false;
    }
    
    /**
     * Get current SMTP rotation index from database
     * This fetches only the rotation index field for efficiency
     * 
     * @param int $campaign_id Campaign ID
     * @return int Current rotation index (0 if not found)
     */
    private function get_current_rotation_index($campaign_id) {
        $this->email_model->db->select('smtp_rotation_index');
        $this->email_model->db->where('id', $campaign_id);
        $result = $this->email_model->db->get('email_campaigns')->row();
        return $result ? (int)$result->smtp_rotation_index : 0;
    }
    
    /**
     * Get SMTP IDs for a campaign (supports both new multi-SMTP and legacy single SMTP)
     */
    private function get_smtp_ids_for_campaign($campaign){
        // Debug logging
        log_message('debug', sprintf(
            'get_smtp_ids_for_campaign: campaign_id=%d, smtp_config_ids=%s, smtp_config_id=%s',
            $campaign->id,
            isset($campaign->smtp_config_ids) ? $campaign->smtp_config_ids : 'NOT_SET',
            isset($campaign->smtp_config_id) ? $campaign->smtp_config_id : 'NOT_SET'
        ));
        
        // Try to get multiple SMTP IDs first (new multi-SMTP feature)
        if(!empty($campaign->smtp_config_ids)){
            $smtp_ids = json_decode($campaign->smtp_config_ids, true);
            log_message('debug', sprintf(
                'Parsed smtp_config_ids JSON: %s',
                json_encode($smtp_ids)
            ));
            if(is_array($smtp_ids) && !empty($smtp_ids)){
                $result = array_map('intval', $smtp_ids);
                log_message('debug', sprintf(
                    'Using multi-SMTP IDs: %s',
                    implode(',', $result)
                ));
                return $result;
            }
        }
        
        // Fallback to single SMTP ID for backward compatibility
        if(!empty($campaign->smtp_config_id)){
            log_message('debug', sprintf(
                'Using single SMTP ID (fallback): %d',
                $campaign->smtp_config_id
            ));
            return array((int)$campaign->smtp_config_id);
        }
        
        log_message('error', sprintf(
            'No SMTP IDs found for campaign %d',
            $campaign->id
        ));
        return array();
    }
    
    /**
     * Prepare template variables for a recipient
     */
    private function prepare_template_variables($recipient){
        $variables = [];
        
        // Add custom data if available
        if($recipient->custom_data){
            $customData = json_decode($recipient->custom_data, true);
            if(is_array($customData)){
                $variables = $customData;
            }
        }
        
        // Add default recipient data
        $variables['email'] = $recipient->email;
        $variables['name'] = $recipient->name ?: 'User';
        $variables['username'] = $recipient->name ?: 'User';
        
        // Add tracking link
        $trackingUrl = base_url('email_marketing/track/' . $recipient->tracking_token);
        $variables['tracking_pixel'] = '<img src="' . $trackingUrl . '" width="1" height="1" />';
        
        return $variables;
    }
    
    /**
     * Try to send email using a specific SMTP configuration
     * @return array ['success' => bool, 'error' => string|null]
     */
    private function try_send_email($smtp, $recipient, $subject, $body){
        try {
            // Configure email
            $config = [
                'protocol' => 'smtp',
                'smtp_host' => $smtp->host,
                'smtp_port' => $smtp->port,
                'smtp_user' => $smtp->username,
                'smtp_pass' => $smtp->password,
                'smtp_crypto' => $smtp->encryption,
                'mailtype' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n",       // Fix for RFC compliance
                'crlf' => "\r\n",          // Fix for RFC compliance  
                'wordwrap' => TRUE,        // Enable word wrapping to prevent long lines
                'wrapchars' => 78          // Wrap at 78 characters (RFC recommended)
            ];
            
            $this->email->clear(); // Clear any previous email data before initialize
            $this->email->initialize($config);
            $this->email->from($smtp->from_email, $smtp->from_name);
            $this->email->to($recipient->email);
            
            if($smtp->reply_to){
                $this->email->reply_to($smtp->reply_to);
            }
            
            $this->email->subject($subject);
            $this->email->message($body);
            
            // Send email
            if($this->email->send()){
                return ['success' => true, 'error' => null];
            } else {
                // Get error (using print_debugger without params for compatibility)
                $error = $this->email->print_debugger();
                return ['success' => false, 'error' => $error];
            }
        } catch(Exception $e){
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Log failed email with timing information
     */
    private function log_failed_with_timing($campaign, $recipient, $error, $smtp_id = null, $time_taken = 0) {
        // Update recipient status
        $this->email_model->update_recipient_status($recipient->id, 'failed', $error);
        
        // Add log with timing
        $this->email_model->add_log_with_timing(
            $campaign->id,
            $recipient->id,
            $recipient->email,
            'Failed',
            'failed',
            $error,
            $smtp_id,
            $time_taken
        );
    }
    
    /**
     * JSON response with metrics
     */
    private function respond($data){
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}