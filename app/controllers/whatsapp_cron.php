<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('whatsapp_marketing/whatsapp_marketing_model', 'whatsapp_model');
        
        // Security token for cron access
        $this->requiredToken = get_option('whatsapp_cron_token', md5('whatsapp_marketing_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/whatsapp_cron_last_run.lock';
    }
    
    /**
     * Main cron entry point
     * URL: /whatsapp_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID (optional)
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
        
        // Rate limiting
        $lockFileKey = $campaign_id ? 'campaign_' . $campaign_id : 'all';
        $lockFile = APPPATH.'cache/whatsapp_cron_' . $lockFileKey . '.lock';
        
        $minInterval = 60;
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
        
        @file_put_contents($lockFile, time());
        
        $result = $this->process_messages($campaign_id);
        
        $this->respond($result);
    }
    
    /**
     * Process pending messages
     */
    private function process_messages($campaign_id = null){
        $this->whatsapp_model->db->where('status', 'running');
        
        if($campaign_id){
            $this->whatsapp_model->db->where('ids', $campaign_id);
        }
        
        $campaigns = $this->whatsapp_model->db->get('whatsapp_campaigns')->result();
        
        if(empty($campaigns)){
            return [
                'status' => 'info',
                'message' => $campaign_id ? 'No active campaign found with ID: ' . $campaign_id : 'No active campaign found',
                'campaign_id' => $campaign_id,
                'campaigns_checked' => 0,
                'messages_sent' => 0,
                'time' => date('c')
            ];
        }
        
        $totalSent = 0;
        $campaignsProcessed = 0;
        
        foreach($campaigns as $campaign){
            if(!$this->can_send_message($campaign)){
                continue;
            }
            
            $recipient = $this->whatsapp_model->get_next_pending_recipient($campaign->id);
            
            if(!$recipient){
                $this->whatsapp_model->update_campaign($campaign->ids, [
                    'status' => 'completed',
                    'completed_at' => NOW
                ]);
                $campaignsProcessed++;
                continue;
            }
            
            $sent = $this->send_message($campaign, $recipient);
            
            if($sent){
                $totalSent++;
                $campaignsProcessed++;
                
                $this->whatsapp_model->update_campaign($campaign->ids, [
                    'last_sent_at' => NOW
                ]);
                
                $this->whatsapp_model->update_campaign_stats($campaign->id);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'Message processing completed',
            'campaign_id' => $campaign_id,
            'campaigns_checked' => count($campaigns),
            'campaigns_processed' => $campaignsProcessed,
            'messages_sent' => $totalSent,
            'time' => date('c')
        ];
    }
    
    /**
     * Check if campaign can send message based on limits
     */
    private function can_send_message($campaign){
        $now = time();
        
        if($campaign->sending_limit_hourly > 0){
            $hourAgo = date('Y-m-d H:i:s', $now - 3600);
            $this->whatsapp_model->db->where('campaign_id', $campaign->id);
            $this->whatsapp_model->db->where('sent_at >', $hourAgo);
            $this->whatsapp_model->db->where('status', 'sent');
            $sentLastHour = $this->whatsapp_model->db->count_all_results('whatsapp_recipients');
            
            if($sentLastHour >= $campaign->sending_limit_hourly){
                return false;
            }
        }
        
        if($campaign->sending_limit_daily > 0){
            $dayAgo = date('Y-m-d H:i:s', $now - 86400);
            $this->whatsapp_model->db->where('campaign_id', $campaign->id);
            $this->whatsapp_model->db->where('sent_at >', $dayAgo);
            $this->whatsapp_model->db->where('status', 'sent');
            $sentLastDay = $this->whatsapp_model->db->count_all_results('whatsapp_recipients');
            
            if($sentLastDay >= $campaign->sending_limit_daily){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Send individual WhatsApp message using provided API
     */
    private function send_message($campaign, $recipient){
        try {
            // Get template
            $this->whatsapp_model->db->where('id', $campaign->template_id);
            $template = $this->whatsapp_model->db->get('whatsapp_templates')->row();
            
            if(!$template){
                $this->log_failed($campaign, $recipient, 'Template not found');
                return false;
            }
            
            // Get API config
            $this->whatsapp_model->db->where('id', $campaign->api_config_id);
            $api = $this->whatsapp_model->db->get('whatsapp_api_configs')->row();
            
            if(!$api || $api->status != 1){
                $this->log_failed($campaign, $recipient, 'API configuration not found or disabled');
                return false;
            }
            
            // Prepare template variables
            $variables = [];
            
            if($recipient->custom_data){
                $customData = json_decode($recipient->custom_data, true);
                if(is_array($customData)){
                    $variables = $customData;
                }
            }
            
            $variables['phone_number'] = $recipient->phone_number;
            $variables['name'] = $recipient->name ?: 'User';
            $variables['username'] = $recipient->name ?: 'User';
            
            // Process template
            $message = $this->whatsapp_model->process_template_variables($template->message, $variables);
            
            // Prepare API request data as per provided example
            $data = [
                "apiKey" => $api->api_key,
                "phoneNumber" => $recipient->phone_number,
                "message" => $message
            ];
            
            // Initialize cURL
            $ch = curl_init($api->api_url);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            // Execute the request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Check for errors
            if (curl_errno($ch)) {
                $error = 'Request Error: ' . curl_error($ch);
                curl_close($ch);
                $this->log_failed($campaign, $recipient, $error);
                return false;
            }
            
            curl_close($ch);
            
            // Check HTTP response code
            if($httpCode >= 200 && $httpCode < 300){
                // Update recipient status
                $this->whatsapp_model->update_recipient_status($recipient->id, 'sent');
                
                // Add log
                $this->whatsapp_model->add_log(
                    $campaign->id,
                    $recipient->id,
                    $recipient->phone_number,
                    $message,
                    'sent'
                );
                
                return true;
            } else {
                $error = 'HTTP Error ' . $httpCode . ': ' . $response;
                $this->log_failed($campaign, $recipient, $error);
                return false;
            }
            
        } catch(Exception $e){
            $this->log_failed($campaign, $recipient, $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log failed message
     */
    private function log_failed($campaign, $recipient, $error){
        $this->whatsapp_model->update_recipient_status($recipient->id, 'failed', $error);
        
        $this->whatsapp_model->add_log(
            $campaign->id,
            $recipient->id,
            $recipient->phone_number,
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
