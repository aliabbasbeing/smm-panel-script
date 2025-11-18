<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class cron_logs extends MX_Controller {
	public $tb_cron_logs;
	public $columns;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		
		// Define the table
		$this->tb_cron_logs = 'cron_logs';
		
		// Define columns for display
		$this->columns = array(
			"cron_name"        => lang('Cron_Name'),
			"executed_at"      => lang('Executed_At'),
			"status"           => lang('Status'),
			"response_code"    => lang('Response_Code'),
			"execution_time"   => lang('Execution_Time'),
		);
	}

	public function index(){
		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		
		// Build query parameters for pagination
		$query = array();
		
		// Get filter parameters
		$filter_cron = get("cron_name");
		$filter_status = get("status");
		$filter_date_from = get("date_from");
		$filter_date_to = get("date_to");
		
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
		
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}
		
		$config = array(
			'base_url'           => cn(get_class($this).$query_string),
			'total_rows'         => $this->model->get_cron_logs_list(true, $filter_cron, $filter_status, $filter_date_from, $filter_date_to),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();

		$cron_logs = $this->model->get_cron_logs_list(false, $filter_cron, $filter_status, $filter_date_from, $filter_date_to, $limit_per_page, $page * $limit_per_page);
		
		// Get unique cron names for filter dropdown
		$cron_names = $this->model->get_unique_cron_names();
		
		// Get last run info for each cron
		$last_runs = $this->model->get_last_runs();
		
		$data = array(
			"module"         => get_class($this),
			"columns"        => $this->columns,
			"cron_logs"      => $cron_logs,
			"links"          => $links,
			"cron_names"     => $cron_names,
			"last_runs"      => $last_runs,
			"filter_cron"    => $filter_cron,
			"filter_status"  => $filter_status,
			"filter_date_from" => $filter_date_from,
			"filter_date_to"   => $filter_date_to,
		);

		$this->template->build('index', $data);
	}

	
	public function ajax_search(){
		$k = post("k");
		$cron_logs = $this->model->get_cron_logs_by_search($k);
		$data = array(
			"module"      => get_class($this),
			"columns"     => $this->columns,
			"cron_logs"   => $cron_logs,
		);
		$this->load->view("ajax_search", $data);
	}

	public function ajax_delete_item($ids = ""){
		$this->model->delete($this->tb_cron_logs, $ids, false);
	}

	public function ajax_actions_option(){
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
					$this->db->delete($this->tb_cron_logs, ['ids' => $ids]);
				}
				ms(array(
					"status"  => "success",
					"message" => lang("Deleted_successfully")
				));
				break;

			case 'clear_all':
				$this->db->empty_table($this->tb_cron_logs);
				ms(array(
					"status"  => "success",
					"message" => lang("Deleted_successfully")
				));
				break;
				
			case 'cleanup_old':
				// Delete logs older than 30 days
				$this->load->library('cron_logger');
				$deleted = $this->cron_logger->cleanup(30);
				ms(array(
					"status"  => "success",
					"message" => lang("Deleted") . " " . $deleted . " " . lang("old_records")
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
	 * Manual cron trigger (optional feature)
	 * Allows admin to manually trigger a cron job
	 */
	public function trigger($cron_path = ''){
		if (!get_role("admin")) {
			ms(array(
				"status"  => "error",
				"message" => lang("You_dont_have_permission_to_access_this_page")
			));
		}
		
		if (empty($cron_path)) {
			ms(array(
				"status"  => "error",
				"message" => lang("Invalid_cron_path")
			));
		}
		
		// Build the full URL
		$cron_url = base_url($cron_path);
		
		// Add necessary tokens based on cron type
		if (strpos($cron_path, 'email_marketing') !== false) {
			$token = get_option('email_cron_token', md5('email_marketing_cron_' . ENCRYPTION_KEY));
			$cron_url .= '?token=' . $token;
		} elseif (strpos($cron_path, 'whatsapp') !== false) {
			$token = get_option('whatsapp_cron_token', md5('whatsapp_cron_' . ENCRYPTION_KEY));
			$cron_url .= '?token=' . $token;
		}
		
		// Execute cron using curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $cron_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		
		if ($http_code == 200) {
			ms(array(
				"status"  => "success",
				"message" => lang("Cron_triggered_successfully"),
				"response" => substr($response, 0, 500) // Limit response length
			));
		} else {
			ms(array(
				"status"  => "error",
				"message" => lang("Cron_trigger_failed") . ": " . ($error ? $error : "HTTP " . $http_code)
			));
		}
	}
}
