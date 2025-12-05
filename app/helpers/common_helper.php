<?php
if(!function_exists('post_get')){
	function post_get($name = ""){
		$CI = &get_instance();
		if($name != ""){
			return $CI->input->post_get(trim($name));
		}else{
			return $CI->input->post_get();
		}
	}
}

if(!function_exists('get')){
	function get($name = ""){
		$CI = &get_instance();
		$result = $CI->input->get(trim($name));
		$result = strip_tags($result);
		$result = html_entity_decode($result);
		$result = urldecode($result);
		$result = addslashes($result);
		return $result;
	}
}


if(!function_exists('post')){
	function post($name = "", $xss_clean = true){
		$CI = &get_instance();
		if($name != ""){
			// Pass xss_clean to CI input to control XSS filtering at source
			$post = $CI->input->post(trim($name), $xss_clean);
			if(is_string($post)){
				$result = addslashes($post);
				// Only strip tags if xss_clean is true (default behavior)
				if($xss_clean){
					$result = strip_tags($result);
				}
			}else{
				$result = $post;
			}
			return $result;
		}else{
			return $CI->input->post();
		}
	}
}

function xss_clean($data) {
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

	do
	{
	    // Remove really unwanted tags
	    $old_data = $data;
	    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	}
	while ($old_data !== $data);

	// we are done...
	return $data;
}

/**
 * Update configure file
 * @param $item
 * @return mixed
 */
if (!function_exists('get_configs')) {
    function get_configs($item_name, $filename = "") {
    	if ($filename == "") {
    		$filename = 'config';
    	}
        $CI = &get_instance();
        $CI->load->config($filename);
        $item = $CI->config->item($item_name);
        return $item;
    }
}

/**
 * Update configure file
 * @param $item, $filename
 * @return mixed
 */
if (!function_exists('update_configs')) {
    function update_configs($item_name, $item_value, $filename = "") {
    	if ($filename == "") {
    		$filename = 'app_configs';
    	}
        $CI = &get_instance();
        $CI->load->config($filename);
        $item = $CI->config->set_item($item_name, $item_value);
    }
}

/**
 * Get Value from JSone string
 * @param $dataJson, $key
 * @return index of key
 */
if(!function_exists('get_value')){
	function get_value($dataJson, $key, $parseArray = false, $return = false){
		if(is_string($dataJson)){
			$dataJson = json_decode($dataJson);
		}

		if(is_object($dataJson)){
			if(isset($dataJson->$key)){
				if($parseArray){
					return (array)$dataJson->$key;
				}else{
					return $dataJson->$key;
				}
			}
		}else if(is_array($dataJson)){
			if(isset($dataJson[$key])){
				return $dataJson[$key];
			}
		}else{
			return $dataJson;
		}
		
		return $return;
	}
}

if(!function_exists('get_secure')){
	function get_secure($name = ""){
		$CI = &get_instance();
		return filter_input_xss($CI->input->get(trim($name)));
	}
}

if(!function_exists('remove_empty_value')){
	function remove_empty_value($data){
		if(!empty($data)){
			return array_filter($data, function($value) {
			    return ($value !== null && $value !== false && $value !== ''); 
			});
		}else{
			return false;
		}
	}
}

if(!function_exists('get_random_value')){
	function get_random_value($data){
		if(is_array($data) && !empty($data)){
			$index = array_rand($data);
			return $data[$index];
		}else{
			return false;
		}
	}
}

if(!function_exists('get_random_values')){
	function get_random_values($data, $limit){
		if(is_array($data) && !empty($data)){
			shuffle($data);
			if(count($data) < $limit){
				$limit = count($data);
			}

			return array_slice($data, 0, $limit);
		}else{
			return false;
		}
	}
}

if(!function_exists('specialchar_decode')){
	function specialchar_decode($input){
		$input = str_replace("\\'", "'", $input);
		$input = str_replace('\"', '"', $input);
        $input = htmlspecialchars_decode($input, ENT_QUOTES);
		return $input;
	}
}

if(!function_exists('filter_input_xss')){
	function filter_input_xss($input){
        $input = htmlspecialchars($input, ENT_QUOTES);
		return $input;
	}
}

if(!function_exists('ms')){
	function ms($array){
		print_r(json_encode($array));
		exit(0);
	}
}

/**
 * @param string $status error/success
 * @param string $message
 * @return Print Message
 */
if(!function_exists('_validation')){
	function _validation($status, $ms){
		ms(['status' => $status, 'message' => $ms]);
	}
}

if (!function_exists('get_json_content')) {
    function get_json_content($path , $data = []) {
    	if ($data) {
    		return json_decode(file_get_contents($path.'?'.http_build_query($data)));
    	}else{
	        if (file_exists($path)) {
				return json_decode(file_get_contents($path));
			}else{
				return false;
			}
    	}
    }
}

if (!function_exists('ids')) {
	function ids(){
		$CI = &get_instance();
		return md5($CI->encryption->encrypt(time()));
	};
}

if (!function_exists('session')){
	function session($input){
		$CI = &get_instance();

		if ($input == 'uid' && session('uid_tmp')) {
			return session('uid_tmp');
		}
		return $CI->session->userdata($input);
	}
}

if (!function_exists('set_session')){
	function set_session($name,$input){
		$CI = &get_instance();
		return $CI->session->set_userdata($name,$input);
	}
}

if (!function_exists('unset_session')){
	function unset_session($name){
		$CI = &get_instance();
		return $CI->session->unset_userdata($name);
	}
}

if (!function_exists('encrypt_encode')) {
	function encrypt_encode($text){
		$CI = &get_instance();
		return $CI->encryption->encrypt($text);
	};
}

if (!function_exists('encrypt_decode')) {
	function encrypt_decode($key){
		$CI = &get_instance();
		return $CI->encryption->decrypt($key);
	};
}

if (!function_exists('segment')){
	function segment($index){ 
		$CI = &get_instance();
        return $CI->uri->segment($index);
	}
}

if (!function_exists('ini_params')) {
	function ini_params($type){
		switch ($type) {
			case '1':
				return ['type' => base64_decode('aW5zdGFsbA==') , 'main' => 1, base64_decode('ZG9tYWlu') => urlencode(base_url())];
				break;
			case '2':
				return ['type' => 'upgrade', base64_decode('ZG9tYWlu') => urlencode(base_url())];
				break;
			case '3':
				return ['type' => base64_decode('aW5zdGFsbA==') , 'main' => 0, base64_decode('ZG9tYWlu') => urlencode(base_url())];
				break;	
		}
	}
} 

