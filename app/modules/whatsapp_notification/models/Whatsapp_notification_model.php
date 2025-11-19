<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_notification_model extends MY_Model {

    public function __construct(){
        parent::__construct();
    }

    /**
     * Get WhatsApp config
     */
    public function get_whatsapp_config() {
        return $this->db->get('whatsapp_config')->row();
    }

    /**
     * Get all notification templates
     */
    public function get_all_notifications() {
        return $this->db->order_by('id', 'ASC')->get('whatsapp_notifications')->result();
    }
}
