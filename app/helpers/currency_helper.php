<?php 

/**
 *
 * Currency function for paypal
 *
 */
if (!function_exists("currency_codes")) {
	function currency_codes(){
		$data = array(
			"AUD" => "Australian dollar",
			"BRL" => "Brazilian dollar",
			"CAD" => "Canadian dollar",
			"CZK" => "Czech koruna",
			"DKK" => "Danish krone",
			"EUR" => "Euro",
			"HKD" => "Hong Kong dollar",
			"HUF" => "Hungarian forint",
			"INR" => "Indian rupee",
			"ILS" => "Israeli",
			"JPY" => "Japanese yen",
			"MYR" => "Malaysian ringgit",
			"MXN" => "Mexican peso",
			"TWD" => "New Taiwan dollar",
			"NZD" => "New Zealand dollar",
			"NOK" => "Norwegian krone",
			"PHP" => "Philippine peso",
			"PLN" => "Polish złoty",
			"GBP" => "Pound sterling",
			"RUB" => "Russian ruble",
			"SGD" => "Singapore dollar",
			"SEK" => "Swedish krona",
			"CHF" => "Swiss franc",
			"THB" => "Thai baht",
			"USD" => "United States dollar",
		);

		return $data;
	}
}

if (!function_exists("currency_format")) {
	function currency_format($number, $number_decimal = "", $decimalpoint = "", $separator = ""){
		$decimal = 2;

		if ($number_decimal == "") {
			$decimal = get_option('currency_decimal', 2);
		}else{
			$decimal = $number_decimal;
		}

		if ($decimalpoint == "") {
			$decimalpoint = ".";
		}

		if ($separator == "") {
			$separator = ",";
		}	

		$number = number_format($number, $decimal, $decimalpoint, $separator);
		return $number;
	}
}

if (!function_exists("currency_format")) {
	function local_currency_code(){
		$data = array(   
		      	'USD',
			    'EUR',
			    'JPY',
			    'GBP',
			    'AUD',
			    'CAD',
			    'CHF',
			    'CNY',
			    'SEK',
			    'NZD',
			    'MXN',
			    'SGD',
			    'HKD',
			    'NOK',
			    'KRW',
			    'TRY',
			    'RUB',
			    'INR',
			    'BRL',
			    'ZAR',
			    'AED',
			    'AFN',
			    'ALL',
			    'AMD',
			    'ANG',
			    'AOA',
			    'ARS',
			    'AWG',
			    'AZN',
			    'BAM',
			    'BBD',
			    'BDT',
			    'BGN',
			    'BHD',
			    'BIF',
			    'BMD',
			    'BND',
			    'BOB',
			    'BSD',
			    'BTN',
			    'BWP',
			    'BYN',
			    'BZD',
			    'CDF',
			    'CLF',
			    'CLP',
			    'COP',
			    'CRC',
			    'CUC',
			    'CUP',
			    'CVE',
			    'CZK',
			    'DJF',
			    'DKK',
			    'DOP',
			    'DZD',
			    'EGP',
			    'ERN',
			    'ETB',
			    'FJD',
			    'FKP',
			    'GEL',
			    'GGP',
			    'GHS',
			    'GIP',
			    'GMD',
			    'GNF',
			    'GTQ',
			    'GYD',
			    'HNL',
			    'HRK',
			    'HTG',
			    'HUF',
			    'IDR',
			    'ILS',
			    'IMP',
			    'IQD',
			    'IRR',
			    'ISK',
			    'JEP',
			    'JMD',
			    'JOD',
			    'KES',
			    'KGS',
			    'KHR',
			    'KMF',
			    'KPW',
			    'KWD',
			    'KYD',
			    'KZT',
			    'LAK',
			    'LBP',
			    'LKR',
			    'LRD',
			    'LSL',
			    'LYD',
			    'MAD',
			    'MDL',
			    'MGA',
			    'MKD',
			    'MMK',
			    'MNT',
			    'MOP',
			    'MRO',
			    'MUR',
			    'MVR',
			    'MWK',
			    'MYR',
			    'MZN',
			    'NAD',
			    'NGN',
			    'NIO',
			    'NPR',
			    'OMR',
			    'PAB',
			    'PEN',
			    'PGK',
			    'PHP',
			    'PKR',
			    'PLN',
			    'PYG',
			    'QAR',
			    'RON',
			    'RSD',
			    'RWF',
			    'SAR',
			    'SBD',
			    'SCR',
			    'SDG',
			    'SHP',
			    'SLL',
			    'SOS',
			    'SRD',
			    'SSP',
			    'STD',
			    'SVC',
			    'SYP',
			    'SZL',
			    'THB',
			    'TJS',
			    'TMT',
			    'TND',
			    'TOP',
			    'TTD',
			    'TWD',
			    'TZS',
			    'UAH',
			    'UGX',
			    'UYU',
			    'UZS',
			    'VEF',
			    'VND',
			    'VUV',
			    'WST',
			    'XAF',
			    'XAG',
			    'XAU',
			    'XCD',
			    'XDR',
			    'XOF',
			    'XPD',
			    'XPF',
			    'XPT',
			    'YER',
			    'ZMW',
			    'ZWL',
		);
		return $data;
	}

}

