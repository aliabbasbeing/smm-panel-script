<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class currencies extends MX_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('currencies_model', 'model');
	}

	/**
	 * Currency Manager Index Page
	 * Standalone module for managing currencies
	 */
	public function index(){
		// Get all currencies (not just active) for management
		$currencies = $this->model->get_all_currencies();
		
		$data = [
			'module'     => get_class($this),
			'currencies' => $currencies,
		];
		
		$this->template->build('index', $data);
	}

	/**
	 * Get currencies list via AJAX (for lazy loading/pagination)
	 */
	public function get_currencies_ajax(){
		$page = (int)$this->input->get('page', true);
		$limit = (int)$this->input->get('limit', true);
		$search = $this->input->get('search', true);
		$status = $this->input->get('status', true);
		
		$page = max(1, $page);
		$limit = $limit > 0 ? min($limit, 100) : 20;
		$offset = ($page - 1) * $limit;
		
		$currencies = $this->model->get_currencies_filtered($search, $status, $limit, $offset);
		$total = $this->model->count_currencies_filtered($search, $status);
		
		// Ensure limit is at least 1 to prevent division by zero
		$pages = $limit > 0 ? ceil($total / $limit) : 1;
		
		ms([
			'status' => 'success',
			'data' => [
				'currencies' => $currencies,
				'total' => $total,
				'page' => $page,
				'pages' => $pages
			]
		]);
	}

	/**
	 * Set user's selected currency
	 */
	public function set_currency(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$currency_code = $this->input->post('currency_code', true);
		
		if (empty($currency_code)) {
			ms([
				'status'  => 'error',
				'message' => 'Currency code is required'
			]);
		}

		// Verify currency exists and is active
		$currency = $this->model->get_by_code($currency_code);
		
		if (!$currency) {
			ms([
				'status'  => 'error',
				'message' => 'Invalid currency code'
			]);
		}

		// Store in session
		$this->session->set_userdata('selected_currency', $currency_code);
		
		// Also store in cookie for persistence (30 days)
		$this->input->set_cookie([
			'name'   => 'selected_currency',
			'value'  => $currency_code,
			'expire' => 2592000 // 30 days
		]);

		ms([
			'status'  => 'success',
			'message' => 'Currency changed successfully',
			'data'    => [
				'code'   => $currency->code,
				'symbol' => $currency->symbol,
				'name'   => $currency->name
			]
		]);
	}

	/**
	 * Get user's selected currency or default
	 */
	public function get_selected_currency(){
		// Check session first
		$selected = $this->session->userdata('selected_currency');
		
		// If not in session, check cookie
		if (!$selected) {
			$selected = $this->input->cookie('selected_currency', true);
		}

		// If still not found, get default
		if ($selected) {
			$currency = $this->model->get_by_code($selected);
			if ($currency) {
				return $currency;
			}
		}

		return $this->model->get_default_currency();
	}

	/**
	 * Update exchange rate
	 */
	public function update_rate(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);
		$exchange_rate = $this->input->post('exchange_rate', true);

		if (!$id || !$exchange_rate) {
			ms([
				'status'  => 'error',
				'message' => 'Missing required fields'
			]);
		}

		$this->db->where('id', $id);
		$this->db->update('currencies', ['exchange_rate' => $exchange_rate]);

		ms([
			'status'  => 'success',
			'message' => 'Exchange rate updated successfully'
		]);
	}

	/**
	 * Set default currency
	 */
	public function set_default(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);

		if (!$id) {
			ms([
				'status'  => 'error',
				'message' => 'Missing currency ID'
			]);
		}

		// Unset all defaults
		$this->db->update('currencies', ['is_default' => 0]);

		// Set new default
		$this->db->where('id', $id);
		$this->db->update('currencies', ['is_default' => 1]);

		ms([
			'status'  => 'success',
			'message' => 'Default currency updated successfully'
		]);
	}

	/**
	 * Toggle currency status
	 */
	public function toggle_status(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);
		$status = $this->input->post('status', true);

		if (!$id) {
			ms([
				'status'  => 'error',
				'message' => 'Missing currency ID'
			]);
		}

		$this->db->where('id', $id);
		$this->db->update('currencies', ['status' => $status]);

		ms([
			'status'  => 'success',
			'message' => 'Currency status updated'
		]);
	}

	/**
	 * Add new currency
	 */
	public function add_currency(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$data = [
			'code'          => strtoupper($this->input->post('code', true)),
			'name'          => $this->input->post('name', true),
			'symbol'        => $this->input->post('symbol', true),
			'exchange_rate' => $this->input->post('exchange_rate', true),
			'status'        => 1,
			'is_default'    => 0
		];

		// Check if code already exists
		$exists = $this->db->get_where('currencies', ['code' => $data['code']])->row();
		if ($exists) {
			ms([
				'status'  => 'error',
				'message' => 'Currency code already exists'
			]);
		}

		$this->db->insert('currencies', $data);

		ms([
			'status'  => 'success',
			'message' => 'Currency added successfully'
		]);
	}

	/**
	 * Fetch latest exchange rates from API
	 * Can be called via button click or cron job
	 */
	public function fetch_rates(){
		// Get default currency
		$default_currency = $this->model->get_default_currency();
		
		if (!$default_currency) {
			ms([
				'status'  => 'error',
				'message' => 'No default currency set'
			]);
		}

		$base_code = $default_currency->code;
		
		// Use exchangerate-api.com (free tier, no API key required for basic usage)
		// Alternative: Use fixer.io, openexchangerates.org, etc.
		$api_url = "https://api.exchangerate-api.com/v4/latest/{$base_code}";
		
		// Fetch exchange rates
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		// SSL verification enabled for security
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code !== 200 || !$response) {
			ms([
				'status'  => 'error',
				'message' => 'Failed to fetch exchange rates from API'
			]);
		}
		
		$data = json_decode($response, true);
		
		if (!isset($data['rates']) || empty($data['rates'])) {
			ms([
				'status'  => 'error',
				'message' => 'Invalid API response'
			]);
		}
		
		$rates = $data['rates'];
		$updated_count = 0;
		
		// Update exchange rates for all active currencies
		$currencies = $this->model->get_active_currencies();
		
		foreach ($currencies as $currency) {
			// Skip default currency (it stays at 1.0)
			if ($currency->is_default) {
				continue;
			}
			
			// Check if we have a rate for this currency
			if (isset($rates[$currency->code])) {
				$new_rate = $rates[$currency->code];
				
				// The API returns rates in format: 1 BASE = X TARGET
				// Example: With PKR as base, API returns: "USD": 0.00353876
				// This means: 1 PKR = 0.00353876 USD
				// Our database stores rates in the same format (exchange_rate for USD would be 0.00353876)
				// So we can directly use the rate from API
				$this->db->where('id', $currency->id);
				$this->db->update('currencies', [
					'exchange_rate' => $new_rate,
					'updated_at' => date('Y-m-d H:i:s')
				]);
				
				$updated_count++;
			}
		}
		
		ms([
			'status'  => 'success',
			'message' => "Successfully updated {$updated_count} exchange rates",
			'data' => [
				'updated_count' => $updated_count,
				'base_currency' => $base_code,
				'timestamp' => date('Y-m-d H:i:s')
			]
		]);
	}

	/**
	 * Cron-friendly endpoint to fetch rates
	 * Access via: yoursite.com/currencies/cron_fetch_rates
	 * This endpoint bypasses authentication for cron job access
	 */
	public function cron_fetch_rates(){
		// Optional: Add authentication token for security
		$token = $this->input->get('token', true);
		$expected_token = get_option('currency_cron_token', '');
		
		if ($expected_token && $token !== $expected_token) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Invalid token'
			]);
			return;
		}
		
		// Get default currency
		$default_currency = $this->model->get_default_currency();
		
		if (!$default_currency) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'No default currency set'
			]);
			return;
		}

		$base_code = $default_currency->code;
		
		// Use exchangerate-api.com (free tier, no API key required for basic usage)
		$api_url = "https://api.exchangerate-api.com/v4/latest/{$base_code}";
		
		// Fetch exchange rates
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		// SSL verification enabled for security
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code !== 200 || !$response) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to fetch exchange rates from API'
			]);
			return;
		}
		
		$data = json_decode($response, true);
		
		if (!isset($data['rates']) || empty($data['rates'])) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Invalid API response'
			]);
			return;
		}
		
		$rates = $data['rates'];
		$updated_count = 0;
		
		// Update exchange rates for all active currencies
		$currencies = $this->model->get_active_currencies();
		
		foreach ($currencies as $currency) {
			// Skip default currency (it stays at 1.0)
			if ($currency->is_default) {
				continue;
			}
			
			// Check if we have a rate for this currency
			if (isset($rates[$currency->code])) {
				$new_rate = $rates[$currency->code];
				
				// The API returns rates in format: 1 BASE = X TARGET
				// Example: With PKR as base, API returns: "USD": 0.00353876
				// This means: 1 PKR = 0.00353876 USD
				// Our database stores rates in the same format (exchange_rate for USD would be 0.00353876)
				// So we can directly use the rate from API
				$this->db->where('id', $currency->id);
				$this->db->update('currencies', [
					'exchange_rate' => $new_rate,
					'updated_at' => date('Y-m-d H:i:s')
				]);
				
				$updated_count++;
			}
		}
		
		echo json_encode([
			'status'  => 'success',
			'message' => "Successfully updated {$updated_count} exchange rates",
			'data' => [
				'updated_count' => $updated_count,
				'base_currency' => $base_code,
				'timestamp' => date('Y-m-d H:i:s')
			]
		]);
	}

	/**
	 * Update currency details (name, symbol)
	 */
	public function update_currency(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);
		$name = $this->input->post('name', true);
		$symbol = $this->input->post('symbol', true);

		if (!$id || !$name || !$symbol) {
			ms([
				'status'  => 'error',
				'message' => 'Missing required fields'
			]);
		}

		// Get the currency to ensure it exists
		$currency = $this->db->get_where('currencies', ['id' => $id])->row();
		if (!$currency) {
			ms([
				'status'  => 'error',
				'message' => 'Currency not found'
			]);
		}

		// Update currency details
		$this->db->where('id', $id);
		$this->db->update('currencies', [
			'name' => $name,
			'symbol' => $symbol
		]);

		ms([
			'status'  => 'success',
			'message' => 'Currency updated successfully'
		]);
	}

	/**
	 * Delete a currency
	 */
	public function delete_currency(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);

		if (!$id) {
			ms([
				'status'  => 'error',
				'message' => 'Missing currency ID'
			]);
		}

		// Get the currency to ensure it exists and is not default
		$currency = $this->db->get_where('currencies', ['id' => $id])->row();
		if (!$currency) {
			ms([
				'status'  => 'error',
				'message' => 'Currency not found'
			]);
		}

		// Cannot delete default currency
		if ($currency->is_default) {
			ms([
				'status'  => 'error',
				'message' => 'Cannot delete default currency. Set another currency as default first.'
			]);
		}

		// Delete the currency
		$this->db->where('id', $id);
		$this->db->delete('currencies');

		ms([
			'status'  => 'success',
			'message' => 'Currency deleted successfully'
		]);
	}

	/**
	 * Batch update currencies (for bulk operations)
	 */
	public function batch_update(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$action = $this->input->post('action', true);
		$ids = $this->input->post('ids', true);

		if (!$action || !is_array($ids) || empty($ids)) {
			ms([
				'status'  => 'error',
				'message' => 'Invalid parameters'
			]);
		}

		$updated = 0;
		foreach ($ids as $id) {
			$id = (int)$id;
			if ($id <= 0) continue;

			// Check if currency exists and is not default (for certain actions)
			$currency = $this->db->get_where('currencies', ['id' => $id])->row();
			if (!$currency) continue;

			switch ($action) {
				case 'activate':
					$this->db->where('id', $id)->update('currencies', ['status' => 1]);
					$updated++;
					break;
				case 'deactivate':
					// Cannot deactivate default currency
					if (!$currency->is_default) {
						$this->db->where('id', $id)->update('currencies', ['status' => 0]);
						$updated++;
					}
					break;
				case 'delete':
					// Cannot delete default currency
					if (!$currency->is_default) {
						$this->db->where('id', $id)->delete('currencies');
						$updated++;
					}
					break;
			}
		}

		ms([
			'status'  => 'success',
			'message' => "Successfully processed {$updated} currencies"
		]);
	}
}
