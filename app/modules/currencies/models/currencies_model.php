<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class currencies_model extends MY_Model {
	public $tb_currencies;

	public function __construct(){
		$this->tb_currencies = 'currencies';
		parent::__construct();
	}

	/**
	 * Get all active currencies
	 */
	public function get_active_currencies(){
		$this->db->where('status', 1);
		$this->db->order_by('is_default', 'DESC');
		$this->db->order_by('name', 'ASC');
		return $this->db->get($this->tb_currencies)->result();
	}

	/**
	 * Get default currency
	 */
	public function get_default_currency(){
		$this->db->where('is_default', 1);
		$this->db->where('status', 1);
		$currency = $this->db->get($this->tb_currencies)->row();
		
		// If no default found, return first active currency
		if (!$currency) {
			$this->db->where('status', 1);
			$this->db->order_by('id', 'ASC');
			$currency = $this->db->get($this->tb_currencies)->row();
		}
		
		return $currency;
	}

	/**
	 * Get currency by code
	 */
	public function get_by_code($code){
		$this->db->where('code', $code);
		$this->db->where('status', 1);
		return $this->db->get($this->tb_currencies)->row();
	}

	/**
	 * Convert amount from default currency to target currency
	 */
	public function convert($amount, $to_currency_code){
		$default = $this->get_default_currency();
		$target = $this->get_by_code($to_currency_code);
		
		if (!$target || !$default) {
			return $amount;
		}
		
		// Convert: amount * (target_rate / default_rate)
		$converted = $amount * ($target->exchange_rate / $default->exchange_rate);
		
		return $converted;
	}
}
