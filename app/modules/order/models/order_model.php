<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class order_model extends MY_Model {
	public $tb_users;
	public $tb_order;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;

	public function __construct(){
		$this->tb_categories        = CATEGORIES;
		$this->tb_order             = ORDER;
		$this->tb_users             = USERS;
		$this->tb_services          = SERVICES;
		$this->tb_api_providers   	= API_PROVIDERS;
		parent::__construct();
	}

	/**
	 * GET DASHBOARD DATA FOR ADD PAGE
	 * Retrieves all dashboard statistics for the user
	 * @param int $user_id
	 * @return array
	 */
	public function get_dashboard_data($user_id) {
		$dashboard_data = array();

		// Get user details
		$user = $this->get("balance, spent, role", $this->tb_users, ['id' => $user_id]);
		if (!$user) {
			return $dashboard_data;
		}

		$user_role = $user->role;
		$balance = $user->balance;
		$total_spent = $user->spent;

		// Get currency info
		$current_currency = get_current_currency();
		$currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol', "$");

		// Get decimal settings
		$decimal_point = '.';
		switch (get_option('currency_decimal_separator', 'dot')) {
			case 'comma':
				$decimal_point = ',';
				break;
		}

		$separator = ',';
		switch (get_option('currency_thousand_separator', 'comma')) {
			case 'dot':
				$separator = '.';
				break;
			case 'space':
				$separator = ' ';
				break;
		}

		// Calculate formatted balance with currency conversion
		if (empty($balance) || $balance == 0) {
			$formatted_balance = 0.0000;
		} else {
			$formatted_balance = convert_currency($balance);
			$formatted_balance = currency_format($formatted_balance, get_option('currency_decimal', 2), $decimal_point, $separator);
		}

		// Calculate formatted spent with currency conversion
		if (empty($total_spent) || $total_spent == 0) {
			$formatted_spent = 0.0000;
		} else {
			$formatted_spent = convert_currency($total_spent);
			$formatted_spent = currency_format($formatted_spent, get_option('currency_decimal', 2), $decimal_point, $separator);
		}

		// Get total orders count
		$this->db->select("COUNT(*) AS total_orders");
		$this->db->from($this->tb_order);
		$this->db->where("uid", $user_id);
		$total_orders = $this->db->get()->row()->total_orders;

		// If user is admin - get admin statistics
		if ($user_role === 'admin') {
			// Get total users
			$this->db->select("COUNT(*) AS total_users");
			$this->db->from($this->tb_users);
			$total_users = $this->db->get()->row()->total_users;

			// Get total amount received (sum of transactions)
			$this->db->select("SUM(amount) AS total_received");
			$this->db->from("general_transaction_logs");
			$this->db->where("status", 1);
			$total_received = $this->db->get()->row()->total_received;

			if (empty($total_received) || $total_received == 0) {
				$formatted_total_received = 0.0000;
			} else {
				$formatted_total_received = convert_currency($total_received);
				$formatted_total_received = currency_format($formatted_total_received, get_option('currency_decimal', 2), $decimal_point, $separator);
			}

			// Get total orders (all orders, not just the current user's orders)
			$this->db->select("COUNT(*) AS total_orders_all");
			$this->db->from($this->tb_order);
			$total_orders_all = $this->db->get()->row()->total_orders_all;

			$dashboard_data = array(
				'user_role' => 'admin',
				'currency_symbol' => $currency_symbol,
				'total_received' => $formatted_total_received,
				'total_users' => $total_users,
				'total_orders' => $total_orders_all,
				'balance' => $balance,
				'spent' => $total_spent,
			);
		} else {
			// User dashboard
			$dashboard_data = array(
				'user_role' => 'user',
				'currency_symbol' => $currency_symbol,
				'balance' => $formatted_balance,
				'spent' => $formatted_spent,
				'total_orders' => $total_orders,
				'show_low_balance_warning' => ($balance == 0 || (is_numeric($balance) && $balance < 10)),
			);
		}

		return $dashboard_data;
	}

	/**
	 * GET USER ROLE
	 * @param int $user_id
	 * @return string|null
	 */
	public function get_user_role($user_id) {
		$user = $this->get("role", $this->tb_users, ['id' => $user_id]);
		return $user ? $user->role : null;
	}

	/**
	 * GET WHATSAPP DATA FOR USER
	 * Checks if user has a valid WhatsApp number
	 * @param int $user_id
	 * @return array
	 */
	public function get_whatsapp_data($user_id) {
		$user = $this->get("whatsapp_number", $this->tb_users, ['id' => $user_id]);
		
		$whatsapp_exists = false;
		if ($user && !empty($user->whatsapp_number) && $user->whatsapp_number !== '+92') {
			$whatsapp_exists = true;
		}

		return array(
			'exists' => $whatsapp_exists,
			'number' => $user ? $user->whatsapp_number : '',
		);
	}

	/**
	 * UPDATE USER WHATSAPP NUMBER
	 * @param int $user_id
	 * @param string $whatsapp_number
	 * @return bool
	 */
	public function update_user_whatsapp_number($user_id, $whatsapp_number) {
		// Validate format
		if (!preg_match('/^\+?[0-9]{10,15}$/', $whatsapp_number)) {
			return false;
		}

		// Update in database
		$this->db->update($this->tb_users, 
			array(
				'whatsapp_number' => $whatsapp_number,
				'whatsapp_number_updated' => 1
			), 
			array('id' => $user_id)
		);

		return ($this->db->affected_rows() > 0);
	}

	/**
	 * GET USER INFO FOR ADD PAGE
	 * @param int $user_id
	 * @return object|null
	 */
	public function get_user_info($user_id) {
		return $this->get("first_name, last_name, email, balance, whatsapp_number", $this->tb_users, ['id' => $user_id]);
	}

	/**
	 * GET ANNOUNCEMENT TEXT
	 * @return string
	 */
	public function get_announcement_text() {
		return get_option('new_order_text', '');
	}

	/**
	 * GET VERTICAL IMAGE MODAL DATA
	 * @return array
	 */
	public function get_vertical_image_modal_data() {
		return array(
			'show' => get_option('show_vertical_image_modal', 0),
			'url' => get_option('vertical_image_modal_url', 'https://i.ibb.co/8LZvrpDK/file-000000006374622f80e6350155d31b37.png')
		);
	}

	function get_categories_list(){
		$data  = array();
		$this->db->select("*");
		$this->db->from($this->tb_categories);
		$this->db->where("status", "1");
		$this->db->order_by("sort", 'ASC');
		$query = $this->db->get();

		$categories = $query->result();
		if(!empty($categories)){
			return $categories;
		}
		return false;
	}

	function get_services_list_by_cate($id = ""){
		$data  = array();
		if (!get_role("admin")) {
			$this->db->where("status", "1");
		}
		$this->db->select("*");
		$this->db->from($this->tb_services);
		$this->db->where("cate_id", $id);
		$this->db->order_by("price", "ASC");
		$query = $this->db->get();
		$services = $query->result();
		if(!empty($services)){
			return $services;
		}
		return false;
	}

	function get_service_item($id = ""){
		$data  = array();
		$this->db->select("*");
		$this->db->from($this->tb_services);
		$this->db->where("id", $id);
		$this->db->where("status", "1");
		$query = $this->db->get();
		$service = $query->row();
		if(!empty($service)){
			return $service;
		}
		return false;
	}

	function get_services_by_cate($id = ""){
		$data  = array();
		$this->db->select("*");
		$this->db->from($this->tb_services);
		$this->db->where("cate_id", $id);
		$this->db->where("status", "1");
		$this->db->order_by('price', 'ASC');
		$query = $this->db->get();
		$services = $query->result();
		if(!empty($services)){
			return $services;
		}

		return false;
	}

	function get_order_logs_list($total_rows = false, $status = "", $limit = "", $start = ""){
		$data  = array();
		if (get_role("user")) {
			$this->db->where("o.uid", session("uid"));
		}
		if ($limit != "" && $start >= 0) {
			$this->db->limit($limit, $start);
		}
		$this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
		$this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
		if($status != "all" && !empty($status)){
			$this->db->where("o.status", $status);
		}
		$this->db->where("o.service_type !=", "subscriptions");
		$this->db->where("o.is_drip_feed !=", 1);
		$this->db->order_by("o.id", 'DESC');

		$query = $this->db->get();
		if ($total_rows) {
			$result = $query->num_rows();
			return $result;
		}else{
			$result = $query->result();
			return $result;
		}
		return false;
	}

	// Get Count of orders by status
	function get_count_orders($status = ""){
		if (get_role("user")) {
			$this->db->where("uid", session("uid"));
		}
		$this->db->select("id");
		$this->db->from($this->tb_order);
		if($status != "all" && !empty($status)){
			$this->db->where("status", $status);
		}
		$this->db->where("service_type !=", "subscriptions");
		$this->db->where("is_drip_feed !=", 1);
		$query = $this->db->get();
		return $query->num_rows();
	}

	/**
	 * Get total provider price (formal_charge) for orders by status
	 * @param string $status Order status to filter by
	 * @return float Total provider price
	 */
	function get_total_provider_price($status = ""){
		if (get_role("user")) {
			$this->db->where("uid", session("uid"));
		}
		$this->db->select("SUM(formal_charge) as total_provider_price");
		$this->db->from($this->tb_order);
		if($status != "all" && !empty($status)){
			$this->db->where("status", $status);
		}
		$this->db->where("service_type !=", "subscriptions");
		$this->db->where("is_drip_feed !=", 1);
		$query = $this->db->get();
		$result = $query->row();
		return ($result && $result->total_provider_price) ? (float)$result->total_provider_price : 0;
	}

	function get_orders_logs_by_search($k){
		$k = trim(htmlspecialchars($k));
		if (get_role("user")) {
			$this->db->select('o.*, u.email as user_email, s.name as service_name');
			$this->db->from($this->tb_order." o");
			$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
			$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');

			if ($k != "" && strlen($k) >= 2) {
				$this->db->where("(`o`.`id` LIKE '%".$k."%' ESCAPE '!' OR `o`.`link` LIKE '%".$k."%' ESCAPE '!' OR `o`.`status` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!')");
			}
			$this->db->where("o.service_type !=", "subscriptions");
			$this->db->where("o.is_drip_feed !=", 1);
			$this->db->where("u.id", session("uid"));
			$this->db->order_by("o.id", 'DESC');
			$query = $this->db->get();
			$result = $query->result();

		}else{
			$this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
			$this->db->from($this->tb_order." o");
			$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
			$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
			$this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');

			if ($k != "" && strlen($k) >= 2) {
				$this->db->where("(`o`.`api_order_id` LIKE '%".$k."%' ESCAPE '!' OR `o`.`id` LIKE '%".$k."%' ESCAPE '!' OR `o`.`status` LIKE '%".$k."%' ESCAPE '!' OR `o`.`link` LIKE '%".$k."%' ESCAPE '!' OR  `u`.`email` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!')");
			}
			$this->db->where("o.service_type !=", "subscriptions");
			$this->db->where("o.is_drip_feed !=", 1);
			$this->db->order_by("o.id", 'DESC');

			$query = $this->db->get();
			$result = $query->result();
		}
		return $result;
	}

	// Get Count of orders by Search query
	public function get_count_orders_by_search($search = []){
		$k = htmlspecialchars($search['k']);
		$where_like = "";
		if (get_role("user")) {
			$this->db->where("o.uid", session("uid"));
			$where_like = "(`o`.`id` LIKE '%".$k."%' ESCAPE '!' OR `o`.`link` LIKE '%".$k."%' ESCAPE '!')";
		}else{
			switch ($search['type']) {
				case 1:
					#order id
					$where_like = "`o`.`id` LIKE '%".$k."%' ESCAPE '!'";
					break;
				case 2:
					# API order id
					$where_like = "`o`.`api_order_id` LIKE '%".$k."%' ESCAPE '!'";
					break;

				case 3:
					# Link
					$where_like = "`o`.`link` LIKE '%".$k."%' ESCAPE '!'";
					break;

				case 4:
					# User Email
					$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
					break;
			}
		}
		$this->db->select('o.id, u.email as user_email');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');

		if ($where_like) $this->db->where($where_like);

		$this->db->where("o.service_type !=", "subscriptions");
		$this->db->where("o.is_drip_feed !=", 1);
		$query = $this->db->get();
		$number_row = $query->num_rows();
		
		return $number_row;
	}

	// Search Logs by keywork and search type
	public function search_logs_by_get_method($search, $limit = "", $start = ""){
		$k = htmlspecialchars($search['k']);
		$where_like = "";
		if (get_role("user")) {
			$this->db->select('o.*, u.email as user_email, s.name as service_name');
			$this->db->from($this->tb_order." o");
			$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
			$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');

			$this->db->where("(`o`.`id` LIKE '%".$k."%' ESCAPE '!' OR `o`.`link` LIKE '%".$k."%' ESCAPE '!')");

			$this->db->where("o.service_type !=", "subscriptions");
			$this->db->where("o.is_drip_feed !=", 1);
			$this->db->where("o.uid", session("uid"));
			$this->db->order_by("o.id", 'DESC');
			$this->db->limit($limit, $start);
			$query = $this->db->get();
			$result = $query->result();

		}else{
			switch ($search['type']) {
				case 1:
					#order id
					$where_like = "`o`.`id` LIKE '%".$k."%' ESCAPE '!'";
					break;
				case 2:
					# API order id
					$where_like = "`o`.`api_order_id` LIKE '%".$k."%' ESCAPE '!'";
					break;

				case 3:
					# Link
					$where_like = "`o`.`link` LIKE '%".$k."%' ESCAPE '!'";
					break;

				case 4:
					# User Email
					$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
					break;
			}

			$this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
			$this->db->from($this->tb_order." o");
			$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
			$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
			$this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');

			if ($where_like) $this->db->where($where_like);

			$this->db->where("o.service_type !=", "subscriptions");
			$this->db->where("o.is_drip_feed !=", 1);
			$this->db->order_by("o.id", 'DESC');
			$this->db->limit($limit, $start);

			$query = $this->db->get();
			$result = $query->result();
		}
		return $result;
	}

	function get_log_details($id){
		$this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
		$this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
		$this->db->where("o.main_order_id", $id);
		$this->db->order_by("o.id", 'DESC');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
	
	public function get_bulk_orders($status) {
		$this->db->select("ids");
		$this->db->from($this->tb_order);
		$this->db->where("status", $status);
		$query = $this->db->get();
	
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return [];
	}
	
	function get_top_bestsellers($limit = ""){
		if ($limit == "") {
			$limit = 10;
		}
		$query = "SELECT count(service_id) as total_orders, service_id FROM {$this->tb_order} GROUP BY service_id ORDER BY total_orders DESC LIMIT 30";
		$top_sellers =  $this->db->query($query)->result();
		$result = [];
		$i = 1;
		foreach ($top_sellers as $key => $row) {
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
			$this->db->where("s.id", $row->service_id);
			$this->db->where("s.status", 1);
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			if(!empty($query->row()) && $i <= $limit ){
				$item = $query->row();
				$item->total_orders = $row->total_orders;
				$result[] = $item;
				$i++;
			}
		}
		return $result;
	}

	public function get_whatsapp_config() {
		$config = $this->db->get("whatsapp_config")->row();
		return ($config) ? $config : false;
	}
}