function __curl($url, $zipPath = ""){
	$zipResource = fopen($zipPath, "w");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt($ch, CURLOPT_FILE, $zipResource);
	$page = curl_exec($ch);
	if(!$page) {
		ms(array(
			"status" 	=> "error",
			"message"   => "Error :- ".curl_error($ch),
		));
	}
	curl_close($ch);
}

if(!function_exists("__inst")){
	function _inst($result){
		if (empty($result)) {
			ms(array(
				"status"  => "error", 
				"message" => 'There was an error processing your request. Please contact author to get a support'
			));
		}
		if ((isset($result->status) &&  $result->status == 'error')) {
			ms(array(
				"status"  => "error", 
				"message" => $result->message
			));
		}
		if (isset($result->status) &&  $result->status = 'success') {
			$result_object = explode("{|}", $result->response);
			$file_path = 'files.zip';
			__curl(base64_decode($result_object[2]), $file_path);
			if (filesize($file_path) <= 1) {
				ms(array(
					"status" 	=> "error",
					"message"   => "There was an error processing your request. Please contact author to get a support",
				));
			}
			$zip = new ZipArchive;
			if($zip->open($file_path) != TRUE){
				ms(array(
					"status" 	=> "error",
					"message"   => "Error :- Unable to open the Zip File",
				));
			} 
			$zip->extractTo("./");
			$zip->close();
			@unlink($file_path);
			return $result_object;
		}
	}
}

function extract_zip_file($output_filename){
	$zip = new ZipArchive;
	$extractPath = $output_filename;
	if($zip->open($zipFile) != "true"){
		ms(array(
			"status" 	=> "error",
			"message"   => "Error :- Unable to open the Zip File",
		));
	} 
	$zip->extractTo($extractPath);
	$zip->close();
}


if (!function_exists('cn')) {
	function cn($module=""){
		return PATH.$module;
	};
}

if (!function_exists('load_404')) {
	function load_404(){
		$CI = &get_instance();
		return	$CI->load->view("layouts/404.php");
	};
}

if (!function_exists('time_elapsed_string')) {
	function time_elapsed_string($datetime, $full = false) {
	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . lang($v . ($diff->$k > 1 ? 's' : ''));
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' '.lang('ago') : lang('just_now');
	}
}

if (!function_exists('ajax_page')) {
	function ajax_page(){
		$CI = &get_instance();
		if(!post()){
			$CI = &get_instance();
			$CI->load->view("layouts/404.php");
			return false;
		}else{
			return true;
		}
	};
}

if (!function_exists('require_all')) {
	function require_all($dir = "", $depth=0) {
		if($dir == ""){
			$segment = segment(1);
			$dir = APPPATH."../public/".$segment."/config/constants/";
		}

	    // require all php files
	    $scan = glob("$dir/*");
	    foreach ($scan as $path) {
	        if (preg_match('/\.php$/', $path)) {
	            require_once $path;
	        }
	        elseif (is_dir($path)) {
	            require_all($path, $depth+1);
	        }
	    }
	}
}

if (!function_exists('get_name_folder_from_dir')) {
	function get_name_folder_from_dir($dir = "") {
		if($dir == ""){
			$dir = APPPATH."../themes";
		}
	    // require all php files
	    $dirs = glob($dir . '/*' , GLOB_ONLYDIR);

	    $folder_names = [];
	    foreach ($dirs as $folder_path) {
		    $folder_names[] = basename($folder_path ); 
		}

		if (!empty($folder_names)) {
			return $folder_names;
		}else{
			return [];
		}
	}
}

if (!function_exists('get_all_file_from_folder')) {
	function get_all_file_from_folder($dir = "") {
		$data = array();
		if($dir == ""){
			$segment = segment(1);
			$dir = APPPATH."../public/".$segment."/config/constants/";
		}

	    // require all php files
	    $scan = glob("$dir/*");
	    foreach ($scan as $path) {
	        if (preg_match('/\.php$/', $path)) {
	        	$data[] = $path;
	        }
	    }

	    return $data;
	}
}



if (!function_exists('get_path_module')) {
	function get_path_module(){
		$CI = &get_instance();
		return APPPATH.'modules/'.$CI->router->fetch_module().'/';
	}
}

if (!function_exists('folder_size')) {
	function folder_size($dir){
	    $size = 0;
	    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
	        $size += is_file($each) ? filesize($each) : folderSize($each);
	    }
	    return $size;
	}
}

if (!function_exists('pr')) {
    function pr($data, $type = 0) {
        print '<pre>';
        print_r($data);
        print '</pre>';
        if ($type != 0) {
            exit();
        }
    }
}

if(!function_exists('pr_sql')){
	function pr_sql($type=0){
		$CI = &get_instance();
		$sql = $CI->db->last_query();
		pr($sql,$type);
	}
}

// escape output data before print
if(!function_exists('_echo')){
	function _echo($output, $is_image = TRUE){
		if ($is_image) {
			$output = htmlspecialchars($output);
		}
		echo $output;
	}
}

if(!function_exists("convert_datetime")){
	function convert_datetime($datetime){
		return date("h:iA M d, Y", strtotime($datetime));
	}
}

if(!function_exists("convert_date")){
	function convert_date($date){
		return date("M d, Y", strtotime($date));
	}
}

if(!function_exists("convert_datetime_sql")){
	function convert_datetime_sql($datetime){
		return date("Y-m-d H:i:s", get_to_time($datetime));
	}
}

if(!function_exists("convert_date_sql")){
	function convert_date_sql($date){
		return date("Y-m-d", get_to_time($date));
	}
}

if(!function_exists("validateDate")){
	function validateDate($date, $format = 'Y-m-d'){
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}
}

if(!function_exists("get_to_time")){
	function get_to_time($date){
		if(is_numeric($date)){
			return $date;
		}else{
			return strtotime(str_replace('/', '-', $date));
		}
	}
}

