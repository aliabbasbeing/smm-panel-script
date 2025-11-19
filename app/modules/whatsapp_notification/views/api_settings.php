<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card">
    <div class="card-header" style="border: 0.1px solid #25D366; border-radius: 3.5px 3.5px 0px 0px; background: #25D366 !important;">
        <h3 class="card-title text-white">
            <i class="fa fa-cog"></i> WhatsApp API Configuration
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="fa fa-info-circle"></i> 
            <strong>Important:</strong> Configure your WhatsApp API credentials here. These settings are required for sending WhatsApp notifications.
        </div>

        <form class="actionForm" action="<?= cn('whatsapp_notification/ajax_save_api_settings') ?>" method="POST" data-redirect="<?= get_current_url(); ?>">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa fa-link"></i> API URL <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               name="url" 
                               value="<?= isset($whatsapp_config->url) ? html_escape($whatsapp_config->url) : '' ?>" 
                               placeholder="https://api.example.com/send" 
                               required>
                        <small class="form-text text-muted">
                            The endpoint URL for your WhatsApp API service
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa fa-key"></i> API Key <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               name="api_key" 
                               value="<?= isset($whatsapp_config->api_key) ? html_escape($whatsapp_config->api_key) : '' ?>" 
                               placeholder="Your API key" 
                               required>
                        <small class="form-text text-muted">
                            Your WhatsApp API authentication key
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa fa-phone"></i> Admin Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               name="admin_phone" 
                               value="<?= isset($whatsapp_config->admin_phone) ? html_escape($whatsapp_config->admin_phone) : '' ?>" 
                               placeholder="+1234567890" 
                               required>
                        <small class="form-text text-muted">
                            Admin phone number to receive notifications (with country code)
                        </small>
                    </div>
                </div>
            </div>

            <div class="card bg-light mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fa fa-lightbulb-o"></i> Configuration Tips</h6>
                    <ul class="mb-0">
                        <li>Ensure your API URL is accessible from your server</li>
                        <li>Keep your API key secure and don't share it</li>
                        <li>Phone number should include country code (e.g., +92300XXXXXXX)</li>
                        <li>Test the configuration after saving by sending a test notification</li>
                    </ul>
                </div>
            </div>

            <div class="form-footer mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa fa-save"></i> Save API Settings
                </button>
                <a href="<?= cn('whatsapp_notification/notification_templates') ?>" class="btn btn-outline-secondary btn-lg ml-2">
                    <i class="fa fa-bell"></i> Configure Notifications
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.card-header {
    font-weight: 600;
}
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.form-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
}
.bg-light {
    background-color: #f8f9fa !important;
}
</style>
