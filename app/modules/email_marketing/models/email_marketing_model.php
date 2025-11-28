<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_marketing_model extends MY_Model {
    
    protected $tb_campaigns;
    protected $tb_templates;
    protected $tb_smtp_configs;
    protected $tb_recipients;
    protected $tb_logs;
    protected $tb_settings;
    
    public function __construct(){
        parent::__construct();
        
        // Define table names
        $this->tb_campaigns = 'email_campaigns';
        $this->tb_templates = 'email_templates';
        $this->tb_smtp_configs = 'email_smtp_configs';
        $this->tb_recipients = 'email_recipients';
        $this->tb_logs = 'email_logs';
        $this->tb_settings = 'email_settings';
    }
    
    // ========================================
    // CAMPAIGN METHODS
    // ========================================
    
    public function get_campaigns($limit = -1, $page = -1, $status = null) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('c.*, t.name as template_name, s.name as smtp_name');
        }
        
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_templates . ' t', 'c.template_id = t.id', 'left');
        $this->db->join($this->tb_smtp_configs . ' s', 'c.smtp_config_id = s.id', 'left');
        
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
        $this->db->select('c.*, t.name as template_name, s.name as smtp_name');
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_templates . ' t', 'c.template_id = t.id', 'left');
        $this->db->join($this->tb_smtp_configs . ' s', 'c.smtp_config_id = s.id', 'left');
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
    
    /**
     * Update campaign SMTP rotation index by campaign ID (not ids)
     * @param int $campaign_id Campaign primary key ID
     * @param int $new_index New rotation index
     * @return bool Success
     */
    public function update_campaign_rotation_index($campaign_id, $new_index) {
        log_message('error', 'ROTATION UPDATE: campaign_id=' . $campaign_id . ', new_index=' . $new_index);
        
        $this->db->where('id', $campaign_id);
        $result = $this->db->update($this->tb_campaigns, [
            'smtp_rotation_index' => $new_index,
            'updated_at' => NOW
        ]);
        
        $last_query = $this->db->last_query();
        log_message('error', 'ROTATION SQL: ' . $last_query);
        log_message('error', 'ROTATION RESULT: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        // Verify the update
        $this->db->select('smtp_rotation_index');
        $this->db->where('id', $campaign_id);
        $verify = $this->db->get($this->tb_campaigns)->row();
        if ($verify) {
            log_message('error', 'ROTATION VERIFY: smtp_rotation_index in DB=' . var_export($verify->smtp_rotation_index, true));
        }
        
        return $result;
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
                'total_emails' => $stats->total,
                'sent_emails' => $stats->sent,
                'failed_emails' => $stats->failed,
                'opened_emails' => $stats->opened,
                'bounced_emails' => $stats->bounced,
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
     * Get overall email marketing statistics
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
        
        // Get email stats from all campaigns
        $this->db->select("
            SUM(total_emails) as total_emails,
            SUM(sent_emails) as total_sent,
            SUM(failed_emails) as total_failed,
            SUM(opened_emails) as total_opened,
            SUM(bounced_emails) as total_bounced
        ");
        $email_stats = $this->db->get($this->tb_campaigns)->row();
        
        // Calculate remaining emails
        $remaining = ($email_stats->total_emails - $email_stats->total_sent - $email_stats->total_failed);
        
        return (object) [
            'total_campaigns' => $campaign_stats->total_campaigns ?: 0,
            'running_campaigns' => $campaign_stats->running_campaigns ?: 0,
            'completed_campaigns' => $campaign_stats->completed_campaigns ?: 0,
            'paused_campaigns' => $campaign_stats->paused_campaigns ?: 0,
            'pending_campaigns' => $campaign_stats->pending_campaigns ?: 0,
            'total_emails' => $email_stats->total_emails ?: 0,
            'total_sent' => $email_stats->total_sent ?: 0,
            'total_failed' => $email_stats->total_failed ?: 0,
            'total_opened' => $email_stats->total_opened ?: 0,
            'total_bounced' => $email_stats->total_bounced ?: 0,
            'total_remaining' => max(0, $remaining),
            'open_rate' => $email_stats->total_sent > 0 ? round(($email_stats->total_opened / $email_stats->total_sent) * 100, 1) : 0,
            'failure_rate' => $email_stats->total_emails > 0 ? round(($email_stats->total_failed / $email_stats->total_emails) * 100, 1) : 0
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
    // SMTP CONFIG METHODS
    // ========================================
    
    public function get_smtp_configs($limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_smtp_configs);
        
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
    
    public function get_smtp_config($ids) {
        $this->db->where('ids', $ids);
        $query = $this->db->get($this->tb_smtp_configs);
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function get_default_smtp() {
        $this->db->where('is_default', 1);
        $this->db->where('status', 1);
        $query = $this->db->get($this->tb_smtp_configs);
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function create_smtp_config($data) {
        $data['ids'] = ids();
        $data['created_at'] = NOW;
        $data['updated_at'] = NOW;
        
        // If this is set as default, unset others
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->update($this->tb_smtp_configs, ['is_default' => 0]);
        }
        
        return $this->db->insert($this->tb_smtp_configs, $data);
    }
    
    public function update_smtp_config($ids, $data) {
        $data['updated_at'] = NOW;
        
        // If this is set as default, unset others
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->db->update($this->tb_smtp_configs, ['is_default' => 0]);
        }
        
        $this->db->where('ids', $ids);
        return $this->db->update($this->tb_smtp_configs, $data);
    }
    
    public function delete_smtp_config($ids) {
        $smtp = $this->get_smtp_config($ids);
        if ($smtp) {
            // Check if SMTP is being used
            $this->db->where('smtp_config_id', $smtp->id);
            $this->db->where('status !=', 'completed');
            $count = $this->db->count_all_results($this->tb_campaigns);
            
            if ($count > 0) {
                return false; // Cannot delete SMTP in use
            }
            
            $this->db->where('ids', $ids);
            return $this->db->delete($this->tb_smtp_configs);
        }
        return false;
    }
    
    /**
     * Increment SMTP usage statistics
     * Uses a helper method to check column existence for cleaner code
     * @param int $smtp_id SMTP config ID
     * @param bool $success Whether the email was sent successfully
     * @return bool Success
     */
    public function increment_smtp_usage($smtp_id, $success = true) {
        // Check if columns exist (graceful handling for existing installations)
        $this->db->where('id', $smtp_id);
        $smtp = $this->db->get($this->tb_smtp_configs)->row();
        
        if (!$smtp) {
            return false;
        }
        
        // Build update data based on what columns exist
        $update_data = ['updated_at' => NOW];
        
        // Helper array mapping column names to update values
        $column_updates = [
            'total_sent' => ($smtp->total_sent ?? 0) + 1
        ];
        
        if ($success) {
            $column_updates['successful_sent'] = ($smtp->successful_sent ?? 0) + 1;
            $column_updates['last_success_at'] = NOW;
        } else {
            $column_updates['failed_sent'] = ($smtp->failed_sent ?? 0) + 1;
            $column_updates['last_failure_at'] = NOW;
        }
        
        // Only add columns that exist in the SMTP object
        foreach ($column_updates as $column => $value) {
            if (property_exists($smtp, $column)) {
                $update_data[$column] = $value;
            }
        }
        
        // Only update if we have something meaningful to update
        if (count($update_data) > 1) { // More than just updated_at
            $this->db->where('id', $smtp_id);
            return $this->db->update($this->tb_smtp_configs, $update_data);
        }
        
        return true; // Gracefully succeed if columns don't exist yet
    }
    
    /**
     * Get SMTP usage statistics for a specific SMTP config
     * Gracefully handles missing columns for existing installations
     * @param int $smtp_id SMTP config ID
     * @return object|null Statistics object or null if not found
     */
    public function get_smtp_usage_stats($smtp_id) {
        // Use SELECT * to avoid errors if new columns don't exist
        $this->db->where('id', $smtp_id);
        $query = $this->db->get($this->tb_smtp_configs);
        
        if ($query->num_rows() > 0) {
            $smtp = $query->row();
            
            // Calculate success rate if tracking columns exist
            $smtp->success_rate = 0;
            if (property_exists($smtp, 'total_sent') && isset($smtp->total_sent) && $smtp->total_sent > 0) {
                $successful = property_exists($smtp, 'successful_sent') ? ($smtp->successful_sent ?? 0) : 0;
                $smtp->success_rate = round(($successful / $smtp->total_sent) * 100, 2);
            }
            
            return $smtp;
        }
        
        return null;
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
    
    public function add_recipient($campaign_id, $email, $name = null, $user_id = null, $custom_data = null, $priority = 100) {
        // Check if recipient already exists to prevent duplicates
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('email', $email);
        $exists = $this->db->count_all_results($this->tb_recipients);
        
        if ($exists > 0) {
            // Email already exists in this campaign, skip insertion
            return false;
        }
        
        $data = [
            'ids' => ids(),
            'campaign_id' => $campaign_id,
            'email' => $email,
            'name' => $name,
            'user_id' => $user_id,
            'custom_data' => $custom_data ? json_encode($custom_data) : null,
            'priority' => $priority,
            'tracking_token' => md5($campaign_id . $email . time() . rand(1000, 9999)),
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
            $this->db->select('u.id, u.email, u.first_name as name, u.balance');
            $this->db->from(USERS . ' u');
            $this->db->where('u.status', 1);
            $this->db->where('u.email IS NOT NULL', NULL, FALSE);
            $this->db->where('u.email !=', '');
            
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
                log_message('error', 'Email Marketing: Failed to query users - ' . $this->db->error()['message']);
                return 0;
            }
            
            $users = $query->result();
            
            $imported = 0;
            foreach ($users as $user) {
                // Skip if email is invalid
                if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                
                // Get order count for this user (with limit to prevent slow queries)
                $this->db->where('uid', $user->id);
                $order_count = $this->db->count_all_results(ORDER);
                
                $custom_data = [
                    'username' => $user->name ? $user->name : 'User',
                    'email' => $user->email,
                    'balance' => $user->balance ? $user->balance : 0,
                    'total_orders' => $order_count
                ];
                
                // add_recipient now handles duplicate checking
                if ($this->add_recipient($campaign_id, $user->email, $user->name, $user->id, $custom_data)) {
                    $imported++;
                }
            }
            
            return $imported;
        } catch (Exception $e) {
            log_message('error', 'Email Marketing: Error in import_from_users - ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Import ALL users from general_users table (no order filtering)
     * @param int $campaign_id Campaign ID
     * @param array $filters Optional filters
     * @param int $limit Maximum number of users to import (0 = no limit)
     * @return int Number of imported users
     */
    public function import_all_users($campaign_id, $filters = [], $limit = 0) {
        try {
            // Import all users from general_users table without order filtering
            $this->db->select('u.id, u.email, u.first_name as name, u.balance');
            $this->db->from(USERS . ' u');
            $this->db->where('u.status', 1);
            $this->db->where('u.email IS NOT NULL', NULL, FALSE);
            $this->db->where('u.email !=', '');
            
            // Apply filters if provided
            if (!empty($filters['role'])) {
                $this->db->where('u.role', $filters['role']);
            }
            
            // Apply limit if specified (0 = no limit)
            if ($limit > 0) {
                $this->db->limit($limit);
            }
            
            $query = $this->db->get();
            
            // Check for database errors
            if (!$query) {
                log_message('error', 'Email Marketing: Failed to query all users - ' . $this->db->error()['message']);
                return 0;
            }
            
            $users = $query->result();
            
            $imported = 0;
            foreach ($users as $user) {
                // Skip if email is invalid
                if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                
                $custom_data = [
                    'username' => !empty($user->name) ? $user->name : 'User',
                    'email' => $user->email,
                    'balance' => !empty($user->balance) ? $user->balance : 0,
                    'total_orders' => 0 // Not checking orders for this import type
                ];
                
                // add_recipient now handles duplicate checking
                if ($this->add_recipient($campaign_id, $user->email, $user->name, $user->id, $custom_data)) {
                    $imported++;
                }
            }
            
            return $imported;
        } catch (Exception $e) {
            log_message('error', 'Email Marketing: Error in import_all_users - ' . $e->getMessage());
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
                    $email = trim($data[0]);
                    $name = isset($data[1]) ? trim($data[1]) : null;
                    
                    // add_recipient now handles duplicate checking
                    if ($this->add_recipient($campaign_id, $email, $name)) {
                        $imported++;
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
        // Order by priority first (lower = higher priority), then by id
        // Manual emails have priority=1, imported have priority=100
        $this->db->order_by('priority', 'ASC');
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
    
    public function add_log($campaign_id, $recipient_id, $email, $subject, $status, $error_message = null, $smtp_config_id = null) {
        // Ensure smtp_config_id is properly cast to integer if provided
        // Note: MySQL auto-increment IDs start at 1, so 0 is not a valid SMTP config ID
        // We filter out null, empty string, and 0 to prevent invalid values
        $smtp_id_value = null;
        if ($smtp_config_id !== null && $smtp_config_id !== '' && (int)$smtp_config_id > 0) {
            $smtp_id_value = (int)$smtp_config_id;
        }
        
        // Log the incoming parameter for debugging
        log_message('error', 'ADD_LOG CALLED: smtp_config_id param=' . var_export($smtp_config_id, true) . ', computed smtp_id_value=' . var_export($smtp_id_value, true));
        
        $data = [
            'ids' => ids(),
            'campaign_id' => (int)$campaign_id,
            'recipient_id' => (int)$recipient_id,
            'smtp_config_id' => $smtp_id_value,
            'email' => $email,
            'subject' => $subject,
            'status' => $status,
            'error_message' => $error_message,
            'sent_at' => ($status == 'sent') ? NOW : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => NOW
        ];
        
        // Log the full data array being inserted
        log_message('error', 'ADD_LOG DATA: ' . json_encode($data));
        
        $result = $this->db->insert($this->tb_logs, $data);
        
        // Log the actual SQL query that was executed
        $last_query = $this->db->last_query();
        log_message('error', 'ADD_LOG SQL: ' . $last_query);
        
        // Log any database errors
        if (!$result) {
            $error = $this->db->error();
            log_message('error', 'ADD_LOG FAILED: ' . json_encode($error));
        } else {
            $insert_id = $this->db->insert_id();
            log_message('error', 'ADD_LOG SUCCESS: ID=' . $insert_id);
            
            // Verify the insert by reading back the record
            $this->db->where('id', $insert_id);
            $verify = $this->db->get($this->tb_logs)->row();
            if ($verify) {
                log_message('error', 'ADD_LOG VERIFY: smtp_config_id in DB=' . var_export($verify->smtp_config_id, true));
            }
        }
        
        return $result;
    }
    
    public function get_logs($campaign_id, $limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
            $this->db->from($this->tb_logs);
            $this->db->where('campaign_id', $campaign_id);
            $this->db->order_by('created_at', 'DESC');
        } else {
            // Select all log fields plus SMTP name via JOIN
            $this->db->select('l.*, s.name as smtp_name');
            $this->db->from($this->tb_logs . ' l');
            $this->db->join($this->tb_smtp_configs . ' s', 'l.smtp_config_id = s.id', 'left');
            $this->db->where('l.campaign_id', $campaign_id);
            $this->db->limit($limit, $page);
            $this->db->order_by('l.created_at', 'DESC');
        }
        
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