if(!function_exists("get_to_day")){
	function get_to_day($date, $fulltime = true){
		$strtime = strtotime(str_replace('/', '-', $date));
		if($fulltime){
			return date("Y-m-d H:i:s", $strtime);
		}else{
			return date("Y-m-d", $strtime);
		}
	}
}

if(!function_exists("row")){
	function row($data, $field){
		if(is_object($data)){
			if(isset($data->$field)){
				return $data->$field;
			}else{
				return "";
			}
		}

		if(is_array($data)){
			if(isset($data[$field])){
				return $data[$field];
			}else{
				return "";
			}
		}
	}
}


if (!function_exists('tz_convert')){
	function tz_convert($timezone) {
		date_default_timezone_set($timezone);
	  	$zones_array = array();
	  	$timestamp = time();
	  	foreach(timezone_identifiers_list() as $key => $zone) {
	   		if($zone == $timezone){
	   			return date('P', $timestamp);
	   		}
	  	}
		
	  	return false;
	}
}

if (!function_exists('get_line_with_string')){
	function get_line_with_string($fileName, $str) {
		if(is_file($fileName)){
	    	$lines = file($fileName);
		    foreach ($lines as $lineNumber => $line) {
		        if (strpos($line, $str) !== false) {
		            return trim(str_replace("/*", "", str_replace("*/", "", $line)));
		        }
		    }
		}else{
			$lines = $fileName;
		}
		
	    return false;
	}
}

if (!function_exists('get_timezone_user')){
	function get_timezone_user($datetime, $convert = false, $uid = 0){
		$datetime = get_to_time($datetime);
		$datetime = is_numeric($datetime)?date("Y-m-d H:i:s", $datetime):$datetime;

		$uid = session("uid")?session("uid"):$uid;
		$CI = &get_instance();

		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}

		$user = $CI->help_model->get("timezone", USERS, "id = '".$uid."'");
		if(!empty($user)){
			$date = new DateTime($datetime, new DateTimeZone(TIMEZONE));
			$date->setTimezone(new DateTimeZone($user->timezone));
			$result = $date->format('Y-m-d H:i:s');
			return $convert?convert_datetime($result):$result;
		}else{
			return $convert?convert_datetime($datetime):$result;
		}
	}
}

if (!function_exists('get_timezone_system')){
	function get_timezone_system($datetime, $convert = false, $uid = 0){
		$datetime = get_to_time($datetime);
		$datetime = is_numeric($datetime)?date("Y-m-d H:i:s", $datetime):$datetime;

		$uid = session("uid")?session("uid"):$uid;
		$CI = &get_instance();

		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}

		$user = $CI->help_model->get("timezone", USERS, "id = '".$uid."'");
		if(!empty($user)){
			$date = new DateTime($datetime, new DateTimeZone($user->timezone));
			$date->setTimezone(new DateTimeZone(TIMEZONE));
			$result = $date->format('Y-m-d H:i:s');  
			return $convert?convert_datetime($result):$result;
		}else{
			return $convert?convert_datetime($datetime):$result;
		}
	}
}


if(!function_exists("delete_option")){
	function delete_option($key){
		$CI = &get_instance();
		$CI->db->delete(OPTIONS, array("name" => $key));
	}
}



if(!function_exists("get_payment")){
	function get_payment(){
		if (is_dir(APPPATH."modules/payment")) {
			return true;
		}else{
			return false;
		}
	}
}



if(!function_exists("get_client_ip")){
	function get_client_ip() {
	    if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');

			if (strstr($ip, ',')) {
				$tmp = explode(',', $ip);
				$ip = trim($tmp[0]);
			}
		} else {
			$ip = getenv('REMOTE_ADDR');
		}

	    return $ip;
	}
}

if(!function_exists("info_client_ip")){
	function info_client_ip(){
		$result = get_curl("https://timezoneapi.io/api/ip");

		$result = json_decode($result);
		if(!empty($result)){
			return $result;
		}
		return false;
	}
}

function get_location_info_by_ip($ip_address){
	$result = (object)array();
    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip_address));    
    if($ip_data && $ip_data->geoplugin_countryName != null){
        $result->country     = $ip_data->geoplugin_countryName;
        $result->timezone    = $ip_data->geoplugin_timezone;
        $result->city        = $ip_data->geoplugin_city;
    }else{
    	$result->country     = 'Unknown';
        $result->timezone    = 'Unknown';
        $result->city        = 'Unknown';
    }
    return $result;
}

if(!function_exists("get_curl")){
	function get_curl($url){
		$user_agent='Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3B48b Safari/419.3';

		$headers = array
		(
		    'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		    'Accept-Language: en-US,fr;q=0.8;q=0.6,en;q=0.4,ar;q=0.2',
		    'Accept-Encoding: gzip,deflate',
		    'Accept-Charset: utf-8;q=0.7,*;q=0.7',
		    'cookie:datr=; locale=en_US; sb=; pl=n; lu=gA; c_user=; xs=; act=; presence='
		); 

        $ch = curl_init( $url );

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "GET");
        curl_setopt($ch, CURLOPT_POST, false);     
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_REFERER, base_url());

        $result = curl_exec( $ch );
       
        curl_close( $ch );

        return $result;
	}
}

if(!function_exists("get_js")){
	function get_js($js_files = array()){
		$core = APPPATH."../assets/js/core.js";

		if(!file_exists($core)){
			$minifier = new MatthiasMullie\Minify\JS();
			foreach ($js_files as $file) {
				$minifier->add(APPPATH."../".$file);
			}

			$minifier->minify($core);
			$minifier->add($core);
		}else{

			$mod_date=date("F d Y H:i:s.", filemtime($core));
			$date = strtotime(date("Y-m-d", strtotime(NOW)));
			$mod_date = strtotime(date("Y-m-d", strtotime($mod_date)));

			if($mod_date < $date){
				$minifier = new MatthiasMullie\Minify\JS();
				foreach ($js_files as $file) {
					$minifier->add(APPPATH."../".$file);
				}

				$minifier->minify($core);
				$minifier->add($core);
			}

		}
		echo BASE."assets/js/core.js";
	}
}

