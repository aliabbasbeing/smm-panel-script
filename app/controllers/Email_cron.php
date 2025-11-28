<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('email_marketing/email_marketing_model', 'email_model');
        $this->load->library('email');
                $this->load->library('cron_logger');
        
        // Security token for cron access
        $this->requiredToken = get_option('email_cron_token', md5('email_marketing_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/email_cron_last_run.lock';
    }
    
    /**
     * Main cron entry point
     * URL: /cron/email_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID (optional)
     */
    public function run(){
        $log_id = $this->cron_logger->start('cron/email_marketing');
        // Verify token
        $token = $this->input->get('token', true);
        if(!$token || !hash_equals($this->requiredToken, $token)){
            $this->cron_logger->end($log_id, 'Failed', 403, 'Invalid or missing token');
            show_404();
            return;
        }
        
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
        
        // Update lock file
        @file_put_contents($lockFile, time());
        
        // Process emails
        $result = $this->process_emails($campaign_id);
        // Log the result
        $status = ($result['status'] == 'success' || $result['status'] == 'info') ? 'Success' : 'Failed';
        $response_code = ($status == 'Success') ? 200 : 500;
        $message = $result['message'] . ' (Sent: ' . $result['emails_sent'] . ')';
        $this->cron_logger->end($log_id, $status, $response_code, $message);
        
        $this->respond($result);
    }
    
    /**
     * Process pending emails
     * @param string $campaign_id Optional campaign ID to process specific campaign only
     */
    private function process_emails($campaign_id = null){
        // Get running campaigns
        $this->email_model->db->where('status', 'running');
        
        // If campaign_id specified, filter by it
        if($campaign_id){
            $this->email_model->db->where('ids', $campaign_id);
        }
        
        $campaigns = $this->email_model->db->get('email_campaigns')->result();
        
        if(empty($campaigns)){
            return [
                'status' => 'info',
                'message' => $campaign_id ? 'No active campaign found with ID: ' . $campaign_id : 'No active campaign found',
                'campaign_id' => $campaign_id,
                'campaigns_checked' => 0,
                'emails_sent' => 0,
                'time' => date('c')
            ];
        }
        
        $totalSent = 0;
        $campaignsProcessed = 0;
        
        foreach($campaigns as $campaign){
            // Check sending limits
            if(!$this->can_send_email($campaign)){
                continue;
            }
            
            // Get next pending recipient
            $recipient = $this->email_model->get_next_pending_recipient($campaign->id);
            
            if(!$recipient){
                // No more recipients - mark campaign as completed
                $this->email_model->update_campaign($campaign->ids, [
                    'status' => 'completed',
                    'completed_at' => NOW
                ]);
                $campaignsProcessed++;
                continue;
            }
            
            // Send email
            $sent = $this->send_email($campaign, $recipient);
            
            if($sent){
                $totalSent++;
                $campaignsProcessed++;
                
                // Update campaign last sent time
                $this->email_model->update_campaign($campaign->ids, [
                    'last_sent_at' => NOW
                ]);
                
                // Update campaign stats
                $this->email_model->update_campaign_stats($campaign->id);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'Email processing completed',
            'campaign_id' => $campaign_id,
            'campaigns_checked' => count($campaigns),
            'campaigns_processed' => $campaignsProcessed,
            'emails_sent' => $totalSent,
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
     * Send individual email with true round-robin SMTP rotation
     * 
     * SMTP Rotation Logic:
     * - Each email uses the next SMTP in the rotation list
     * - Rotation advances on EVERY email attempt (not just on failure)
     * - If the selected SMTP fails, it falls back to the next available SMTP
     * - Rotation index is always updated after each send attempt to ensure fair distribution
     */
    private function send_email($campaign, $recipient){
        try {
            // Gmail domain filter: Only allow @gmail.com emails
            if(!$this->is_gmail_email($recipient->email)){
                // Delete non-gmail email from queue and log as rejected
                $this->email_model->update_recipient_status($recipient->id, 'failed', 'Email rejected: Only @gmail.com addresses are allowed');
                $this->email_model->add_log(
                    $campaign->id,
                    $recipient->id,
                    $recipient->email,
                    'Domain Filter',
                    'failed',
                    'Email rejected: Only @gmail.com addresses are allowed',
                    null
                );
                return false;
            }
            
            // Get template
            $this->email_model->db->where('id', $campaign->template_id);
            $template = $this->email_model->db->get('email_templates')->row();
            
            if(!$template){
                $this->log_failed($campaign, $recipient, 'Template not found');
                return false;
            }
            
            // Get SMTP configs for rotation
            $smtp_ids = $this->get_smtp_ids_for_campaign($campaign);
            
            if(empty($smtp_ids)){
                $this->log_failed($campaign, $recipient, 'No SMTP configurations available');
                return false;
            }
            
            // Prepare template variables (done once, reused for retries)
            $variables = $this->prepare_template_variables($recipient);
            
            // Process template
            $subject = $this->email_model->process_template_variables($template->subject, $variables);
            $body = $this->email_model->process_template_variables($template->body, $variables);
            
            // Add tracking pixel to body if enabled
            if($this->email_model->get_setting('enable_open_tracking', 1) == 1){
                $body .= $variables['tracking_pixel'];
            }
            
            // Get current rotation index - this determines which SMTP to use for THIS email
            $current_index = isset($campaign->smtp_rotation_index) ? (int)$campaign->smtp_rotation_index : 0;
            $total_smtps = count($smtp_ids);
            
            // Ensure index is within bounds
            $current_index = $current_index % $total_smtps;
            
            // IMPORTANT: Advance rotation index IMMEDIATELY for true round-robin
            // This ensures the next email will use the next SMTP regardless of success/failure
            $next_rotation_index = ($current_index + 1) % $total_smtps;
            $this->email_model->update_campaign_rotation_index($campaign->id, $next_rotation_index);
            
            // Log the rotation decision
            log_message('info', sprintf(
                'Email Marketing: Campaign %d - Using SMTP index %d (ID: %d) for email to %s. Next email will use index %d.',
                $campaign->id,
                $current_index,
                $smtp_ids[$current_index],
                $recipient->email,
                $next_rotation_index
            ));
            
            // Try sending with the selected SMTP first, then fallback to others if it fails
            $attempts = 0;
            $last_error = '';
            $last_attempted_smtp_id = null;
            
            // Try each SMTP starting from current_index, with fallback to others
            while($attempts < $total_smtps){
                $smtp_index = ($current_index + $attempts) % $total_smtps;
                $smtp_id = $smtp_ids[$smtp_index];
                
                // Get SMTP config
                $this->email_model->db->where('id', $smtp_id);
                $smtp = $this->email_model->db->get('email_smtp_configs')->row();
                
                if(!$smtp || $smtp->status != 1){
                    $attempts++;
                    $last_error = "SMTP ID {$smtp_id} not found or disabled";
                    log_message('warning', sprintf(
                        'Email Marketing: Campaign %d - SMTP ID %d unavailable, trying fallback. Error: %s',
                        $campaign->id,
                        $smtp_id,
                        $last_error
                    ));
                    continue; // Skip to next SMTP
                }
                
                // Track the last attempted SMTP for logging if all fail
                $last_attempted_smtp_id = (int)$smtp->id;
                
                // Try sending with this SMTP
                $result = $this->try_send_email($smtp, $recipient, $subject, $body);
                
                if($result['success']){
                    // Update recipient status
                    $this->email_model->update_recipient_status($recipient->id, 'sent');
                    
                    // Add log with SMTP info
                    $this->email_model->add_log(
                        $campaign->id,
                        $recipient->id,
                        $recipient->email,
                        $subject,
                        'sent',
                        null,
                        $last_attempted_smtp_id
                    );
                    
                    // Update SMTP usage statistics
                    $this->email_model->increment_smtp_usage($smtp->id, true);
                    
                    // Log success with SMTP details
                    log_message('info', sprintf(
                        'Email Marketing: Campaign %d - Email sent successfully to %s using SMTP "%s" (ID: %d)',
                        $campaign->id,
                        $recipient->email,
                        $smtp->name,
                        $smtp->id
                    ));
                    
                    return true;
                } else {
                    $last_error = "SMTP '{$smtp->name}' (ID: {$smtp->id}): " . $result['error'];
                    
                    // Update SMTP failure statistics
                    $this->email_model->increment_smtp_usage($smtp->id, false);
                    
                    log_message('error', sprintf(
                        'Email Marketing: Campaign %d - SMTP "%s" (ID: %d) failed for %s. Error: %s',
                        $campaign->id,
                        $smtp->name,
                        $smtp->id,
                        $recipient->email,
                        $result['error']
                    ));
                    
                    $attempts++;
                    // Continue to try next SMTP as fallback
                }
            }
            
            // All SMTPs failed - log with the last attempted SMTP
            $this->log_failed($campaign, $recipient, "All SMTP servers failed. Last error: " . $last_error, $last_attempted_smtp_id);
            return false;
            
        } catch(Exception $e){
            log_message('error', sprintf(
                'Email Marketing: Campaign %d - Exception while sending to %s: %s',
                $campaign->id,
                $recipient->email,
                $e->getMessage()
            ));
            $this->log_failed($campaign, $recipient, $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get SMTP IDs for a campaign (supports both new multi-SMTP and legacy single SMTP)
     */
    private function get_smtp_ids_for_campaign($campaign){
        // Try to get multiple SMTP IDs first
        if(!empty($campaign->smtp_config_ids)){
            $smtp_ids = json_decode($campaign->smtp_config_ids, true);
            if(is_array($smtp_ids) && !empty($smtp_ids)){
                return array_map('intval', $smtp_ids);
            }
        }
        
        // Fallback to single SMTP ID for backward compatibility
        if(!empty($campaign->smtp_config_id)){
            return array((int)$campaign->smtp_config_id);
        }
        
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
     * Check if email is a Gmail address
     * @param string $email Email address to check
     * @return bool True if email ends with @gmail.com
     */
    private function is_gmail_email($email){
        if(empty($email)){
            return false;
        }
        $email = strtolower(trim($email));
        $gmail_domain = '@gmail.com';
        return (substr($email, -strlen($gmail_domain)) === $gmail_domain);
    }
    
    /**
     * Log failed email with optional SMTP ID
     * @param object $campaign Campaign object
     * @param object $recipient Recipient object
     * @param string $error Error message
     * @param int|null $smtp_id SMTP config ID that was attempted (optional)
     */
    private function log_failed($campaign, $recipient, $error, $smtp_id = null){
        // Update recipient status
        $this->email_model->update_recipient_status($recipient->id, 'failed', $error);
        
        // Add log with SMTP ID if provided
        $this->email_model->add_log(
            $campaign->id,
            $recipient->id,
            $recipient->email,
            'Failed',
            'failed',
            $error,
            $smtp_id
        );
        
        // Log the failure for monitoring
        log_message('error', sprintf(
            'Email Marketing: Campaign %d - Failed to send email to %s. SMTP ID: %s. Error: %s',
            $campaign->id,
            $recipient->email,
            $smtp_id !== null ? $smtp_id : 'N/A',
            $error
        ));
    }
    
    /**
     * JSON response
     */
    private function respond($data){
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}