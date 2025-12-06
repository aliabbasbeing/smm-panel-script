<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card2">
    <div class="card-header" style="border: 0.1px solid #25D366; border-radius: 3.5px 3.5px 0px 0px; background: #25D366 !important;">
        <h3 class="card-title text-white">
            <i class="fab fa-whatsapp"></i> WhatsApp Notification Settings
        </h3>
    </div>
    <div class="card-body">
        
        <!-- API Configuration Section -->
        <div class="mb-5">
            <h4 class="text-success mb-3">
                <i class="fas fa-cog"></i> WhatsApp API Configuration
            </h4>
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> 
                <strong>Important:</strong> Configure your WhatsApp API credentials. These settings are required for sending WhatsApp notifications.
            </div>

            <form class="actionForm" action="<?= cn("$module/ajax_whatsapp_api_settings") ?>" method="POST" data-redirect="<?= get_current_url(); ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-link"></i> API URL <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   name="url" 
                                   value="<?= isset($whatsapp_api->url) ? html_escape($whatsapp_api->url) : '' ?>" 
                                   placeholder="https://api.example.com/send" 
                                   required>
                            <small class="form-text text-muted">
                                The endpoint URL for your WhatsApp API service
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-key"></i> API Key <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   name="api_key" 
                                   value="<?= isset($whatsapp_api->api_key) ? html_escape($whatsapp_api->api_key) : '' ?>" 
                                   placeholder="Your API key" 
                                   required>
                            <small class="form-text text-muted">
                                Your WhatsApp API authentication key
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-phone"></i> Admin Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   name="admin_phone" 
                                   value="<?= isset($whatsapp_api->admin_phone) ? html_escape($whatsapp_api->admin_phone) : '' ?>" 
                                   placeholder="+1234567890" 
                                   required>
                            <small class="form-text text-muted">
                                Admin phone number to receive notifications (with country code)
                            </small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save API Configuration
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <hr class="my-5 dark-mode-hr">

        <!-- Notification Templates Section -->
        <div>
            <h4 class="text-success mb-3">
                <i class="fas fa-bell"></i> Notification Templates
            </h4>
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> 
                <strong>Manage Notifications:</strong> Enable or disable notifications and customize message templates for different events.
            </div>

            <?php
            // Load the WhatsApp notification library
            $this->load->library('whatsapp_notification');
            $notifications = $this->whatsapp_notification->get_all_notifications();
            
            if (empty($notifications)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Notice:</strong> WhatsApp notification templates are not set up yet. Please run the database migration file: 
                    <code>/database/whatsapp-notifications.sql</code>
                </div>
            <?php else: ?>
                
                <form class="actionForm" method="POST" action="<?php echo cn('setting/ajax_whatsapp_notifications'); ?>" data-redirect="<?= get_current_url(); ?>">
                    
                    <?php foreach ($notifications as $notification): 
                        $variables = json_decode($notification->variables, true);
                        if (!is_array($variables)) {
                            $variables = array();
                        }
                    ?>
                        
                        <div class="notification-card mb-4">
                            <div class="notification-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 notification-title">
                                        <i class="fas fa-bell text-primary"></i> 
                                        <?php echo htmlspecialchars($notification->event_name); ?>
                                    </h5>
                                    <div class="form-check form-switch custom-switch-lg">
                                        <input type="checkbox" 
                                               class="form-check-input notification-toggle" 
                                               name="notification_status[<?php echo $notification->event_type; ?>]" 
                                               id="status_<?php echo $notification->event_type; ?>"
                                               value="1"
                                               <?php echo ($notification->status == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="status_<?php echo $notification->event_type; ?>">
                                            <span class="status-badge <?php echo ($notification->status == 1) ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ($notification->status == 1) ? 'Enabled' : 'Disabled'; ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                
                                <?php if (!empty($notification->description)): ?>
                                    <p class="notification-description text-muted mb-0 mt-2">
                                        <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($notification->description); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="notification-body">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">
                                        <i class="far fa-file-lines"></i> Message Template
                                    </label>
                                    <textarea 
                                        class="form-control template-textarea" 
                                        name="notification_template[<?php echo $notification->event_type; ?>]" 
                                        rows="8"
                                        placeholder="Enter message template"><?php echo htmlspecialchars($notification->template); ?></textarea>
                                </div>
                                
                                <?php if (!empty($variables)): ?>
                                    <div class="variables-info">
                                        <strong><i class="fas fa-code"></i> Available Variables:</strong>
                                        <div class="variable-tags mt-2">
                                            <?php foreach ($variables as $var): ?>
                                                <span class="variable-tag">{<?php echo htmlspecialchars($var); ?>}</span>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="d-block mt-2 text-muted">
                                            <i class="far fa-lightbulb"></i> Use these variables in your template. They will be replaced with actual values when the notification is sent.
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                    <?php endforeach; ?>
                    
                    <div class="form-footer mt-4 pt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save All Notification Templates
                        </button>
                    </div>
                </form>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Notification Cards */
.notification-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #ffffff;
}

.notification-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #25D366;
}