if(!function_exists("get_css")){
	function get_css($css_files = array()){
		$core = APPPATH."../assets/css/core.css";

		if(!file_exists($core)){
			$minifier = new MatthiasMullie\Minify\CSS();
			foreach ($css_files as $file) {
				$minifier->add(APPPATH."../".$file);
			}
			$minifier->minify($core);
			$minifier->add($core);
		}else{

			$mod_date=date("F d Y H:i:s.", filemtime($core));
			$date = strtotime(date("Y-m-d", strtotime(NOW)));
			$mod_date = strtotime(date("Y-m-d", strtotime($mod_date)));

			if($mod_date < $date){
				$minifier = new MatthiasMullie\Minify\CSS();
				foreach ($css_files as $file) {
					$minifier->add(APPPATH."../".$file);
				}

				$minifier->minify($core);
				$minifier->add($core);
			}

		}
		echo BASE."assets/css/core.css";
	}
}



class Spintax
{
    public function process( $text )
    {
    	$text = specialchar_decode($text);
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array( $this, 'replace' ),
            $text
        );
    }

    public function replace( $text )
    {
        $text = $this -> process( $text[1] );
        $parts = explode( '|', $text );
        return $parts[ array_rand( $parts ) ];
    }
}



/*=================================================
=            edit and add new function            =
=================================================*/


if (!function_exists("echo_json_string")) {
	function echo_json_string($array){
		echo json_encode($array, JSON_PRETTY_PRINT);
		exit(0);
	}
}

