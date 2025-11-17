<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('email_marketing/email_marketing_model', 'email_model');
        $this->load->library('email');
        
        // Security token for cron access
        $this->requiredToken = get_option('email_cron_token', md5('email_marketing_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/email_cron_last_run.lock';
    }
    
    /**
     * Main cron entry point
     * URL: /cron/email_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID (optional)
     */
    public function run(){
        // Verify token
        $token = $this->input->get('token', true);
        if(!$token || !hash_equals($this->requiredToken, $token)){
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
     * Send individual email
     */
    private function send_email($campaign, $recipient){
        try {
            // Get template
            $this->email_model->db->where('id', $campaign->template_id);
            $template = $this->email_model->db->get('email_templates')->row();
            
            if(!$template){
                $this->log_failed($campaign, $recipient, 'Template not found');
                return false;
            }
            
            // Get SMTP config
            $this->email_model->db->where('id', $campaign->smtp_config_id);
            $smtp = $this->email_model->db->get('email_smtp_configs')->row();
            
            if(!$smtp || $smtp->status != 1){
                $this->log_failed($campaign, $recipient, 'SMTP configuration not found or disabled');
                return false;
            }
            
            // Prepare template variables
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
            
            // Process template
            $subject = $this->email_model->process_template_variables($template->subject, $variables);
            $body = $this->email_model->process_template_variables($template->body, $variables);
            
            // Add tracking pixel to body if enabled
            if($this->email_model->get_setting('enable_open_tracking', 1) == 1){
                $body .= $variables['tracking_pixel'];
            }
            
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
                // Update recipient status
                $this->email_model->update_recipient_status($recipient->id, 'sent');
                
                // Add log
                $this->email_model->add_log(
                    $campaign->id,
                    $recipient->id,
                    $recipient->email,
                    $subject,
                    'sent'
                );
                
                return true;
            } else {
                // Get error
                $error = $this->email->print_debugger();
                $this->log_failed($campaign, $recipient, $error);
                return false;
            }
            
        } catch(Exception $e){
            $this->log_failed($campaign, $recipient, $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log failed email
     */
    private function log_failed($campaign, $recipient, $error){
        // Update recipient status
        $this->email_model->update_recipient_status($recipient->id, 'failed', $error);
        
        // Add log
        $this->email_model->add_log(
            $campaign->id,
            $recipient->id,
            $recipient->email,
            'Failed',
            'failed',
            $error
        );
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