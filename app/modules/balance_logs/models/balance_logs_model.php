<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class balance_logs_model extends MY_Model {
	public $tb_users;
	public $tb_balance_logs;

	public function __construct(){
		$this->tb_users        = USERS;
		$this->tb_balance_logs = BALANCE_LOGS;
		parent::__construct();
	}

	/**
	 * Get balance logs list with pagination
	 * @param bool $total_rows - if true, return count; if false, return data
	 * @param string $status - filter by status (not used currently)
	 * @param int $limit - number of records to fetch
	 * @param int $start - offset for pagination
	 * @return mixed
	 */
	function get_balance_logs_list($total_rows = false, $status = "", $limit = "", $start = ""){
		// For regular users, show only their logs
		if (get_role("user")) {
			$this->db->where("bl.uid", session('uid'));
		}
		
		if ($limit != "" && $start >= 0) {
			$this->db->limit($limit, $start);
		}
		
		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');
		$this->db->order_by("bl.id", 'DESC');
		$query = $this->db->get();
		
		if ($total_rows) {
			return $query->num_rows();
		} else {
			return $query->result();
		}
	}

	/**
	 * Search balance logs
	 * @param string $k - search keyword
	 * @return array
	 */
	function get_balance_logs_by_search($k){
		$k = trim(htmlspecialchars($k));
		
		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');

		if ($k != "" && strlen($k) >= 2) {
			if (get_role("user")) {
				$this->db->where("(`bl`.`description` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`related_id` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`action_type` LIKE '%".$k."%' ESCAPE '!')");
				$this->db->where("u.id", session("uid"));
			} else {
				$this->db->where("(`bl`.`description` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`related_id` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`action_type` LIKE '%".$k."%' ESCAPE '!' OR `u`.`email` LIKE '%".$k."%' ESCAPE '!')");
			}
		} else {
			if (get_role("user")) {
				$this->db->where("u.id", session("uid"));
			}
		}
		
		$this->db->order_by("bl.id", 'DESC');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	/**
	 * Get count of items by search
	 * @param array $search
	 * @return int
	 */
	public function get_count_items_by_search($search = []){
		$k = trim($search['k']);
		$where_like = "";
		
		switch ($search['type']) {
			case 1:
				// User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				// Related ID (Order ID, Transaction ID, etc.)
				$where_like = "`bl`.`related_id` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 3:
				// Action Type
				$where_like = "`bl`.`action_type` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select("bl.*, u.email");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');

		if (get_role("user")) {
			$this->db->where("bl.uid", session('uid'));
		}

		if ($where_like) $this->db->where($where_like);
		$this->db->order_by("bl.id", 'DESC');
		$query = $this->db->get();
		$number_row = $query->num_rows();
		return $number_row;
	}

	/**
	 * Search logs by keyword and search type
	 * @param array $search
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function search_items_by_get_method($search, $limit = "", $start = ""){
		$k = trim($search['k']);
		$where_like = "";
		
		switch ($search['type']) {
			case 1:
				// User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				// Related ID
				$where_like = "`bl`.`related_id` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 3:
				// Action Type
				$where_like = "`bl`.`action_type` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');

		if (get_role("user")) {
			$this->db->where("bl.uid", session('uid'));
		}

		if ($where_like) $this->db->where($where_like);
		
		$this->db->order_by("bl.id", 'DESC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
}