if(!function_exists("create_random_api_key")){
	function create_random_string_key($length = "") {
		if ($length == "") {
			$length = 32;
		}
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
}

if(!function_exists("get_current_user_data")){
	function get_current_user_data($id = ""){
		if ($id == "") {
			$id = session("uid");
		}
		$CI = &get_instance();
		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}
		$user = $CI->help_model->get("*", USERS, "id = '{$id}'");
		if(!empty($user)){
			return $user;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_role')){
	function get_role($role_type = "", $id = ""){
		if (isset($_SESSION['user_current_info']['role']) && $_SESSION['user_current_info']['role'] != '') {
			$role = $_SESSION['user_current_info']['role'];
		}else{
			$user = get_current_user_data($id);
			if (!empty($user)) {
				$data_session = array(
					'role'       => $user->role,
					'email'      => $user->email,
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'timezone'   => $user->timezone,
				);
				set_session('user_current_info', $data_session);
				$role = $user->role;
			}else{
				return false;
			}
		}
		
		if($role != '' && $role == $role_type){
			return true;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_controller_role')){
	function get_controller_role(){
		if(!get_role()){
			redirect(cn());
		}
	}
}

if(!function_exists("table_column")){
	function table_column($data, $table_column_name){
		if (is_object($data) && property_exists($data, $table_column_name)) {
			$value = $data->$table_column_name;
			switch ($table_column_name) {

				case 'api_order_id':
					$value = ($value == 0 || $value ==-1)? "" : $value;
					break;

				case 'api_service_id':
					$value = (!empty($value) && $value > 0)? lang("API") : lang("Manual");
					break;

				case 'link':
					$value = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
					break;

				case 'created':
					$value = convert_timezone($value, 'user');
					break;

				case 'charge':
					$value = currency_format($value, 4);
					break;
					
				case 'service_id':
					$value = get_field(SERVICES, ['id' => $data->service_id], "name");
					break;	
									
				case 'uid':
					$value = get_field(USERS, ['id' => $data->uid], "email");
					break;

				default:
					# code...
					break;
			}
			return $value;
		}
	}
}

if (!function_exists('update_options_status')) {
    function update_options_status(){
    	$user = session('user_current_info');
    	$cookie_lc_verified = "";
		if (isset($_COOKIE["lc_verified"]) && $_COOKIE["lc_verified"] != "") {
	      $cookie_lc_verified = base64_decode($_COOKIE["lc_verified"]);
	    }
	    if ($user['role'] == 'admin') {
			if ($cookie_lc_verified != "verified") {
				update_option('get_features_option', 0);
			}else{
				update_option('get_features_option', 1);
			}
	    }else{
	    	return false;
	    }
    }
}

if(!function_exists("get_field")){
	function get_field($table, $where = array(), $field){
		$CI = &get_instance();

		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}
		$item = $CI->help_model->get("*", $table, $where);

		if(!empty($item) && isset($item->$field)){
			return $item->$field;
		}else{
			return false;
		}
	}
}

if (!function_exists("order_status_array")) {
	function order_status_array(){
		$data = array('pending','processing','inprogress','completed','partial','canceled','refunded', 'awaiting', 'error');
		return $data;
	}
}

if (!function_exists("childpanel_status_array")) {
	function childpanel_status_array(){
		$data = array('active','processing','refunded','disabled','terminated');
		return $data;
	}
}

if (!function_exists("order_subscriptions_status_array")) {
	function order_subscriptions_status_array(){
		$data = array('Active','Paused','Completed','Expired','Canceled');
		return $data;
	}
}

if (!function_exists("order_dripfeed_status_array")) {
	function order_dripfeed_status_array(){
		$data = array('inprogress','completed','canceled');
		return $data;
	}
}

if (!function_exists("ticket_status_array")) {
	function ticket_status_array(){
		$data = array('new','pending','closed');
		return $data;
	}
}

if(!function_exists("ticket_status_title")){
	function ticket_status_title($key){
		switch ($key) {
			case 'new':
				return lang('New');
				break;			
			case 'pending':
				return lang('Pending');
				break;	

			case 'closed':
				return lang('Closed');
				break;

			case 'answered':
				return lang('Answered');
				break;			
		
		}
	}
}

if(!function_exists("childpanel_status_title")){
	function childpanel_status_title($key){
		switch ($key) {
			case 'active':
				return lang('Active');
				break;			
			case 'processing':
				return lang('Processing');
				break;	

			case 'refunded':
				return lang('Refunded');
				break;

			case 'disabled':
				return lang('Disabled');
				break;			
		    case 'terminated':
				return lang('Terminated');
				break;	
		}
	}
}

if(!function_exists("order_status_title")){
	function order_status_title($key){
		switch ($key) {
			case 'completed':
				return lang("Completed");
				break;			
			case 'processing':
				return lang("Processing");
				break;			
			case 'inprogress':
				return lang("In_progress");
				break;			
			case 'pending':
				return lang('Pending');
				break;			
			case 'partial':
				return lang("Partial");
				break;			
			case 'canceled':
				return lang("Canceled");
				break;	

			case 'refunded':
				return lang("Refunded");
				break;	

			case 'active':
				return lang("Active");
				break;	

			case 'awaiting':
				return lang("Awaiting");
				break;	

			case 'rejected':
				return lang("Rejected");
				break;

			/*----------  subscriptions  ----------*/

			case 'Active':
				return lang("Active");
				break;

			case 'Completed':
				return lang("Completed");
				break;

			case 'Paused':
				return lang("Paused");
				break;

			case 'Expired':
				return lang("Expired");
				break;

			case 'Canceled':
				return lang("Canceled");
				break;

			case 'fail':
				return lang("Fail");
				break;	

			case 'error':
				return lang("Error");
				break;						
		}
	}
}

/**
 *
 * Export data to Excel, CSV
 *
 */
if (!function_exists('export_excel')) {
	function export_excel($data){
		$timestamp = time();
        $filename = 'Export_excel_' . $timestamp . '.xls';
        
        header("Content-Description: File Transfer"); 
     	header("Content-Disposition: attachment; filename=$filename"); 
     	header("Content-Type: application/csv;");

     	// file creation 
     	$file = fopen('php://output', 'w');
	 
     	$header = array("Student Name","Student Phone"); 
     	fputcsv($file, $header);
     	foreach ($data as $key => $row) {
     		$row = (array)$row;
	       fputcsv($file, $row->id); 
     	}
     	fclose($file); 
     	exit; 
	}
}

if (!function_exists('export_csv')) {
	function export_csv($filename, $table_name){
		$CI = &get_instance();
        $CI->load->dbutil();
        $CI->load->helper('file');
        $CI->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $query = $CI->db->query("SELECT * FROM ".$table_name);
        $data = $CI->dbutil->csv_from_result($query, $delimiter, $newline);
        force_download($filename, "\xEF\xBB\xBF".$data);
	}
}


/**
 *
 * Get option and update option 
 *
 */

if(!function_exists("get_option")){
	function get_option($key, $value = ""){
		$CI = &get_instance();
		
		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}
		$option = $CI->help_model->get("value", OPTIONS, "name = '{$key}'");
		if(empty($option)){
			$CI->db->insert(OPTIONS, array("name" => $key, "value" => $value));
			return $value;
		}else{
			return $option->value;
		}
	}
}

if(!function_exists("update_option")){
	function update_option($key, $value){
		$CI = &get_instance();
		
		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}
		
		$option = $CI->help_model->get("value", OPTIONS, "name = '{$key}'");
		if(empty($option)){
			$CI->db->insert(OPTIONS, array("name" => $key, "value" => $value));
		}else{
			$CI->db->update(OPTIONS, array("value" => $value), array("name" => $key));
		}
	}
}

/**
 * Get fake order ID when the feature is enabled
 * Converts real order ID to a fake one using multiplier and offset
 * @param int $real_order_id The actual order ID from database
 * @return int The fake order ID if feature is enabled, or the real ID if disabled
 */
if(!function_exists("get_display_order_id")){
	function get_display_order_id($real_order_id){
		// Validate input - ensure it's a positive integer
		$real_order_id = (int)$real_order_id;
		if ($real_order_id <= 0) {
			return $real_order_id;
		}
		
		// Check if fake order ID feature is enabled
		$is_enabled = get_option('enable_fake_order_id', 0);
		
		if ($is_enabled == 1) {
			$multiplier = (int)get_option('fake_order_id_multiplier', 7);
			$offset = (int)get_option('fake_order_id_offset', 1000);
			
			// Ensure valid values
			$multiplier = ($multiplier >= 2 && $multiplier <= 100) ? $multiplier : 7;
			$offset = ($offset >= 100 && $offset <= 100000) ? $offset : 1000;
			
			// Check for potential overflow before performing calculation
			$max_safe_order = (PHP_INT_MAX - $offset) / $multiplier;
			if ($real_order_id > $max_safe_order) {
				return $real_order_id; // Return original if overflow would occur
			}
			
			// Generate fake order ID: (real_id * multiplier) + offset
			return ($real_order_id * $multiplier) + $offset;
		}
		
		return $real_order_id;
	}
}

/**
 * Convert fake order ID back to real order ID (for admin use)
 * @param int $fake_order_id The displayed fake order ID
 * @return int The real order ID, or the input if invalid or feature disabled
 */
if(!function_exists("get_real_order_id")){
	function get_real_order_id($fake_order_id){
		// Validate input - ensure it's a positive integer
		if (!is_numeric($fake_order_id) || $fake_order_id <= 0) {
			return (int)$fake_order_id;
		}
		$fake_order_id = (int)$fake_order_id;
		
		$is_enabled = get_option('enable_fake_order_id', 0);
		
		if ($is_enabled == 1) {
			$multiplier = (int)get_option('fake_order_id_multiplier', 7);
			$offset = (int)get_option('fake_order_id_offset', 1000);
			
			// Ensure valid values
			$multiplier = ($multiplier >= 2 && $multiplier <= 100) ? $multiplier : 7;
			$offset = ($offset >= 100 && $offset <= 100000) ? $offset : 1000;
			
			// Validate that the fake_order_id could have been generated by our formula
			if ($fake_order_id <= $offset) {
				return $fake_order_id; // Cannot be a valid fake ID
			}
			
			// Check divisibility using modulo before division
			$adjusted = $fake_order_id - $offset;
			if ($adjusted % $multiplier !== 0) {
				return $fake_order_id; // Not a valid fake ID, return as-is
			}
			
			// Calculate real ID
			$real_id = $adjusted / $multiplier;
			if ($real_id <= 0) {
				return $fake_order_id; // Not a valid result
			}
			
			return (int)$real_id;
		}
		
		return $fake_order_id;
	}
}

/**
 * Get code part content from the code_parts table
 * @param string $page_key The unique page identifier
 * @param string $default Default value if not found
 * @param bool $process_variables Whether to process template variables
 * @return string The HTML content for the code part
 */
if(!function_exists("get_code_part")){
	function get_code_part($page_key, $default = '', $process_variables = true){
		try {
			$CI = &get_instance();
			
			// Check if database is loaded and table exists
			if (!isset($CI->db) || !$CI->db) {
				return $default;
			}
			
			// Check if table exists - with error suppression
			if (!$CI->db->table_exists('code_parts')) {
				return $default;
			}
			
			$result = $CI->db->select('content')
				->where('page_key', $page_key)
				->where('status', 1)
				->get('code_parts')
				->row();
			
			$content = ($result && !empty($result->content)) ? $result->content : $default;
			
			// Process template variables if enabled and user is logged in
			if ($process_variables && !empty($content) && session('uid')) {
				$content = process_code_part_variables($content);
			}
			
			return $content;
		} catch (Exception $e) {
			// Return default on any error to prevent page breakage
			return $default;
		}
	}
}

/**
 * Process template variables in code part content
 * Supports variables like {{user.balance}}, {{user.orders}}, {{site.name}}, etc.
 * @param string $content The HTML content with template variables
 * @return string Content with variables replaced
 */
if(!function_exists("process_code_part_variables")){
	function process_code_part_variables($content){
		try {
			$CI = &get_instance();
			$uid = session('uid');
			
			// User-related variables (only if logged in)
			$user_vars = [];
			if ($uid && isset($CI->db) && $CI->db) {
				// Get user data - using USERS constant or 'general_users' as fallback
				$users_table = defined('USERS') ? USERS : 'general_users';
				$user = $CI->db->where('id', $uid)->get($users_table)->row();
				if ($user) {
					$user_vars = [
						'{{user.id}}' => $user->id,
						'{{user.email}}' => isset($user->email) ? $user->email : '',
						'{{user.balance}}' => number_format((float)(isset($user->balance) ? $user->balance : 0), 2),
						'{{user.first_name}}' => isset($user->first_name) ? $user->first_name : '',
						'{{user.last_name}}' => isset($user->last_name) ? $user->last_name : '',
						'{{user.name}}' => (isset($user->first_name) ? $user->first_name : '') . ' ' . (isset($user->last_name) ? $user->last_name : ''),
						'{{user.api_key}}' => isset($user->api_key) ? $user->api_key : '',
						'{{user.created}}' => isset($user->created) ? date('Y-m-d', strtotime($user->created)) : '',
					];
					
					// Get user orders count - using ORDER constant or 'orders' as fallback
					$orders_table = defined('ORDER') ? ORDER : 'orders';
					$orders_count = $CI->db->where('uid', $uid)->count_all_results($orders_table);
					$user_vars['{{user.orders}}'] = $orders_count;
					$user_vars['{{user.total_orders}}'] = $orders_count;
					
					// Get user total spent - using 'charge' column from orders table
					$spent_result = $CI->db->select_sum('charge', 'total')
						->where('uid', $uid)
						->where('status !=', 'canceled')
						->get($orders_table)
						->row();
					$user_vars['{{user.spent}}'] = number_format((float)(isset($spent_result->total) ? $spent_result->total : 0), 2);
					$user_vars['{{user.total_spent}}'] = $user_vars['{{user.spent}}'];
					
					// Get pending orders count
					$pending_orders = $CI->db->where('uid', $uid)
						->where_in('status', ['pending', 'processing', 'inprogress'])
						->count_all_results($orders_table);
					$user_vars['{{user.pending_orders}}'] = $pending_orders;
					
					// Get completed orders count
					$completed_orders = $CI->db->where('uid', $uid)
						->where('status', 'completed')
						->count_all_results($orders_table);
					$user_vars['{{user.completed_orders}}'] = $completed_orders;
					
					// Get tickets count - using TICKETS constant or 'tickets' as fallback
					$tickets_table = defined('TICKETS') ? TICKETS : 'tickets';
					if ($CI->db->table_exists($tickets_table)) {
						$tickets_count = $CI->db->where('uid', $uid)->count_all_results($tickets_table);
						$user_vars['{{user.tickets}}'] = $tickets_count;
					} else {
						$user_vars['{{user.tickets}}'] = 0;
					}
				}
			}
			
			// Site-related variables
			$site_vars = [
				'{{site.name}}' => get_option('website_name', ''),
				'{{site.url}}' => cn(),
				'{{site.currency}}' => get_option('currency_symbol', '$'),
				'{{site.currency_code}}' => get_option('currency_code', 'USD'),
			];
			
			// Date/time variables
			$date_vars = [
				'{{date.today}}' => date('Y-m-d'),
				'{{date.now}}' => date('Y-m-d H:i:s'),
				'{{date.year}}' => date('Y'),
				'{{date.month}}' => date('m'),
				'{{date.day}}' => date('d'),
			];
			
			// Merge all variables
			$all_vars = array_merge($user_vars, $site_vars, $date_vars);
			
			// Replace variables in content
			$content = str_replace(array_keys($all_vars), array_values($all_vars), $content);
			
			return $content;
		} catch (Exception $e) {
			// Return original content on error
			return $content;
		}
	}
}

/**
 * Get code part content for editing (without variable processing)
 * @param string $page_key The unique page identifier
 * @param string $default Default value if not found
 * @return string The raw HTML content for editing
 */
if(!function_exists("get_code_part_raw")){
	function get_code_part_raw($page_key, $default = ''){
		return get_code_part($page_key, $default, false);
	}
}

if(!function_exists("get_upload_folder")){
	function get_upload_folder(){
		$path = APPPATH."../assets/uploads/user" . sha1(session("uid"))."/";
		if (!file_exists($path)) {
			$uold     = umask(0);
	    	mkdir($path, 0777);
			umask($uold);

	    	file_put_contents($path."index.html", "<h1>404 Not Found</h1>");
	    }
	}
}


/**
 * Return an array of timezones
 * 
 * @return array
 */
function tz_list()
{
    $timezoneIdentifiers = DateTimeZone::listIdentifiers();
    $utcTime = new DateTime('now', new DateTimeZone('UTC'));
 
    $tempTimezones = array();
    foreach ($timezoneIdentifiers as $timezoneIdentifier) {
        $currentTimezone = new DateTimeZone($timezoneIdentifier);
 
        $tempTimezones[] = array(
            'offset' => (int)$currentTimezone->getOffset($utcTime),
            'identifier' => $timezoneIdentifier
        );
    }
 
    // Sort the array by offset, identifier ascending
    usort($tempTimezones, function($a, $b) {
		return ($a['offset'] == $b['offset'])
			? strcmp($a['identifier'], $b['identifier'])
			: $a['offset'] - $b['offset'];
    });
 
	$timezoneList = array();
    foreach ($tempTimezones as $key => $tz) {
		$sign                       = ($tz['offset'] > 0) ? '+' : '-';
		$offset                     = gmdate('H:i', abs($tz['offset']));
        $timezoneList[$key]['time'] = '(UTC ' . $sign . $offset . ') ' . $tz['identifier'];
		$timezoneList[$key]['zone'] = 	$tz['identifier'];
    }
    return $timezoneList;
}


// Convert time zone for user.
if(!function_exists('convert_timezone')){
	function convert_timezone($datetime, $case, $uid = ''){
		$zonesystem  = date_default_timezone_get();

		if ($uid != '') {
			$zoneuser    = get_user_timezone($uid);
		}else{
			if (isset($_SESSION['user_current_info']['timezone']) && $_SESSION['user_current_info']['timezone'] != '') {
				$zoneuser = $_SESSION['user_current_info']['timezone'];
			}else{
				$zoneuser    = get_user_timezone(session('uid'));
			}
		}

		switch ($case) {
			case 'user':
				$currentTZ   = new DateTimeZone($zonesystem);
				$newTZ       = new DateTimeZone($zoneuser);
				break;

			case 'system':
				$currentTZ   = new DateTimeZone($zoneuser);
				$newTZ       = new DateTimeZone($zonesystem);
				break;
		}
		
		$date        = new DateTime( $datetime, $currentTZ );
		$date->setTimezone( $newTZ );
		return $date->format('Y-m-d H:i:s');
	}
}

//Get User's timezone, return zone
if(!function_exists("get_user_timezone")){
	function get_user_timezone($uid = null){
		if(!empty($uid)){
			$userZone = get_field(USERS, ['id' => $uid], 'timezone');
			if(!empty($userZone)){
				return $userZone;
			}
		}
		return false;
	}
}

if (!function_exists("get_array_diff_object")) {
	function get_array_diff_object($array_a, $array_b){
		$diff = array_udiff($array_a, $array_b,
		    function($a, $b) {
		    	if ($a===$b) {
			        return 0;
			    }
			    return 1;
		    }
		);
		return $diff;
	}
}


/**
 * Getting the names of all files in a directory
 * @return a Array
 *
 */
if (!function_exists('get_name_of_files_in_dir')) {
	function get_name_of_files_in_dir($path, $file_types = array('')){
		if (empty($file_types)) {
			$file_types = ['.php'];
		}
		$name_of_files = [];
		if ($path != "" && is_dir($path)) {
			$dir = new DirectoryIterator($path);
		 	foreach ($dir as $fileinfo) {
			    if (!$fileinfo->isDot()) {
			    	foreach ($file_types as $key => $row) {
	        			if (strrpos($fileinfo->getFilename(), $row)) {
	        				$name_of_files[] = basename($fileinfo->getFilename(), $row);
	        			}
			    	}
			    }
			}
		}
		return $name_of_files;
	}
}

/**
 *
 * Check ticket is_read or not
 *
 */

if (!function_exists('check_unread_ticket')) {
	function check_unread_ticket($ticket_id){
		$CI = &get_instance();
		if(empty($CI->help_model)){
			$CI->load->model('model', 'help_model');
		}
		$ticket_content = $CI->help_model->get('*', TICKET_MESSAGES, ["ticket_id" => $ticket_id], 'id', 'DESC');
		if (get_role('user')) {
			if (!empty($ticket_content)  && $ticket_content->uid != session('uid') && $ticket_content->is_read == 1 ) {
				return true;
			}	
		}else{
			if (!empty($ticket_content) && $ticket_content->is_read == 1 ) {
				return true;
			}
		}
		return false;	
	}
}

if (!function_exists('get_payments_method')) {
	function get_payments_method(){
		$path = APPPATH."./modules/add_funds/controllers/";
		$payment_methods = array();
		if ($path != "") {
			$dir = new DirectoryIterator($path);
		 	foreach ($dir as $fileinfo) {
			    if (!$fileinfo->isDot()) {
			        if ($fileinfo->getFilename() != 'add_funds.php') {
			        	if (!in_array(basename($fileinfo->getFilename(), ".php"), ['paypal', 'stripe', 'two_checkout'])) {
			        		$payment_methods[] = basename($fileinfo->getFilename(), ".php");
			        	}
			        }
			    }
			}
			return $payment_methods;
		}
	}
	
}

if (!function_exists('payment_method_exists')) {
	function payment_method_exists($payment_gateway){
		$path_file1 = APPPATH."./modules/setting/views/integrations/".$payment_gateway.".php";
        $path_file2 = APPPATH."./modules/add_funds/controllers/".$payment_gateway.".php";
        if (file_exists($path_file1) && file_exists($path_file2)) {
        	return true;
        }
        return false;
	}
}

if(!function_exists('truncate_string')){
	function truncate_string($string = "", $max_length = 50, $ellipsis = "...", $trim = true) {
	    $max_length = (int)$max_length;
	    if ($max_length < 1) {
	        $max_length = 50;
	    }

	    if (!is_string($string)) {
	        $string = "";
	    }

	    if ($trim) {
	        $string = trim($string);
	    }

	    if (!is_string($ellipsis)) {
	        $ellipsis = "...";
	    }

	    $string_length = mb_strlen($string);
	    $ellipsis_length = mb_strlen($ellipsis);
	    if($string_length > $max_length){
	        if ($ellipsis_length >= $max_length) {
	            $string = mb_substr($ellipsis, 0, $max_length);
	        } else {
	            $string = mb_substr($string, 0, $max_length - $ellipsis_length)
	                    . $ellipsis;
	        }
	    }

	    return $string;
	}
}

if (!function_exists('connect_api')) {
	function connect_api($url, $post = array("")) {
	    $_post = Array();
      	if (is_array($post)) {
	          foreach ($post as $name => $value) {
	              $_post[] = $name.'='.urlencode($value);
	          }
      	}

      	$ch = curl_init($url);
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_POST, 1);
      	curl_setopt($ch, CURLOPT_HEADER, 0);
      	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      	if (is_array($post)) {
          	curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
      	}
      	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
      	$result = curl_exec($ch);
      	if (curl_errno($ch) != 0 && empty($result)) {
          	$result = false;
      	}
      	curl_close($ch);
	    $response = (isset($_COOKIE[base64_decode('bGNfdmVyaWZpZWQ=')])) ? $_COOKIE[base64_decode('bGNfdmVyaWZpZWQ=')] : '';
		if ($response == '') {
			return false;
		}elseif (base64_decode($response) != base64_decode('dmVyaWZpZWQ=')) {
			return false;
		}else{
	 		return $result;
		}
	}
}

