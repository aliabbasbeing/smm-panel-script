<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing_model extends MY_Model {
    
    protected $tb_campaigns;
    protected $tb_templates;
    protected $tb_api_configs;
    protected $tb_recipients;
    protected $tb_logs;
    protected $tb_settings;
    
    public function __construct(){
        parent::__construct();
        
        // Define table names
        $this->tb_campaigns = 'whatsapp_campaigns';
        $this->tb_templates = 'whatsapp_templates';
        $this->tb_api_configs = 'whatsapp_api_configs';
        $this->tb_recipients = 'whatsapp_recipients';
        $this->tb_logs = 'whatsapp_logs';
        $this->tb_settings = 'whatsapp_settings';
    }
    
    // ========================================
    // CAMPAIGN METHODS
    // ========================================
    
    public function get_campaigns($limit = -1, $page = -1, $status = null) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('c.*, t.name as template_name, s.name as api_name');
        }
        
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_templates . ' t', 'c.template_id = t.id', 'left');
        $this->db->join($this->tb_api_configs . ' s', 'c.api_config_id = s.id', 'left');
        
        if ($status !== null) {
            $this->db->where('c.status', $status);
        }
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('c.created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function get_campaign($ids) {
        $this->db->select('c.*, t.name as template_name, s.name as api_name');
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_templates . ' t', 'c.template_id = t.id', 'left');
        $this->db->join($this->tb_api_configs . ' s', 'c.api_config_id = s.id', 'left');
        $this->db->where('c.ids', $ids);
        $query = $this->db->get();
        
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function create_campaign($data) {
        $data['ids'] = ids();
        $data['created_at'] = NOW;
        $data['updated_at'] = NOW;
        
        return $this->db->insert($this->tb_campaigns, $data);
    }
    
    public function update_campaign($ids, $data) {
        $data['updated_at'] = NOW;
        $this->db->where('ids', $ids);
        return $this->db->update($this->tb_campaigns, $data);
    }
    
    public function delete_campaign($ids) {
        // Get campaign to delete related data
        $campaign = $this->get_campaign($ids);
        if ($campaign) {
            // Delete recipients
            $this->db->where('campaign_id', $campaign->id);
            $this->db->delete($this->tb_recipients);
            
            // Delete logs
            $this->db->where('campaign_id', $campaign->id);
            $this->db->delete($this->tb_logs);
            
            // Delete campaign
            $this->db->where('ids', $ids);
            return $this->db->delete($this->tb_campaigns);
        }
        return false;
    }
    
    public function update_campaign_stats($campaign_id) {
        $this->db->select("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = 'opened' THEN 1 ELSE 0 END) as opened,
            SUM(CASE WHEN status = 'bounced' THEN 1 ELSE 0 END) as bounced
        ");
        $this->db->from($this->tb_recipients);
        $this->db->where('campaign_id', $campaign_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $stats = $query->row();
            $this->db->where('id', $campaign_id);
            $this->db->update($this->tb_campaigns, [
                'total_messages' => $stats->total,
                'sent_messages' => $stats->sent,
                'failed_messages' => $stats->failed,
                'delivered_messages' => $stats->opened,
                'read_messages' => $stats->bounced,
                'updated_at' => NOW
            ]);
            return true;
        }
        return false;
    }
    
    /**
     * Reset failed recipients to pending for resending
     * @param int $campaign_id Campaign ID
     * @return int Number of recipients reset
     */
    public function reset_failed_recipients($campaign_id) {
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'failed');
        $this->db->update($this->tb_recipients, [
            'status' => 'pending',
            'sent_at' => null,
            'error_message' => null,
            'updated_at' => NOW
        ]);
        
        return $this->db->affected_rows();
    }
    
    /**
     * Get overall phone marketing statistics
     * @return object Statistics object
     */
    public function get_overall_stats() {
        // Get campaign stats
        $this->db->select("
            COUNT(*) as total_campaigns,
            SUM(CASE WHEN status = 'running' THEN 1 ELSE 0 END) as running_campaigns,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_campaigns,
            SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) as paused_campaigns,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_campaigns
        ");
        $campaign_stats = $this->db->get($this->tb_campaigns)->row();
        
        // Get message stats from all campaigns
        $this->db->select("
            SUM(total_messages) as total_messages,
            SUM(sent_messages) as total_sent,
            SUM(failed_messages) as total_failed
        ");
        $message_stats = $this->db->get($this->tb_campaigns)->row();
        
        // Calculate remaining messages
        $remaining = ($message_stats->total_messages - $message_stats->total_sent - $message_stats->total_failed);
        
        return (object) [
            'total_campaigns' => $campaign_stats->total_campaigns ?: 0,
            'running_campaigns' => $campaign_stats->running_campaigns ?: 0,
            'completed_campaigns' => $campaign_stats->completed_campaigns ?: 0,
            'paused_campaigns' => $campaign_stats->paused_campaigns ?: 0,
            'pending_campaigns' => $campaign_stats->pending_campaigns ?: 0,
            'total_messages' => $message_stats->total_messages ?: 0,
            'total_sent' => $message_stats->total_sent ?: 0,
            'total_failed' => $message_stats->total_failed ?: 0,
            'total_remaining' => max(0, $remaining),
            'failure_rate' => $message_stats->total_messages > 0 ? round(($message_stats->total_failed / $message_stats->total_messages) * 100, 1) : 0
        ];
    }
    
    /**
     * Get recent activity logs across all campaigns
     * @param int $limit Number of logs to fetch
     * @return array Array of log objects
     */
    public function get_recent_logs($limit = 20) {
        $this->db->select('l.*, c.name as campaign_name');
        $this->db->from($this->tb_logs . ' l');
        $this->db->join($this->tb_campaigns . ' c', 'l.campaign_id = c.id', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        
        return $query->num_rows() > 0 ? $query->result() : [];
    }
    
    // ========================================
    // TEMPLATE METHODS
    // ========================================
    
    public function get_templates($limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_templates);
        $this->db->where('status', 1);
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function get_template($ids) {
        $this->db->where('ids', $ids);
        $query = $this->db->get($this->tb_templates);
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function create_template($data) {
        $data['ids'] = ids();
        $data['created_at'] = NOW;
        $data['updated_at'] = NOW;
        
        return $this->db->insert($this->tb_templates, $data);
    }
    
    public function update_template($ids, $data) {
        $data['updated_at'] = NOW;
        $this->db->where('ids', $ids);
        return $this->db->update($this->tb_templates, $data);
    }
    
    public function delete_template($ids) {
        // Check if template is being used by any campaign
        $template = $this->get_template($ids);
        if ($template) {
            $this->db->where('template_id', $template->id);
            $this->db->where('status !=', 'completed');
            $count = $this->db->count_all_results($this->tb_campaigns);
            
            if ($count > 0) {
                return false; // Cannot delete template in use
            }
            
            $this->db->where('ids', $ids);
            return $this->db->delete($this->tb_templates);
        }
        return false;
    }
    
    // ========================================
    // API CONFIG METHODS
    // ========================================
    
    public function get_api_configs($limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_api_configs);
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function get_api_config($ids) {
        $this->db->where('ids', $ids);
        $query = $this->db->get($this->tb_api_configs);
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function get_default_api() {
        $this->db->where('is_default', 1);
        $this->db->where('status', 1);
        $query = $this->db->get($this->tb_api_configs);
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function create_api_config($data) {
        $data['ids'] = ids();
        $data['created_at'] = NOW;
        $data['updated_at'] = NOW;
        
        // If this is set as default, unset others
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->update($this->tb_api_configs, ['is_default' => 0]);
        }
        
        return $this->db->insert($this->tb_api_configs, $data);
    }
    
    public function update_api_config($ids, $data) {
        $data['updated_at'] = NOW;
        
        // If this is set as default, unset others
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->update($this->tb_api_configs, ['is_default' => 0]);
        }
        
        $this->db->where('ids', $ids);
        return $this->db->update($this->tb_api_configs, $data);
    }
    
    public function delete_api_config($ids) {
        $api = $this->get_api_config($ids);
        if ($api) {
            // Check if API is being used
            $this->db->where('api_config_id', $api->id);
            $this->db->where('status !=', 'completed');
            $count = $this->db->count_all_results($this->tb_campaigns);
            
            if ($count > 0) {
                return false; // Cannot delete API in use
            }
            
            $this->db->where('ids', $ids);
            return $this->db->delete($this->tb_api_configs);
        }
        return false;
    }
    
    // ========================================
    // RECIPIENT METHODS
    // ========================================
    
    public function get_recipients($campaign_id, $limit = -1, $page = -1, $status = null) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_recipients);
        $this->db->where('campaign_id', $campaign_id);
        
        if ($status !== null) {
            $this->db->where('status', $status);
        }
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('created_at', 'ASC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function add_recipient($campaign_id, $phone, $name = null, $user_id = null, $custom_data = null) {
        $data = [
            'ids' => ids(),
            'campaign_id' => $campaign_id,
            'phone' => $phone,
            'name' => $name,
            'user_id' => $user_id,
            'custom_data' => $custom_data ? json_encode($custom_data) : null,
            'tracking_token' => md5($campaign_id . $phone . time() . rand(1000, 9999)),
            'status' => 'pending',
            'created_at' => NOW,
            'updated_at' => NOW
        ];
        
        return $this->db->insert($this->tb_recipients, $data);
    }
    
    public function import_from_users($campaign_id, $filters = [], $limit = 0) {
        try {
            // More efficient approach: Use WHERE EXISTS instead of JOIN + GROUP BY
            // This will be much faster for large datasets
            // $limit = 0 means no limit (import all available users)
            // Note: Using first_name (with underscore) as per actual database schema
            $this->db->select('u.id, u.phone, u.first_name as name, u.balance');
            $this->db->from(USERS . ' u');
            $this->db->where('u.status', 1);
            $this->db->where('u.phone IS NOT NULL', NULL, FALSE);
            $this->db->where('u.phone !=', '');
            
            // Apply filters if provided
            if (!empty($filters['role'])) {
                $this->db->where('u.role', $filters['role']);
            }
            
            // Only get users who have at least one order
            // Using uid column from orders table as per schema
            $this->db->where("EXISTS (SELECT 1 FROM " . ORDER . " o WHERE o.uid = u.id LIMIT 1)", NULL, FALSE);
            
            // Apply limit if specified (0 = no limit)
            if ($limit > 0) {
                $this->db->limit($limit);
            }
            
            $query = $this->db->get();
            
            // Check for database errors
            if (!$query) {
                log_message('error', 'Phone Marketing: Failed to query users - ' . $this->db->error()['message']);
                return 0;
            }
            
            $users = $query->result();
            
            $imported = 0;
            foreach ($users as $user) {
                // Skip if phone is invalid
                if (empty($user->phone) || !filter_var($user->phone, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                
                // Check if already exists
                $this->db->where('campaign_id', $campaign_id);
                $this->db->where('phone', $user->phone);
                $exists = $this->db->count_all_results($this->tb_recipients);
                
                if ($exists == 0) {
                    // Get order count for this user (with limit to prevent slow queries)
                    $this->db->where('uid', $user->id);
                    $order_count = $this->db->count_all_results(ORDER);
                    
                    $custom_data = [
                        'username' => $user->name ? $user->name : 'User',
                        'phone' => $user->phone,
                        'balance' => $user->balance ? $user->balance : 0,
                        'total_orders' => $order_count
                    ];
                    
                    if ($this->add_recipient($campaign_id, $user->phone, $user->name, $user->id, $custom_data)) {
                        $imported++;
                    }
                }
            }
            
            return $imported;
        } catch (Exception $e) {
            log_message('error', 'Phone Marketing: Error in import_from_users - ' . $e->getMessage());
            return 0;
        }
    }
    
    public function import_from_csv($campaign_id, $file_path) {
        if (!file_exists($file_path)) {
            return 0;
        }
        
        $imported = 0;
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $header = fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (isset($data[0]) && filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                    $phone = trim($data[0]);
                    $name = isset($data[1]) ? trim($data[1]) : null;
                    
                    // Check if already exists
                    $this->db->where('campaign_id', $campaign_id);
                    $this->db->where('phone', $phone);
                    $exists = $this->db->count_all_results($this->tb_recipients);
                    
                    if ($exists == 0) {
                        if ($this->add_recipient($campaign_id, $phone, $name)) {
                            $imported++;
                        }
                    }
                }
            }
            fclose($handle);
        }
        
        return $imported;
    }
    
    public function get_next_pending_recipient($campaign_id) {
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'pending');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get($this->tb_recipients);
        
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function update_recipient_status($recipient_id, $status, $error_message = null) {
        $data = [
            'status' => $status,
            'updated_at' => NOW
        ];
        
        if ($status == 'sent') {
            $data['sent_at'] = NOW;
        } elseif ($status == 'opened') {
            $data['opened_at'] = NOW;
        }
        
        if ($error_message) {
            $data['error_message'] = $error_message;
        }
        
        $this->db->where('id', $recipient_id);
        return $this->db->update($this->tb_recipients, $data);
    }
    
    // ========================================
    // LOG METHODS
    // ========================================
    
    public function add_log($campaign_id, $recipient_id, $phone, $subject, $status, $error_message = null) {
        $data = [
            'ids' => ids(),
            'campaign_id' => $campaign_id,
            'recipient_id' => $recipient_id,
            'phone' => $phone,
            'subject' => $subject,
            'status' => $status,
            'error_message' => $error_message,
            'sent_at' => ($status == 'sent') ? NOW : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => NOW
        ];
        
        return $this->db->insert($this->tb_logs, $data);
    }
    
    public function get_logs($campaign_id, $limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_logs);
        $this->db->where('campaign_id', $campaign_id);
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    // ========================================
    // SETTINGS METHODS
    // ========================================
    
    public function get_setting($key, $default = null) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->tb_settings);
        
        if ($query->num_rows() > 0) {
            return $query->row()->setting_value;
        }
        
        return $default;
    }
    
    public function update_setting($key, $value) {
        $this->db->where('setting_key', $key);
        $exists = $this->db->count_all_results($this->tb_settings);
        
        if ($exists > 0) {
            $this->db->where('setting_key', $key);
            return $this->db->update($this->tb_settings, [
                'setting_value' => $value,
                'updated_at' => NOW
            ]);
        } else {
            return $this->db->insert($this->tb_settings, [
                'setting_key' => $key,
                'setting_value' => $value,
                'updated_at' => NOW
            ]);
        }
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    public function process_template_variables($template_body, $variables) {
        $body = $template_body;
        
        // Add default variables
        $default_vars = [
            'site_name' => get_option('website_name', 'SMM Panel'),
            'site_url' => base_url(),
            'current_date' => date('Y-m-d'),
            'current_year' => date('Y')
        ];
        
        $variables = array_merge($default_vars, $variables);
        
        // Replace variables
        foreach ($variables as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
        }
        
        return $body;
    }
}
