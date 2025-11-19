<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card">
    <div class="card-header" style="border: 0.1px solid #25D366; border-radius: 3.5px 3.5px 0px 0px; background: #25D366 !important;">
        <h3 class="card-title text-white">
            <i class="fa fa-whatsapp"></i> WhatsApp Settings
        </h3>
    </div>
    <div class="list-group list-group-flush">
        <a href="<?php echo cn('whatsapp_notification/api_settings'); ?>" 
           class="list-group-item list-group-item-action <?php echo (segment(2) == 'api_settings' || segment(2) == '' || segment(2) == 'index') ? 'active' : ''; ?>">
            <i class="fa fa-cog mr-2"></i> API Configuration
        </a>
        <a href="<?php echo cn('whatsapp_notification/notification_templates'); ?>" 
           class="list-group-item list-group-item-action <?php echo (segment(2) == 'notification_templates') ? 'active' : ''; ?>">
            <i class="fa fa-bell mr-2"></i> Notification Templates
        </a>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-muted mb-3">
            <i class="fa fa-info-circle"></i> Quick Guide
        </h6>
        <ul class="small text-muted mb-0">
            <li>Configure your WhatsApp API credentials in API Configuration</li>
            <li>Manage notification templates in Notification Templates</li>
            <li>Enable/disable notifications as needed</li>
        </ul>
    </div>
</div>