if (!function_exists("get_random_time")) {
	function get_random_time($type = ""){
		$rand_time = rand(600, 1200);
		if ($type == "api") {
			$rand_time = rand(14400, 28000);
		}
		return $rand_time;
	}
}

if(!function_exists('get_theme')){
	function get_theme(){
		$theme_config = APPPATH."../themes/config.json";
		$theme = "basic";
		if(file_exists($theme_config)){	
			$config = file_get_contents($theme_config);
			$config = json_decode($config);
			if(is_object($config) && isset($config->theme)){
				$theme = $config->theme;
			}
		}
		return $theme;
	}
}

/*----------  Show custom metion  ----------*/
if (!function_exists('get_list_custom_mention')) {
	function get_list_custom_mention($order){
		switch ($order->service_type) {
			case 'custom_comments':
				$result = (object)array(
					'exists_list'     => true,
					'title'		      => lang('comments'),
					'list'	          => json_decode($order->comments)
				);
				break;
			
			case 'comment_likes':
				$result = (object)array(
					'exists_list'     => true,
					'title'		      => lang('username'),
					'list'	          => $order->username
				);
				break;

			case 'mentions_hashtag':
				$result = (object)array(
					'exists_list'     => true,
					'title'		      => lang('hashtag'),
					'list'	          => $order->hashtag
				);
				break;	

			case 'mentions_user_followers':
				$result = (object)array(
					'exists_list'     => true,
					'title'		      => lang('username'),
					'list'	          => $order->hashtag
				);
				break;
			
			default:
				$result = (object)array(
					'exists_list' => false,
				);
				break;
		}
		return $result;


	}
}

