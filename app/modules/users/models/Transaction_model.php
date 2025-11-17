<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model {
    
    /**
     * Get total deposit amount for a user
     * @param int $uid User ID
     * @return float|false Total deposit amount or false if no deposits found
     */
    public function get_total_deposit($uid) {
        if (empty($uid) || !is_numeric($uid)) {
            return false;
        }

        $this->db->select_sum('amount');
        $this->db->from('general_transaction_logs');
        $this->db->where('uid', (int)$uid);
        $this->db->where('status', 1);
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->amount !== null ? (float)$result->amount : 0;
        }

        return false;
    }

    /**
     * Get all transactions for a user
     * @param int $uid User ID
     * @return array Array of transaction objects
     */
    public function get_user_transactions($uid) {
        if (empty($uid) || !is_numeric($uid)) {
            return [];
        }

        $this->db->select('transaction_id, amount, created, status, type');
        $this->db->from('general_transaction_logs');
        $this->db->where('uid', (int)$uid);
        $this->db->order_by('created', 'DESC');
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return [];
    }

    /**
     * Get total count of orders for a user
     * @param int $uid User ID
     * @return int Total orders count
     */
    public function get_total_orders($uid) {
        if (empty($uid) || !is_numeric($uid)) {
            return 0;
        }

        $this->db->from('orders');
        $this->db->where('uid', (int)$uid);
        
        return $this->db->count_all_results();
    }

    /**
     * Get transaction statistics for a user
     * @param int $uid User ID
     * @return object Transaction statistics
     */
    public function get_transaction_stats($uid) {
        if (empty($uid) || !is_numeric($uid)) {
            return null;
        }

        $this->db->select('COUNT(*) as total_transactions, SUM(amount) as total_amount, AVG(amount) as average_amount');
        $this->db->from('general_transaction_logs');
        $this->db->where('uid', (int)$uid);
        $this->db->where('status', 1);
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return null;
    }

    /**
     * Get recent transactions for a user
     * @param int $uid User ID
     * @param int $limit Number of transactions to retrieve
     * @return array Array of transaction objects
     */
    public function get_recent_transactions($uid, $limit = 10) {
        if (empty($uid) || !is_numeric($uid)) {
            return [];
        }

        $this->db->select('transaction_id, amount, created, status, type');
        $this->db->from('general_transaction_logs');
        $this->db->where('uid', (int)$uid);
        $this->db->order_by('created', 'DESC');
        $this->db->limit((int)$limit);
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return [];
    }
}