.notification-header {
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.notification-body {
    padding: 1.25rem;
}

.notification-title {
    color: #212529;
}

.notification-description {
    color: #6c757d;
}

/* Custom Switch */
.custom-switch-lg .form-check-label::before {
    width: 3rem;
    height: 1.5rem;
    border-radius: 3rem;
}

.custom-switch-lg .form-check-label::after {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
}

.custom-switch-lg .form-check-input:checked ~ .form-check-label::after {
    transform: translateX(1.5rem);
}

/* Status Badge */
.status-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
}

.bg-success {
    background-color: #28a745;
    color: white;
}

.bg-secondary {
    background-color: #6c757d;
    color: white;
}

/* Template Textarea */
.template-textarea {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    transition: border-color 0.3s ease;
}

.template-textarea:focus {
    border-color: #25D366;
    box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25);
}

/* Variables Info */
.variables-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border-left: 4px solid #25D366;
}

.variable-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.variable-tag {
    background: #25D366;
    color: white;
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    cursor: pointer;
    transition: all 0.2s ease;
}

.variable-tag:hover {
    background: #1ea952;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
}

/* Form Elements */
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.card-header {
    font-weight: 600;
}

.dark-mode-hr {
    border-top: 2px solid #dee2e6;
}

.form-footer {
    border-top: 2px solid #e9ecef;
}

/* ==================== DARK MODE STYLES ==================== */
@media (prefers-color-scheme: dark) {
    /* Notification Cards - Dark Mode */
    .notification-card {
        border-color: #404040;
        background: #2b2b2b;
    }

    .notification-card:hover {
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);
        border-color: #25D366;
    }

    .notification-header {
        background: linear-gradient(135deg, #353535 0%, #2b2b2b 100%);
        border-bottom-color: #404040;
    }

    .notification-body {
        background: #2b2b2b;
    }

    .notification-title {
        color: #e0e0e0;
    }

    .notification-description {
        color: #a0a0a0 !important;
    }

    /* Template Textarea - Dark Mode */
    .template-textarea {
        background-color: #1e1e1e;
        border-color: #404040;
        color: #e0e0e0;
    }

    .template-textarea:focus {
        background-color: #1e1e1e;
        border-color: #25D366;
        color: #e0e0e0;
    }

    .template-textarea::placeholder {
        color: #6c757d;
    }

    /* Variables Info - Dark Mode */
    .variables-info {
        background: #1e1e1e;
        border-left-color: #25D366;
    }

    .variables-info strong {
        color: #e0e0e0;
    }

    /* Form Elements - Dark Mode */
    .form-label {
        color: #e0e0e0;
    }

    .dark-mode-hr {
        border-top-color: #404040;
    }

    .form-footer {
        border-top-color: #404040;
    }
}

/* Manual Dark Mode Class Support (if using body.dark-mode class) */
body.dark-mode .notification-card {
    border-color: #404040;
    background: #2b2b2b;
}

body.dark-mode .notification-card:hover {
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);
    border-color: #25D366;
}

body.dark-mode .notification-header {
    background: linear-gradient(135deg, #353535 0%, #2b2b2b 100%);
    border-bottom-color: #404040;
}

body.dark-mode .notification-body {
    background: #2b2b2b;
}

body.dark-mode .notification-title {
    color: #e0e0e0;
}

body.dark-mode .notification-description {
    color: #a0a0a0 !important;
}

body.dark-mode .template-textarea {
    background-color: #1e1e1e;
    border-color: #404040;
    color: #e0e0e0;
}

body.dark-mode .template-textarea:focus {
    background-color: #1e1e1e;
    border-color: #25D366;
    color: #e0e0e0;
}

body.dark-mode .template-textarea::placeholder {
    color: #6c757d;
}

body.dark-mode .variables-info {
    background: #1e1e1e;
    border-left-color: #25D366;
}

body.dark-mode .variables-info strong {
    color: #e0e0e0;
}

body.dark-mode .form-label {
    color: #e0e0e0;
}

body.dark-mode .dark-mode-hr {
    border-top-color: #404040;
}

body.dark-mode .form-footer {
    border-top-color: #404040;
}
</style>

<script>
$(document).ready(function() {
    // Update badge text when switch changes
    $('.notification-toggle').on('change', function() {
        var badge = $(this).siblings('.form-check-label').find('.status-badge');
        if ($(this).is(':checked')) {
            badge.removeClass('bg-secondary').addClass('bg-success').text('Enabled');
        } else {
            badge.removeClass('bg-success').addClass('bg-secondary').text('Disabled');
        }
    });

    // Copy variable to clipboard when clicked
    $('.variable-tag').on('click', function() {
        var text = $(this).text();
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(text).select();
        document.execCommand('copy');
        temp.remove();
        
        // Show feedback
        var original = $(this).text();
        $(this).text('Copied!');
        var self = this;
        setTimeout(function() {
            $(self).text(original);
        }, 1000);
    });
});
</script>