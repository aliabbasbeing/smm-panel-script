<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_logs_model extends MY_Model {
	public $tb_cron_logs;

	public function __construct(){
		$this->tb_cron_logs = 'cron_logs';
		parent::__construct();
	}

	/**
	 * Get cron logs list with optional filters
	 * 
	 * @param bool $total_rows Return count instead of rows
	 * @param string $filter_cron Filter by cron name
	 * @param string $filter_status Filter by status
	 * @param string $filter_date_from Filter from date
	 * @param string $filter_date_to Filter to date
	 * @param int $limit Limit number of rows
	 * @param int $start Start offset
	 * @return mixed
	 */
	function get_cron_logs_list($total_rows = false, $filter_cron = "", $filter_status = "", $filter_date_from = "", $filter_date_to = "", $limit = "", $start = ""){
		if ($limit != "" && $start >= 0) {
			$this->db->limit($limit, $start);
		}
		
		$this->db->select('*');
		$this->db->from($this->tb_cron_logs);
		
		// Apply filters
		if (!empty($filter_cron)) {
			$this->db->like('cron_name', $filter_cron);
		}
		
		if (!empty($filter_status)) {
			$this->db->where('status', $filter_status);
		}
		
		if (!empty($filter_date_from)) {
			$this->db->where('executed_at >=', $filter_date_from . ' 00:00:00');
		}
		
		if (!empty($filter_date_to)) {
			$this->db->where('executed_at <=', $filter_date_to . ' 23:59:59');
		}
		
		$this->db->order_by("executed_at", 'DESC');
		$query = $this->db->get();
		
		if ($total_rows) {
			return $query->num_rows();
		} else {
			return $query->result();
		}
	}

	/**
	 * Search cron logs by keyword
	 * 
	 * @param string $k Search keyword
	 * @return array
	 */
	function get_cron_logs_by_search($k){
		$k = trim(htmlspecialchars($k));
		$this->db->select('*');
		$this->db->from($this->tb_cron_logs);

		if ($k != "" && strlen($k) >= 2) {
			$this->db->where("(`cron_name` LIKE '%".$this->db->escape_like_str($k)."%' ESCAPE '!' OR `response_message` LIKE '%".$this->db->escape_like_str($k)."%' ESCAPE '!')");
		}
		$this->db->order_by('executed_at', 'DESC');

		$query = $this->db->get();
		return $query->result();
	}
	
	/**
	 * Get unique cron names for filter dropdown
	 * 
	 * @return array
	 */
	function get_unique_cron_names(){
		$this->db->select('DISTINCT cron_name');
		$this->db->from($this->tb_cron_logs);
		$this->db->order_by('cron_name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}
	
	/**
	 * Get last run information for each cron
	 * Returns an associative array with cron_name as key
	 * 
	 * @return array
	 */
	function get_last_runs(){
		$result = array();
		
		// Get unique cron names
		$cron_names = $this->get_unique_cron_names();
		
		foreach ($cron_names as $cron) {
			$this->db->select('*');
			$this->db->from($this->tb_cron_logs);
			$this->db->where('cron_name', $cron->cron_name);
			$this->db->order_by('executed_at', 'DESC');
			$this->db->limit(1);
			$query = $this->db->get();
			
			if ($query->num_rows() > 0) {
				$result[$cron->cron_name] = $query->row();
			}
		}
		
		return $result;
	}
	
	/**
	 * Get statistics for dashboard
	 * 
	 * @return object
	 */
	function get_statistics(){
		$stats = new stdClass();
		
		// Total executions
		$this->db->select('COUNT(*) as total');
		$this->db->from($this->tb_cron_logs);
		$query = $this->db->get();
		$stats->total_executions = $query->row()->total;
		
		// Success count
		$this->db->select('COUNT(*) as total');
		$this->db->from($this->tb_cron_logs);
		$this->db->where('status', 'Success');
		$query = $this->db->get();
		$stats->success_count = $query->row()->total;
		
		// Failed count
		$this->db->select('COUNT(*) as total');
		$this->db->from($this->tb_cron_logs);
		$this->db->where('status', 'Failed');
		$query = $this->db->get();
		$stats->failed_count = $query->row()->total;
		
		// Average execution time
		$this->db->select('AVG(execution_time) as avg_time');
		$this->db->from($this->tb_cron_logs);
		$this->db->where('execution_time IS NOT NULL', NULL, FALSE);
		$query = $this->db->get();
		$stats->avg_execution_time = $query->row()->avg_time;
		
		// Total unique crons
		$this->db->select('COUNT(DISTINCT cron_name) as total');
		$this->db->from($this->tb_cron_logs);
		$query = $this->db->get();
		$stats->unique_crons = $query->row()->total;
		
		return $stats;
	}
}
