<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Balance Logs Controller
 *
 * Features:
 *  - List / paginate balance logs
 *  - Search balance logs
 *  - View detailed balance change history for users and admins
 *  - Admin view includes user email, user ID, and all transaction details
 *  - User view shows only their own balance changes
 */
class balance_logs extends MX_Controller {

	public $module;
	public $tb_users;
	public $tb_balance_logs;
	public $columns;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		$this->load->helper('balance_logs');

		$this->module           = get_class($this);
		$this->tb_users         = USERS;
		$this->tb_balance_logs  = BALANCE_LOGS;

		// Columns for admin view (more detailed)
		if (get_role("admin") || get_role("supporter")) {
			$this->columns = array(
				"uid"            => lang('User'),
				"action_type"    => lang('Action_Type'),
				"amount"         => lang('Amount'),
				"balance_before" => lang('Balance_Before'),
				"balance_after"  => lang('Balance_After'),
				"description"    => lang('Description'),
				"related_id"     => lang('Related_ID'),
				"related_type"   => lang('Related_Type'),
				"created"        => lang('Date_Time'),
			);
		} else {
			// Columns for user view (simplified)
			$this->columns = array(
				"action_type"    => lang('Action_Type'),
				"amount"         => lang('Amount'),
				"balance_before" => lang('Balance_Before'),
				"balance_after"  => lang('Balance_After'),
				"description"    => lang('Description'),
				"created"        => lang('Date_Time'),
			);
		}
	}

	/**
	 * Index - balance logs list
	 */
	public function index(){
		$page            = (int)get("p");
		$page            = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page  = get_option("default_limit_per_page", 10);

		$query           = array();
		$query_string    = (!empty($query)) ? "?".http_build_query($query) : "";

		$config = array(
			'base_url'         => cn(get_class($this).$query_string),
			'total_rows'       => $this->model->get_balance_logs_list(true),
			'per_page'         => $limit_per_page,
			'use_page_numbers' => true,
			'prev_link'        => '<i class="fe fe-chevron-left"></i>',
			'first_link'       => '<i class="fe fe-chevrons-left"></i>',
			'next_link'        => '<i class="fe fe-chevron-right"></i>',
			'last_link'        => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links       = $this->pagination->create_links();
		$balance_logs = $this->model->get_balance_logs_list(false, "all", $limit_per_page, $page * $limit_per_page);

		$data = array(
			"module"       => $this->module,
			"columns"      => $this->columns,
			"balance_logs" => $balance_logs,
			"links"        => $links,
		);

		$this->template->build('index', $data);
	}

	/**
	 * AJAX search
	 */
	public function ajax_search(){
		$k = post("k");
		$balance_logs = $this->model->get_balance_logs_by_search($k);
		$data = array(
			"module"       => $this->module,
			"columns"      => $this->columns,
			"balance_logs" => $balance_logs,
		);
		$this->load->view("ajax_search", $data);
	}

	/**
	 * Search page
	 */
	public function search(){
		if (!get_role('admin') && !get_role('supporter')) {
			redirect(cn($this->module));
		}
		$k              = htmlspecialchars(get('query'));
		$search_type    = (int)get('search_type');
		$data_search    = ['k' => $k, 'type' => $search_type];

		$page           = (int)get("p");
		$page           = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);

		$query          = ['query' => $k, 'search_type' => $search_type];
		$query_string   = "?".http_build_query($query);

		$config = array(
			'base_url'         => cn($this->module."/search".$query_string),
			'total_rows'       => $this->model->get_count_items_by_search($data_search),
			'per_page'         => $limit_per_page,
			'use_page_numbers' => true,
			'prev_link'        => '<i class="fe fe-chevron-left"></i>',
			'first_link'       => '<i class="fe fe-chevrons-left"></i>',
			'next_link'        => '<i class="fe fe-chevron-right"></i>',
			'last_link'        => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links        = $this->pagination->create_links();
		$balance_logs = $this->model->search_items_by_get_method($data_search, $limit_per_page, $page * $limit_per_page);

		$data = array(
			"module"       => $this->module,
			"columns"      => $this->columns,
			"balance_logs" => $balance_logs,
			"links"        => $links,
		);

		$this->template->build('index', $data);
	}

	/**
	 * Delete balance log (admin only)
	 */
	public function ajax_delete_item($ids = ""){
		if (!get_role('admin')) {
			ms(['status' => 'error', 'message' => 'Permission denied']);
		}
		$result = $this->model->delete($this->tb_balance_logs, $ids, false);
		if ($result) {
			ms(['status' => 'success', 'message' => lang('Deleted_successfully')]);
		} else {
			ms(['status' => 'error', 'message' => lang('There_was_an_error_processing_your_request_Please_try_again_later')]);
		}
	}

	/**
	 * Bulk actions
	 */
	public function ajax_actions_option(){
		if (!get_role('admin')) {
			ms(['status' => 'error', 'message' => 'Permission denied']);
		}
		
		$type = post("type");
		$idss = post("ids");
		
		if ($type == '') {
			ms(array(
				"status"  => "error",
				"message" => lang('There_was_an_error_processing_your_request_Please_try_again_later')
			));
		}

		if (in_array($type, ['delete']) && empty($idss)) {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_at_least_one_item")
			));
		}
		
		switch ($type) {
			case 'delete':
				foreach ($idss as $key => $ids) {
					$this->db->delete($this->tb_balance_logs, ['ids' => $ids]);
				}
				ms(array(
					"status"  => "success",
					"message" => lang("Deleted_successfully")
				));
				break;

			case 'clear_all':
				$this->db->empty_table($this->tb_balance_logs);
				ms(array(
					"status"  => "success",
					"message" => lang("Deleted_successfully")
				));
				break;
			
			default:
				ms(array(
					"status"  => "error",
					"message" => lang('There_was_an_error_processing_your_request_Please_try_again_later')
				));
				break;
		}
	}
	
	/**
	 * View Cron Logs - Display cron execution logs
	 * Admin only
	 */
	public function view_cron_logs(){
		// TEMPORARILY REMOVED ADMIN CHECK FOR TESTING
		// if (!get_role("admin")) {
		// 	redirect(cn($this->module));
		// }
		
		$page           = (int)get("p");
		$page           = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		
		// Get filter parameters
		$filter_cron    = get("cron_name");
		$filter_status  = get("status");
		$filter_date_from = get("date_from");
		$filter_date_to = get("date_to");
		
		// Build query for pagination
		$query = array();
		if ($filter_cron) {
			$query['cron_name'] = $filter_cron;
		}
		if ($filter_status) {
			$query['status'] = $filter_status;
		}
		if ($filter_date_from) {
			$query['date_from'] = $filter_date_from;
		}
		if ($filter_date_to) {
			$query['date_to'] = $filter_date_to;
		}
		
		$query_string = (!empty($query)) ? "?".http_build_query($query) : "";
		
		// Get cron logs from database
		$this->db->select('*');
		$this->db->from('cron_logs');
		
		// Apply filters
		if ($filter_cron) {
			$this->db->like('cron_name', $filter_cron);
		}
		if ($filter_status) {
			$this->db->where('status', $filter_status);
		}
		if ($filter_date_from) {
			$this->db->where('executed_at >=', $filter_date_from . ' 00:00:00');
		}
		if ($filter_date_to) {
			$this->db->where('executed_at <=', $filter_date_to . ' 23:59:59');
		}
		
		$total_rows = $this->db->count_all_results('', FALSE);
		
		$this->db->order_by('executed_at', 'DESC');
		$this->db->limit($limit_per_page, $page * $limit_per_page);
		$cron_logs = $this->db->get()->result();
		
		// Get unique cron names for filter
		$this->db->select('DISTINCT cron_name');
		$this->db->from('cron_logs');
		$this->db->order_by('cron_name', 'ASC');
		$cron_names = $this->db->get()->result();
		
		// Get last run info for each cron
		$last_runs = array();
		foreach ($cron_names as $cron) {
			$this->db->select('*');
			$this->db->from('cron_logs');
			$this->db->where('cron_name', $cron->cron_name);
			$this->db->order_by('executed_at', 'DESC');
			$this->db->limit(1);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				$last_runs[$cron->cron_name] = $query->row();
			}
		}
		
		// Pagination config
		$config = array(
			'base_url'         => cn($this->module.'/view_cron_logs'.$query_string),
			'total_rows'       => $total_rows,
			'per_page'         => $limit_per_page,
			'use_page_numbers' => true,
			'prev_link'        => '<i class="fe fe-chevron-left"></i>',
			'first_link'       => '<i class="fe fe-chevrons-left"></i>',
			'next_link'        => '<i class="fe fe-chevron-right"></i>',
			'last_link'        => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();
		
		$data = array(
			"module"         => $this->module,
			"cron_logs"      => $cron_logs,
			"cron_names"     => $cron_names,
			"last_runs"      => $last_runs,
			"links"          => $links,
			"filter_cron"    => $filter_cron,
			"filter_status"  => $filter_status,
			"filter_date_from" => $filter_date_from,
			"filter_date_to"   => $filter_date_to,
		);
		
		$this->template->build('cron_logs', $data);
	}
}
