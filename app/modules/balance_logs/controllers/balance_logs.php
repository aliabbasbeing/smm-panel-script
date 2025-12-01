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
	 * View Execution Logs - Display cron list with last run times
	 * Admin only
	 * 
	 * Shows a simple list of all crons and when they last ran.
	 */
	public function view_execution_logs(){
    if (!get_role("admin") && !get_role("supporter")) {
        redirect(cn($this->module));
    }
    
    // Get all cron logs with their last run time and status
    // Use subquery to get the latest record per cron_name
    $subquery = "(SELECT c1.cron_name, c1.executed_at, c1.status 
                  FROM cron_logs c1 
                  WHERE c1.executed_at = (SELECT MAX(c2.executed_at) FROM cron_logs c2 WHERE c2.cron_name = c1.cron_name)
                  GROUP BY c1.cron_name) as latest_logs";
    $this->db->select('cron_name, executed_at, status');
    $this->db->from($subquery);
    $this->db->order_by('cron_name', 'ASC');
    $cron_list = $this->db->get()->result();
    
    $data = array(
        "module"    => $this->module,
        "cron_list" => $cron_list,
    );
    
    $this->template->build('execution_logs', $data);
}
}