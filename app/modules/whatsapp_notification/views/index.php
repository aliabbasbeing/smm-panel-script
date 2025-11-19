<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-3">
        <?php $this->load->view('whatsapp_notification/sidebar'); ?>
    </div>
    <div class="col-md-9">
        <?php 
        $content_view = isset($content_view) ? $content_view : 'api_settings';
        $this->load->view('whatsapp_notification/' . $content_view); 
        ?>
    </div>
</div>
