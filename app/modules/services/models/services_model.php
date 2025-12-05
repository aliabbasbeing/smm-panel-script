<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class services_model extends MY_Model {
	public $tb_users;
	public $tb_users_price;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;

	public function __construct(){
		$this->tb_categories     = CATEGORIES;
		$this->tb_services       = SERVICES;
		$this->tb_api_providers  = API_PROVIDERS;
		$this->tb_users_price    = USERS_PRICE;
		parent::__construct();
	}

	public function get_services_list(){
		if (get_role("admin")) {
			$this->db->select('s.*, api.name as api_name, c.name as category_name, c.id as main_cate_id');
		}else{
			$this->db->where("s.status", "1");
			$this->db->select('s.id, s.desc, s.ids, s.name, s.min, s.max, s.price, api.name as api_name, c.name as category_name, c.id as main_cate_id');
		}
		$this->db->from($this->tb_services." s");
		$this->db->join($this->tb_categories." c", "c.id = s.cate_id", 'left');
		$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
		$this->db->order_by("c.sort", 'ASC');
		$this->db->order_by("s.price", 'ASC');
		$this->db->order_by("s.name", 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		$category = array();
		if ($result) {
			foreach ($query->result_array() as $row) {
               $category[$row['category_name']][] = (object)$row;
         	}
		}
		return $category;
	}

	public function get_services_list_old(){
		$data  = array();
		// get categories
		if (get_role("user")) {
			$this->db->where("status", "1");
		}

		$this->db->select("id, ids, name");
		$this->db->from($this->tb_categories);
		$this->db->order_by("sort", 'ASC');

		$query = $this->db->get();
		$categories = $query->result();
		if(!empty($categories)){
			$i = 0;
			foreach ($categories as $key => $row) {
				$i++;
				// get services
				if ($i > 0) {
					if (get_role("supporter") || get_role("admin")) {
						$services = $this->model->fetch("id", $this->tb_services, ['cate_id' => $row->id],'price', 'ASC');
					}else{
						$services = $this->model->fetch("id", $this->tb_services, ["status" => 1, 'cate_id' => $row->id], 'price', 'ASC');
					}

					if(!empty($services)){
						$categories[$key]->is_exists_services = 1;
					}else{
						unset($categories[$key]);	
					}

				}else{
					break;
				}
			}
		}
		return $categories;
	}

	public function get_services_by_search($k){
		$k = trim(htmlspecialchars($k));
		if (get_role("supporter") || get_role("admin")) {
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');

			$this->db->where("(`s`.`id` LIKE '%".$k."%' ESCAPE '!' OR `s`.`api_service_id` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!')");
			
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			$result = $query->result();

		}else{
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');

			$this->db->where("(`s`.`id` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!')");

			$this->db->where("s.status", 1);
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			$result = $query->result();
		}
		return $result;
	}

	// Search Items by keywork and search type
	public function search_items_by_get_method($search){
		$k = trim($search['k']);
		$where_like = "";

		if (get_role("user")) {
			$this->db->where("s.status", 1);
			$this->db->where("s.status", 1);
			$where_like = "(`s`.`id` LIKE '%".$k."%' ESCAPE '!' OR `s`.`api_service_id` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!')";
		}else{
			$where_like = "(`s`.`id` LIKE '%".$k."%' ESCAPE '!' OR `s`.`api_service_id` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!' OR  `api`.`name` LIKE '%".$k."%' ESCAPE '!')";
		}

		$this->db->select('s.*, api.name as api_name');
		$this->db->from($this->tb_services." s");
		$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
		
		if ($where_like) $this->db->where($where_like);

		$this->db->order_by("s.price", 'ASC');
		$query = $this->db->get();
		$result = $query->result();

		return $result;
	}

	public function get_services_by_cate_id($id){
		if (get_role("supporter") || get_role("admin")) {
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
			$this->db->where("s.cate_id", $id);
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			$result = $query->result();
		}else{
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
			$this->db->where("s.cate_id", $id);
			$this->db->where("s.status", 1);
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			$result = $query->result();
		}
		return $result;
	}

	public function get_active_categories(){
		$data  = array();
		// get categories
		if (get_role("user")) {
			$this->db->where("status", "1");
		}
		$this->db->select("id, ids, name");
		$this->db->from($this->tb_categories);
		$this->db->order_by("sort", 'ASC');
		$query = $this->db->get();
		$categories = $query->result();
		if(!empty($categories)){
			$i = 0;
			foreach ($categories as $key => $row) {
				$i++;
				// get services
				if ($i > 0) {
					$query = $this->db->query("SELECT id FROM $this->tb_services WHERE status = 1 AND cate_id = '{$row->id}'");
					if($query->num_rows() > 0){
						$categories[$key]->is_exists_services = 1;
					}else{
						unset($categories[$key]);	
					}

				}else{
					break;
				}
			}
		}
		return $categories;
	}

	public function get_custom_rates(){
		$custom_rates = $this->model->fetch('uid, service_id, service_price',$this->tb_users_price, ['uid' => session('uid')]);
		$exist_db_custom_rates = [];
		if (!empty($custom_rates)) {
			foreach ($custom_rates as $key => $row) {
				$exist_db_custom_rates[$row->service_id]['uid']           = $row->uid;
				$exist_db_custom_rates[$row->service_id]['service_id']    = $row->service_id;
				$exist_db_custom_rates[$row->service_id]['service_price'] = $row->service_price;
			}
		}
		return $exist_db_custom_rates;
	}

	/**
	 * Get paginated services with advanced filtering
	 * 
	 * @param array $filters Filter options (search, category, status, provider, price_min, price_max)
	 * @param int $page Current page number
	 * @param int $per_page Items per page
	 * @return array Contains 'services', 'total', 'pages', 'current_page'
	 */
	public function get_paginated_services($filters = array(), $page = 1, $per_page = 50){
		$page = max(1, (int)$page);
		$per_page = max(10, min(100, (int)$per_page));
		$offset = ($page - 1) * $per_page;
		
		// Build base query for counting
		$this->_build_services_base_query();
		$this->_apply_filters($filters);
		
		// Get total count first (before limit)
		$total = $this->db->count_all_results('', false);
		
		// Reset the query and rebuild for fetching data
		$this->db->reset_query();
		$this->_build_services_base_query();
		$this->_apply_filters($filters);
		
		// Apply ordering and pagination
		$this->db->order_by("c.sort", 'ASC');
		$this->db->order_by("s.price", 'ASC');
		$this->db->order_by("s.name", 'ASC');
		$this->db->limit($per_page, $offset);
		
		$query = $this->db->get();
		$services = $query->result();
		
		$total_pages = ceil($total / $per_page);
		
		return array(
			'services' => $services,
			'total' => $total,
			'pages' => $total_pages,
			'current_page' => $page,
			'per_page' => $per_page,
			'from' => $offset + 1,
			'to' => min($offset + $per_page, $total)
		);
	}
	
	/**
	 * Build base query for services with joins
	 * Extracted to reduce code duplication
	 */
	private function _build_services_base_query(){
		if (get_role("admin")) {
			$this->db->select('s.*, api.name as api_name, c.name as category_name, c.id as main_cate_id, c.sort as cate_sort');
		} else {
			$this->db->where("s.status", "1");
			$this->db->select('s.id, s.desc, s.ids, s.name, s.min, s.max, s.price, s.dripfeed, s.status, api.name as api_name, c.name as category_name, c.id as main_cate_id, c.sort as cate_sort');
		}
		
		$this->db->from($this->tb_services." s");
		$this->db->join($this->tb_categories." c", "c.id = s.cate_id", 'left');
		$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
	}
	
	/**
	 * Apply filters to the current query
	 * 
	 * @param array $filters Filter options
	 */
	private function _apply_filters($filters){
		// Search filter
		if (!empty($filters['search'])) {
			$search = $this->db->escape_like_str($filters['search']);
			if (get_role("supporter") || get_role("admin")) {
				$this->db->where("(`s`.`id` LIKE '%".$search."%' ESCAPE '!' OR `s`.`api_service_id` LIKE '%".$search."%' ESCAPE '!' OR `s`.`name` LIKE '%".$search."%' ESCAPE '!' OR `api`.`name` LIKE '%".$search."%' ESCAPE '!')");
			} else {
				$this->db->where("(`s`.`id` LIKE '%".$search."%' ESCAPE '!' OR `s`.`name` LIKE '%".$search."%' ESCAPE '!')");
			}
		}
		
		// Category filter
		if (!empty($filters['category']) && $filters['category'] != 'all') {
			$this->db->where("s.cate_id", (int)$filters['category']);
		}
		
		// Status filter (admin only)
		if (get_role("admin") && isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
			$this->db->where("s.status", (int)$filters['status']);
		}
		
		// Provider filter (admin only)
		if (get_role("admin") && !empty($filters['provider']) && $filters['provider'] != 'all') {
			if ($filters['provider'] == 'manual') {
				$this->db->where("(s.api_provider_id IS NULL OR s.api_provider_id = '' OR s.add_type = 'manual')");
			} elseif ($filters['provider'] == 'api') {
				$this->db->where("(s.api_provider_id IS NOT NULL AND s.api_provider_id != '' AND s.add_type = 'api')");
			} else {
				$this->db->where("s.api_provider_id", (int)$filters['provider']);
			}
		}
		
		// Price range filter
		if (!empty($filters['price_min']) && is_numeric($filters['price_min'])) {
			$this->db->where("s.price >=", (float)$filters['price_min']);
		}
		if (!empty($filters['price_max']) && is_numeric($filters['price_max'])) {
			$this->db->where("s.price <=", (float)$filters['price_max']);
		}
		
		// Dripfeed filter
		if (isset($filters['dripfeed']) && $filters['dripfeed'] !== '' && $filters['dripfeed'] !== 'all') {
			$this->db->where("s.dripfeed", (int)$filters['dripfeed']);
		}
	}
	
	/**
	 * Get all categories for filter dropdown
	 * 
	 * @return array
	 */
	public function get_all_categories_for_filter(){
		if (get_role("user")) {
			$this->db->where("status", "1");
		}
		$this->db->select("id, ids, name");
		$this->db->from($this->tb_categories);
		$this->db->order_by("sort", 'ASC');
		$query = $this->db->get();
		return $query->result();
	}
	
	/**
	 * Get all API providers for filter dropdown
	 * 
	 * @return array
	 */
	public function get_all_providers_for_filter(){
		$this->db->select("id, name");
		$this->db->from($this->tb_api_providers);
		$this->db->where("status", 1);
		$this->db->order_by("name", 'ASC');
		$query = $this->db->get();
		return $query->result();
	}
	
	/**
	 * Get service statistics for dashboard
	 * 
	 * @return object
	 */
	public function get_services_stats(){
		$stats = new stdClass();
		
		// Total services
		$this->db->from($this->tb_services);
		$stats->total = $this->db->count_all_results();
		
		// Active services
		$this->db->from($this->tb_services);
		$this->db->where("status", 1);
		$stats->active = $this->db->count_all_results();
		
		// Inactive services
		$stats->inactive = $stats->total - $stats->active;
		
		// API services
		$this->db->from($this->tb_services);
		$this->db->where("add_type", 'api');
		$stats->api = $this->db->count_all_results();
		
		// Manual services
		$stats->manual = $stats->total - $stats->api;
		
		return $stats;
	}

}