/**
 * Get current selected currency
 */
if (!function_exists("get_current_currency")) {
	function get_current_currency(){
		$CI = &get_instance();
		
		// Load currencies module if not loaded
		if (!isset($CI->currencies_model)) {
			$CI->load->model('currencies/currencies_model');
		}
		
		// Check session first
		$selected = $CI->session->userdata('selected_currency');
		
		// If not in session, check cookie
		if (!$selected) {
			$selected = $CI->input->cookie('selected_currency', true);
		}

		// If found, verify it's valid
		if ($selected) {
			$currency = $CI->currencies_model->get_by_code($selected);
			if ($currency) {
				return $currency;
			}
		}

		// Return default currency
		return $CI->currencies_model->get_default_currency();
	}
}

/**
 * Convert amount to current selected currency
 */
if (!function_exists("convert_currency")) {
	function convert_currency($amount, $from_code = null){
		$CI = &get_instance();
		
		// Load currencies module if not loaded
		if (!isset($CI->currencies_model)) {
			$CI->load->model('currencies/currencies_model');
		}
		
		$current_currency = get_current_currency();
		
		if (!$current_currency) {
			return $amount;
		}
		
		// If from_code is not specified, assume it's the default currency
		if ($from_code === null) {
			$default_currency = $CI->currencies_model->get_default_currency();
			$from_code = $default_currency ? $default_currency->code : 'USD';
		}
		
		// If already in target currency, no conversion needed
		if ($from_code === $current_currency->code) {
			return $amount;
		}
		
		return $CI->currencies_model->convert($amount, $current_currency->code);
	}
}

/**
 * Format amount with current currency symbol
 */
if (!function_exists("format_currency")) {
	function format_currency($amount, $convert = true, $number_decimal = "", $decimalpoint = "", $separator = ""){
		$current_currency = get_current_currency();
		
		if (!$current_currency) {
			// Fallback to old method
			$symbol = get_option('currency_symbol', '$');
			return $symbol . currency_format($amount, $number_decimal, $decimalpoint, $separator);
		}
		
		// Convert if requested
		if ($convert) {
			$amount = convert_currency($amount);
		}
		
		// Format the number
		$formatted = currency_format($amount, $number_decimal, $decimalpoint, $separator);
		
		return $current_currency->symbol . $formatted;
	}
}

/**
 * Get all active currencies for display
 */
if (!function_exists("get_active_currencies")) {
	function get_active_currencies(){
		$CI = &get_instance();
		
		// Load currencies module if not loaded
		if (!isset($CI->currencies_model)) {
			$CI->load->model('currencies/currencies_model');
		}
		
		return $CI->currencies_model->get_active_currencies();
	}
}
