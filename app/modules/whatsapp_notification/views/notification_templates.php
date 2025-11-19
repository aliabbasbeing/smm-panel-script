<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card">
    <div class="card-header" style="border: 0.1px solid #25D366; border-radius: 3.5px 3.5px 0px 0px; background: #25D366 !important;">
        <h3 class="card-title text-white">
            <i class="fa fa-bell"></i> WhatsApp Notification Templates
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="fa fa-info-circle"></i> 
            <strong>Manage Notifications:</strong> Enable or disable notifications and customize message templates for different events.
        </div>

        <?php
        if (empty($notifications)): ?>
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                <strong>Notice:</strong> WhatsApp notification templates are not set up yet. Please run the database migration file: 
                <code>/database/whatsapp-notifications.sql</code>
            </div>
        <?php else: ?>
            
            <form class="actionForm" method="POST" action="<?php echo cn('whatsapp_notification/ajax_save_notification_templates'); ?>" data-redirect="<?= get_current_url(); ?>">
                
                <?php foreach ($notifications as $notification): 
                    $variables = json_decode($notification->variables, true);
                    if (!is_array($variables)) {
                        $variables = array();
                    }
                ?>
                    
                    <div class="notification-card mb-4">
                        <div class="notification-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fa fa-bell text-primary"></i> 
                                    <?php echo htmlspecialchars($notification->event_name); ?>
                                </h5>
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" 
                                           class="custom-control-input notification-toggle" 
                                           name="notification_status[<?php echo $notification->event_type; ?>]" 
                                           id="status_<?php echo $notification->event_type; ?>"
                                           value="1"
                                           <?php echo ($notification->status == 1) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="status_<?php echo $notification->event_type; ?>">
                                        <span class="status-badge <?php echo ($notification->status == 1) ? 'badge-success' : 'badge-secondary'; ?>">
                                            <?php echo ($notification->status == 1) ? 'Enabled' : 'Disabled'; ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <?php if (!empty($notification->description)): ?>
                                <p class="text-muted mb-0 mt-2">
                                    <i class="fa fa-info-circle"></i> <?php echo htmlspecialchars($notification->description); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notification-body">
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">
                                    <i class="fa fa-file-text-o"></i> Message Template
                                </label>
                                <textarea 
                                    class="form-control template-textarea" 
                                    name="notification_template[<?php echo $notification->event_type; ?>]" 
                                    rows="8"
                                    placeholder="Enter message template"><?php echo htmlspecialchars($notification->template); ?></textarea>
                            </div>
                            
                            <?php if (!empty($variables)): ?>
                                <div class="variables-info">
                                    <strong><i class="fa fa-code"></i> Available Variables:</strong>
                                    <div class="variable-tags mt-2">
                                        <?php foreach ($variables as $var): ?>
                                            <span class="variable-tag">{<?php echo htmlspecialchars($var); ?>}</span>
                                        <?php endforeach; ?>
                                    </div>
                                    <small class="d-block mt-2 text-muted">
                                        <i class="fa fa-lightbulb-o"></i> Use these variables in your template. They will be replaced with actual values when the notification is sent.
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
                
                <div class="form-footer mt-4 pt-3" style="border-top: 2px solid #e9ecef;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Save All Notification Settings
                    </button>
                    <a href="<?= cn('whatsapp_notification/api_settings') ?>" class="btn btn-outline-secondary btn-lg ml-2">
                        <i class="fa fa-cog"></i> API Settings
                    </a>
                </div>
            </form>
            
        <?php endif; ?>
    </div>
</div>

<style>
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

.custom-switch-lg .custom-control-label::before {
    width: 3rem;
    height: 1.5rem;
    border-radius: 3rem;
}

.custom-switch-lg .custom-control-label::after {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
}

.custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateX(1.5rem);
}

.status-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

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

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.card-header {
    font-weight: 600;
}
</style>

<script>
$(document).ready(function() {
    // Update badge text when switch changes
    $('.notification-toggle').on('change', function() {
        var badge = $(this).siblings('.custom-control-label').find('.status-badge');
        if ($(this).is(':checked')) {
            badge.removeClass('badge-secondary').addClass('badge-success').text('Enabled');
        } else {
            badge.removeClass('badge-success').addClass('badge-secondary').text('Disabled');
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