if(!function_exists('strip_tag_css')){
	function strip_tag_css($text){
	    $text      = strip_tags($text,"<style>");
	    $substring = substr($text,strpos($text,"<style"),strpos($text,"</style>"));
	    $text      = str_replace($substring,"",$text);
	    $text      = str_replace(array("\t","\r","\n"),"",$text);
	    $text      = trim($text);
	    return $text;
	}
}

if(!function_exists('get_current_url')){
	function get_current_url(){
	    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	    if ($url == "") {
	    	$url = segment(1);
	    }
	    return $url;
	}
}

// get search box
if (!function_exists('allowed_search_bar')) {
	function allowed_search_bar($module = ""){
		if (get_role('user')) {
			$allowed_search = ['subscriptions', 'dripfeed', 'log', 'tickets', 'services'];
		}else{
			$allowed_search = ['user_block_ip', 'user_logs', 'user_mail_logs', 'services', 'subscriptions', 'dripfeed', 'users','tickets', 'faqs', 'log', 'search', 'transactions', 'subscribers'];
		}
		if (in_array($module, $allowed_search)) {
			return true;
		}
		return false;
	}
}

// check is_ajax_request or not
if (!function_exists('_is_ajax')) {
	function _is_ajax($module_request = ""){
		$CI = &get_instance();
		if (!$CI->input->is_ajax_request()) {
			if ($module_request != "") {
		   		redirect(cn($module_request));
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
}

/**
 * Get dynamic header menu items based on user role
 * @return array Array of menu items visible to current user
 */
if (!function_exists('get_header_menu_items')) {
	function get_header_menu_items() {
		$menu_data = get_option('header_menu_items', '[]');
		$items = json_decode($menu_data, true);
		
		if (!is_array($items) || empty($items)) {
			return [];
		}

		// Sort by sort_order
		usort($items, function($a, $b) {
			return ($a['sort_order'] ?? 0) - ($b['sort_order'] ?? 0);
		});

		// Get current user role
		$current_role = get_current_role_type();

		$visible_items = [];
		foreach ($items as $item) {
			// Skip disabled items
			if (empty($item['status']) || $item['status'] != 1) {
				continue;
			}

			// Check role visibility
			$roles = isset($item['roles']) ? $item['roles'] : ['everyone'];
			
			if (in_array('everyone', $roles) || in_array($current_role, $roles)) {
				$visible_items[] = $item;
			}
		}

		return $visible_items;
	}
}

/**
 * Get current user role type for menu visibility checks
 * @return string Role type (guest, user, supporter, admin)
 */
if (!function_exists('get_current_role_type')) {
	function get_current_role_type() {
		if (!session('uid')) {
			return 'guest';
		}
		
		if (get_role('admin')) {
			return 'admin';
		}
		
		if (get_role('supporter')) {
			return 'supporter';
		}
		
		return 'user';
	}
}

/**
 * Check if a menu URL is active (matches current page)
 * @param string $url The menu URL to check
 * @return bool True if active
 */
if (!function_exists('is_menu_url_active')) {
	function is_menu_url_active($url) {
		if (empty($url) || strpos($url, '#') === 0) {
			return false;
		}
		
		// Parse URL path
		$url_parts = explode('/', trim($url, '/'));
		
		if (empty($url_parts[0])) {
			return false;
		}
		
		// Check first segment match
		if (segment(1) != $url_parts[0]) {
			return false;
		}
		
		// Check second segment if exists
		if (isset($url_parts[1]) && !empty($url_parts[1])) {
			return (segment(2) == $url_parts[1]);
		}
		
		return true;
	}
}

/**
 * Render a menu item URL - handles relative and absolute URLs
 * @param string $url The URL from menu item
 * @return string The full URL
 */
if (!function_exists('render_menu_url')) {
	function render_menu_url($url) {
		if (empty($url)) {
			return '#';
		}
		
		// Check if it's already an absolute URL
		if (preg_match('/^(https?:\/\/|\/\/)/i', $url)) {
			return $url;
		}
		
		// Check if it starts with # (anchor link)
		if (strpos($url, '#') === 0) {
			return $url;
		}
		
		// It's a relative URL, prepend base URL
		return cn($url);
	}
}