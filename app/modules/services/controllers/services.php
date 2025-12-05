<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class services extends MX_Controller {
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;
	public $columns;
	public $module;
	public $module_name;
	public $module_icon;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		//Config Module
		$this->tb_categories      = CATEGORIES;
		$this->tb_services        = SERVICES;
		$this->tb_api_providers   = API_PROVIDERS;
		$this->module_name        = 'Services';
		$this->module             = get_class($this);
		$this->module_icon        = "fa ft-users";

		$this->columns = array(
			"price"            => lang("rate_per_1000")."(".get_option("currency_symbol","").")",
			"min_max"          => lang("min__max_order"),
			"desc"             => lang("Description"),
		);

        if (get_role("admin") || get_role("supporter")) {
			$this->columns = array(
				"provider"         => 'Provider',
				"price"            => lang("rate_per_1000")."(".get_option("currency_symbol","").")",
				"min_max"          => lang("min__max_order"),
				"desc"             => lang("Description"),
				"dripfeed"         => lang("dripfeed"),
				"status"           => lang("Status"),
			);
		}				
	}

	public function index(){

		if (!session('uid') && get_option("enable_service_list_no_login") != 1) {
			redirect(cn());
		}

		// Get filter parameters from URL
		$filters = array(
			'search'    => $this->input->get('search'),
			'category'  => $this->input->get('category'),
			'status'    => $this->input->get('status'),
			'provider'  => $this->input->get('provider'),
			'price_min' => $this->input->get('price_min'),
			'price_max' => $this->input->get('price_max'),
			'dripfeed'  => $this->input->get('dripfeed'),
		);
		
		$page = max(1, (int)$this->input->get('page'));
		$per_page = max(10, min(100, (int)$this->input->get('per_page') ?: 50));
		
		// Get paginated services
		$result = $this->model->get_paginated_services($filters, $page, $per_page);
		
		// Get categories and providers for filters
		$categories_list = $this->model->get_all_categories_for_filter();
		$providers_list = get_role("admin") ? $this->model->get_all_providers_for_filter() : array();
		$stats = get_role("admin") ? $this->model->get_services_stats() : null;
		
		$data = array(
			"module"          => get_class($this),
			"columns"         => $this->columns,
			"services"        => $result['services'],
			"pagination"      => $result,
			"filters"         => $filters,
			"categories_list" => $categories_list,
			"providers_list"  => $providers_list,
			"stats"           => $stats,
			"custom_rates"    => $this->model->get_custom_rates(),
		);
		
		if (!session('uid')) {
			$this->template->set_layout('general_page');
			$this->template->build("index", $data);
		}
		$this->template->build("index", $data);
	}

	public function update($ids = ""){
		if (!get_role('admin')) _validation('error', "Permission Denied!");

		$service     = $this->model->get("*", $this->tb_services, "ids = '{$ids}' ");
		$categories  = $this->model->fetch("*", $this->tb_categories, "status = 1", 'sort','ASC');
		$api_providers  = $this->model->fetch("*", $this->tb_api_providers, "status = 1", 'id','ASC');
		$data = array(
			"module"   			=> get_class($this),
			"service" 			=> $service,
			"categories" 		=> $categories,
			"api_providers" 	=> $api_providers,
		);
		$this->load->view('update', $data);
	}

	public function desc($ids = ""){
		$service    = $this->model->get("id, ids, name, desc", $this->tb_services, "ids = '{$ids}' ");
		$data = array(
			"module"   		=> get_class($this),
			"service" 		=> $service,
		);
		$this->load->view('descriptions', $data);
	}

	public function ajax_update($ids = ""){
		_is_ajax($this->module);

		if (!get_role('admin')) _validation('error', "Permission Denied!");

		$name 		        = post("name");
		$category	        = post("category");
		$min	            = post("min");
		$max	            = post("max");
		$add_type			= post("add_type");
		$price	            = (double)post("price");
		$status 	        = (int)post("status");
		$desc 	            = $_POST['desc'];

		if($name == ""){
			ms(array(
				"status"  => "error",
				"message" => lang("name_is_required")
			));
		}

		if($category == ""){
			ms(array(
				"status"  => "error",
				"message" => lang("category_is_required")
			));
		}

		if($min == ""){
			ms(array(
				"status"  => "error",
				"message" => lang("min_order_is_required")
			));
		}

		if($max == ""){
			ms(array(
				"status"  => "error",
				"message" => lang("max_order_is_required")
			));
		}

		if($min > $max){
			ms(array(
				"status"  => "error",
				"message" => lang("max_order_must_to_be_greater_than_min_order")
			));
		}

		if($price == ""){
			ms(array(
				"status"  => "error",
				"message" => lang("price_invalid")
			));
		}

		// $decimal_places = get_option("auto_rounding_x_decimal_places", 2);
		// if(strlen(substr(strrchr($price, "."), 1)) > $decimal_places || strlen(substr(strrchr($price, "."), 1)) < 0){
		// 	ms(array(
		// 		"status"  => "error",
		// 		"message" => lang("price_invalid_format")
		// 	));
		// }

		$data = array(
			"uid"             => session('uid'),
			"cate_id"         => $category,
			"name"            => $name,
			"desc"            => $desc,
			"min"             => $min,
			"max"             => $max,
			"price"           => $price,
			"status"          => $status,
		);

		/*----------  Fields for Service API type  ----------*/
		switch ($add_type) {
			case 'api':
				$api_provider_id	         = post("api_provider_id");
				$original_price	             = post("original_price");
				$api_service_id	             = post("api_service_id");
				$api_service_type	         = post("api_service_type");
				$api_service_dripfeed	     = (int)post("api_service_dripfeed");
				
				$api = $this->model->get("ids", $this->tb_api_providers, ['id' => $api_provider_id, 'status' => 1]);
				if (empty($api)) {
					ms(array(
						"status"  => "error",
						"message" => lang("api_provider_does_not_exists")
					));
				}

				if ($api_service_id == "") {
					ms(array(
						"status"  => "error",
						"message" => 'API Service ID invalid format'
					));
				}
				$data['api_provider_id'] = $api_provider_id;
				$data['api_service_id']  = $api_service_id;
				$data['original_price']  = $original_price;
				$data['type']            = $api_service_type;
				$data['dripfeed']        = $api_service_dripfeed;
				break;
			
			default:

				$service_type_array = array('default', 'subscriptions', 'custom_comments', 'custom_comments_package', 'mentions_with_hashtags', 'mentions_custom_list', 'mentions_hashtag', 'mentions_user_followers', 'mentions_media_likers', 'package', 'comment_likes');

				if (!in_array(post("service_type"), $service_type_array)) {
					ms(array(
						"status"  => "error",
						"message" => 'Service Type invalid format'
					));
				}
				$data['api_provider_id'] = "";
				$data['api_service_id']  = "";
				$data['type']            = post("service_type");
				$data['dripfeed']        = (int)post("dripfeed");
				break;
		}
		
		$data['add_type'] = $add_type;

		$check_item = $this->model->get("ids", $this->tb_services, "ids = '{$ids}'");
		
		if(empty($check_item)){
			$data["ids"]     = ids();
			$data["changed"] = NOW;
			$data["created"] = NOW;

			$this->db->insert($this->tb_services, $data);
		}else{
			$data["changed"] = NOW;
			$this->db->update($this->tb_services, $data, array("ids" => $check_item->ids));
		}

		ms(array(
			"status"  => "success",
			"message" => lang("Update_successfully")
		));
	}
	
	public function ajax_search(){
		_is_ajax($this->module);

		$k = post("query");
		$k = htmlspecialchars($k);
		$services = $this->model->get_services_by_search($k);
		$data = array(
			"module"       => get_class($this),
			"columns"      => $this->columns,
			"services"     => $services,
			"custom_rates" => $this->model->get_custom_rates(),
		);
		$this->load->view("ajax_search", $data);
	}
	
	public function ajax_service_sort_by_cate($id){
		$data = array(
			"module"     => get_class($this),
			"columns"    => $this->columns,
			"cate_name"  => get_field($this->tb_categories, ['id' => $id], 'name'),
			"services"   => $this->model->get_services_by_cate_id($id),
		);
		$this->load->view("ajax_search", $data);
	}

	public function ajax_load_services_by_cate($id){
		$data = array(
			"module"     => get_class($this),
			"columns"    => $this->columns,
			"services"   => $this->model->get_services_by_cate_id($id),
			"cate_id"    => $id,
		);
		$this->load->view("ajax_load_services_by_cate", $data);
	}

	public function ajax_delete_item($ids = ""){
		_is_ajax($this->module);
		$this->model->delete($this->tb_services, $ids, false);
	}

	// Change Item Status
	public function ajax_toggle_item_status($id = ""){

		_is_ajax($this->module);
		if (!get_role('admin')) _validation('error', "Permission Denied!");


		$status  = post('status');
		$item  = $this->model->get("id", $this->tb_services, ['id' => $id]);
		if ($item ) {
			$this->db->update($this->tb_services, ['status' => (int)$status], ['id' => $id]);
			_validation('success', lang("Update_successfully"));
		}
	}

	public function ajax_actions_option(){
		_is_ajax($this->module);
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		
		$type = post("type");
		$idss = post("ids");
		if ($type == '') {
			ms(array(
				"status"  => "error",
				"message" => lang('There_was_an_error_processing_your_request_Please_try_again_later')
			));
		}

		if (in_array($type, ['delete', 'deactive', 'active']) && empty($idss)) {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_at_least_one_item")
			));
		}
		switch ($type) {
			case 'delete':
				foreach ($idss as $key => $ids) {
					$this->db->delete($this->tb_services, ['ids' => $ids]);
				}
				ms(array(
					"status"  => "success",
					"message" => lang("Deleted_successfully")
				));
				break;
			case 'deactive':
				foreach ($idss as $key => $ids) {
					$this->db->update($this->tb_services, ['status' => 0], ['ids' => $ids]);
				}
				ms(array(
					"status"  => "success",
					"message" => lang("Updated_successfully")
				));
				break;

			case 'active':
				foreach ($idss as $key => $ids) {
					$this->db->update($this->tb_services, ['status' => 1], ['ids' => $ids]);
				}
				ms(array(
					"status"  => "success",
					"message" => lang("Updated_successfully")
				));
				break;


			case 'all_deactive':
				$deactive_services = $this->model->fetch("*", $this->tb_services, ['status' => 0]);
				if (empty($deactive_services)) {
					ms(array(
						"status"  => "error",
						"message" => lang("failed_to_delete_there_are_no_deactivate_service_now")
					));
				}
				$this->db->delete($this->tb_services, ['status' => 0]);
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

	// Get Services From API for Update page
	public function ajax_get_services_from_api($api_service_id = ""){
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		
		$api_id  = post('api_id');
		$api     = $this->model->get("id, name, type, ids, url, key",  $this->tb_api_providers, ['id' => $api_id, 'status' => 1]);
		if (!$api) {
			redirect(cn());
		}
		$data_post = [
			'key'    => $api->key,
			'action' => 'services',
		];
		$response = api_connect($api->url, $data_post);
		if (!empty($response)) {
			$response = json_decode($response);
			usort($response, function($a, $b) {return $a->service - $b->service;});
		}
		$data = array(
			"module"   		        => get_class($this),
			"services" 		        => $response,
			"api_service_id" 		=> $api_service_id,
		);
		$this->load->view('ajax/get_services_from_api', $data);
	}

	/**
	 * AJAX endpoint for paginated services with filtering
	 * Returns HTML for the services table
	 */
	public function ajax_get_paginated_services(){
		_is_ajax($this->module);
		
		// Get filter parameters
		$filters = array(
			'search'    => post('search'),
			'category'  => post('category'),
			'status'    => post('status'),
			'provider'  => post('provider'),
			'price_min' => post('price_min'),
			'price_max' => post('price_max'),
			'dripfeed'  => post('dripfeed'),
		);
		
		$page = max(1, (int)post('page'));
		$per_page = max(10, min(100, (int)post('per_page') ?: 50));
		
		// Get paginated services
		$result = $this->model->get_paginated_services($filters, $page, $per_page);
		
		$data = array(
			"module"       => get_class($this),
			"columns"      => $this->columns,
			"services"     => $result['services'],
			"pagination"   => $result,
			"custom_rates" => $this->model->get_custom_rates(),
		);
		
		$this->load->view("ajax/paginated_services", $data);
	}

	/**
	 * AJAX endpoint for JSON response with paginated services
	 * Used for advanced client-side processing
	 */
	public function ajax_get_services_json(){
		_is_ajax($this->module);
		
		// Get filter parameters
		$filters = array(
			'search'    => post('search'),
			'category'  => post('category'),
			'status'    => post('status'),
			'provider'  => post('provider'),
			'price_min' => post('price_min'),
			'price_max' => post('price_max'),
			'dripfeed'  => post('dripfeed'),
		);
		
		$page = max(1, (int)post('page'));
		$per_page = max(10, min(100, (int)post('per_page') ?: 50));
		
		// Get paginated services
		$result = $this->model->get_paginated_services($filters, $page, $per_page);
		
		// Get custom rates for price adjustments
		$custom_rates = $this->model->get_custom_rates();
		
		// Adjust prices for non-admin users
		if (!get_role('admin') && !empty($custom_rates)) {
			foreach ($result['services'] as &$service) {
				if (isset($custom_rates[$service->id])) {
					$service->price = $custom_rates[$service->id]['service_price'];
				}
			}
		}
		
		ms(array(
			"status"     => "success",
			"data"       => $result['services'],
			"pagination" => array(
				"total"        => $result['total'],
				"pages"        => $result['pages'],
				"current_page" => $result['current_page'],
				"per_page"     => $result['per_page'],
				"from"         => $result['from'],
				"to"           => $result['to']
			)
		));
	}

	/**
	 * Bulk update prices for services
	 * Admin only
	 */
	public function ajax_bulk_update_prices(){
		_is_ajax($this->module);
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		
		$idss = post("ids");
		$price_change = post("price_change");
		$change_type = post("change_type"); // 'fixed', 'percentage', 'set'
		
		if (empty($idss)) {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_at_least_one_item")
			));
		}
		
		if (!is_numeric($price_change)) {
			ms(array(
				"status"  => "error",
				"message" => lang("price_invalid")
			));
		}
		
		$updated_count = 0;
		foreach ($idss as $ids) {
			$service = $this->model->get("id, price", $this->tb_services, ['ids' => $ids]);
			if ($service) {
				$new_price = $service->price;
				switch ($change_type) {
					case 'fixed':
						$new_price = $service->price + (float)$price_change;
						break;
					case 'percentage':
						$new_price = $service->price * (1 + ((float)$price_change / 100));
						break;
					case 'set':
						$new_price = (float)$price_change;
						break;
				}
				$new_price = max(0, $new_price); // Ensure price is not negative
				$this->db->update($this->tb_services, ['price' => $new_price, 'changed' => NOW], ['ids' => $ids]);
				$updated_count++;
			}
		}
		
		ms(array(
			"status"  => "success",
			"message" => sprintf(lang("updated_x_services"), $updated_count)
		));
	}

	/**
	 * Bulk update category for services
	 * Admin only
	 */
	public function ajax_bulk_update_category(){
		_is_ajax($this->module);
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		
		$idss = post("ids");
		$new_category = post("new_category");
		
		if (empty($idss)) {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_at_least_one_item")
			));
		}
		
		if (empty($new_category)) {
			ms(array(
				"status"  => "error",
				"message" => lang("category_is_required")
			));
		}
		
		// Verify category exists
		$category = $this->model->get("id", $this->tb_categories, ['id' => (int)$new_category]);
		if (!$category) {
			ms(array(
				"status"  => "error",
				"message" => lang("category_not_found")
			));
		}
		
		$updated_count = 0;
		foreach ($idss as $ids) {
			$this->db->update($this->tb_services, ['cate_id' => (int)$new_category, 'changed' => NOW], ['ids' => $ids]);
			$updated_count++;
		}
		
		ms(array(
			"status"  => "success",
			"message" => sprintf(lang("updated_x_services"), $updated_count)
		));
	}

	/**
	 * Export services to CSV
	 * Admin only
	 */
	public function export_csv(){
		if (!get_role('admin')) {
			redirect(cn());
		}
		
		// Get filter parameters
		$filters = array(
			'search'    => $this->input->get('search'),
			'category'  => $this->input->get('category'),
			'status'    => $this->input->get('status'),
			'provider'  => $this->input->get('provider'),
		);
		
		// Get all filtered services (no pagination for export)
		$result = $this->model->get_paginated_services($filters, 1, 10000);
		$services = $result['services'];
		
		// Set headers for CSV download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="services_export_' . date('Y-m-d_H-i-s') . '.csv"');
		
		$output = fopen('php://output', 'w');
		
		// Add BOM for UTF-8
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		
		// CSV headers
		fputcsv($output, array('ID', 'Name', 'Category', 'Price', 'Min', 'Max', 'Provider', 'API Service ID', 'Status', 'Dripfeed', 'Type'));
		
		foreach ($services as $service) {
			fputcsv($output, array(
				$service->id,
				$service->name,
				$service->category_name,
				$service->price,
				$service->min,
				$service->max,
				isset($service->api_name) ? $service->api_name : 'Manual',
				isset($service->api_service_id) ? $service->api_service_id : '',
				$service->status == 1 ? 'Active' : 'Inactive',
				isset($service->dripfeed) && $service->dripfeed == 1 ? 'Yes' : 'No',
				isset($service->type) ? $service->type : 'default'
			));
		}
		
		fclose($output);
		exit;
	}
}