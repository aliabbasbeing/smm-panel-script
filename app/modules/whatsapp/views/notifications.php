<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.whatsapp-header {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    padding: 20px;
    border-radius: 8px 8px 0 0;
}
.nav-tabs-whatsapp .nav-link {
    color: #128C7E;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 12px 20px;
}
.nav-tabs-whatsapp .nav-link.active {
    color: #25D366;
    background: transparent;
    border-bottom-color: #25D366;
}
.nav-tabs-whatsapp .nav-link:hover {
    border-bottom-color: #25D366;
}
.notification-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #ffffff;
    margin-bottom: 20px;
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
    background-color: #25D366;
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
    margin: 0.25rem;
}
.variable-tag:hover {
    background: #1ea952;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
}
.test-message-card {
    border: 2px solid #25D366;
    border-radius: 8px;
}
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fa fa-whatsapp text-success"></i> <?=lang("WhatsApp Manager")?>
    </h1>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs nav-tabs-whatsapp mb-4">
    <li class="nav-item">
        <a class="nav-link" href="<?=cn('whatsapp/device')?>">
            <i class="fe fe-smartphone"></i> <?=lang("Device")?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?=cn('whatsapp/notifications')?>">
            <i class="fe fe-bell"></i> <?=lang("Notifications")?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?=cn('whatsapp/logs')?>">
            <i class="fe fe-list"></i> <?=lang("Logs")?>
        </a>
    </li>
</ul>

<?php if (!$is_configured): ?>
<div class="alert alert-warning">
    <i class="fe fe-alert-circle"></i> 
    <strong><?=lang("Not Configured")?></strong> - 
    <?=lang("Please configure the WhatsApp API in the Device tab first.")?>
    <a href="<?=cn('whatsapp/device')?>" class="btn btn-sm btn-warning ml-3"><?=lang("Configure Now")?></a>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <!-- Notification Templates -->
        <div class="card">
            <div class="whatsapp-header">
                <h4 class="mb-0">
                    <i class="fe fe-bell"></i> <?=lang("Notification Templates")?>
                </h4>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong><?=lang("Notice")?>:</strong> <?=lang("WhatsApp notification templates are not set up yet. Please run the database migration file.")?>
                    <code>/database/whatsapp-notifications.sql</code>
                </div>
                <?php else: ?>
                
                <form id="notificationsForm">
                    <?php foreach ($notifications as $notification): 
                        $variables = json_decode($notification->variables, true);
                        if (!is_array($variables)) {
                            $variables = array();
                        }
                    ?>
                    <div class="notification-card">
                        <div class="notification-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 notification-title">
                                    <i class="fa fa-bell text-primary"></i> 
                                    <?=htmlspecialchars($notification->event_name)?>
                                </h5>
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" 
                                           class="custom-control-input notification-toggle" 
                                           name="notification_status[<?=$notification->event_type?>]" 
                                           id="status_<?=$notification->event_type?>"
                                           value="1"
                                           <?=($notification->status == 1) ? 'checked' : ''?>>
                                    <label class="custom-control-label" for="status_<?=$notification->event_type?>">
                                        <span class="status-badge <?=($notification->status == 1) ? 'badge-success' : 'badge-secondary'?>">
                                            <?=($notification->status == 1) ? lang('Enabled') : lang('Disabled')?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <?php if (!empty($notification->description)): ?>
                            <p class="notification-description text-muted mb-0 mt-2">
                                <i class="fa fa-info-circle"></i> <?=htmlspecialchars($notification->description)?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notification-body">
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">
                                    <i class="fa fa-file-text-o"></i> <?=lang("Message Template")?>
                                </label>
                                <textarea 
                                    class="form-control template-textarea" 
                                    name="notification_template[<?=$notification->event_type?>]" 
                                    rows="6"
                                    placeholder="<?=lang("Enter message template")?>"><?=htmlspecialchars($notification->template)?></textarea>
                            </div>
                            
                            <?php if (!empty($variables)): ?>
                            <div class="variables-info">
                                <strong><i class="fa fa-code"></i> <?=lang("Available Variables")?>:</strong>
                                <div class="mt-2">
                                    <?php foreach ($variables as $var): ?>
                                    <span class="variable-tag" data-var="{<?=$var?>}">{<?=htmlspecialchars($var)?>}</span>
                                    <?php endforeach; ?>
                                </div>
                                <small class="d-block mt-2 text-muted">
                                    <i class="fa fa-lightbulb-o"></i> <?=lang("Click to copy. Variables will be replaced with actual values.")?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-save"></i> <?=lang("Save All Templates")?>
                        </button>
                    </div>
                </form>
                
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Test Message Card -->
        <div class="card test-message-card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fe fe-send"></i> <?=lang("Send Test Message")?></h5>
            </div>
            <div class="card-body">
                <form id="testMessageForm">
                    <div class="form-group">
                        <label><?=lang("Phone Number")?></label>
                        <input type="text" name="phone" class="form-control" 
                               placeholder="<?=lang("Leave empty for admin phone")?>"
                               value="<?=isset($config->admin_phone) ? html_escape($config->admin_phone) : ''?>">
                        <small class="text-muted"><?=lang("With country code, e.g. +923001234567")?></small>
                    </div>
                    <div class="form-group">
                        <label><?=lang("Message")?></label>
                        <textarea name="message" class="form-control" rows="4" 
                                  placeholder="<?=lang("Test message...")?>"><?=lang("This is a test message from your SMM Panel WhatsApp integration.")?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block" <?=!$is_configured ? 'disabled' : ''?>>
                        <i class="fe fe-send"></i> <?=lang("Send Test Message")?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fe fe-bar-chart-2"></i> <?=lang("Quick Stats")?></h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span><?=lang("Total Templates")?></span>
                    <strong><?=count($notifications)?></strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span><?=lang("Enabled")?></span>
                    <strong class="text-success"><?=count(array_filter($notifications, function($n) { return $n->status == 1; }))?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><?=lang("Disabled")?></span>
                    <strong class="text-muted"><?=count(array_filter($notifications, function($n) { return $n->status == 0; }))?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var csrfName = '<?=$this->security->get_csrf_token_name()?>';
    var csrfHash = '<?=$this->security->get_csrf_hash()?>';

    // Show toast message
    function showMessage(message, type) {
        if (typeof $.toast === 'function') {
            $.toast({
                heading: type === 'success' ? 'Success' : 'Error',
                text: message,
                position: 'top-right',
                loaderBg: type === 'success' ? '#25D366' : '#dc3545',
                icon: type,
                hideAfter: 3000
            });
        } else {
            alert(message);
        }
    }

    // Update badge text when switch changes
    $('.notification-toggle').on('change', function() {
        var badge = $(this).siblings('.custom-control-label').find('.status-badge');
        if ($(this).is(':checked')) {
            badge.removeClass('badge-secondary').addClass('badge-success').text('<?=lang("Enabled")?>');
        } else {
            badge.removeClass('badge-success').addClass('badge-secondary').text('<?=lang("Disabled")?>');
        }
    });

    // Copy variable to clipboard when clicked
    $('.variable-tag').on('click', function() {
        var text = $(this).data('var');
        var self = this;
        var original = $(this).text();
        
        // Use modern Clipboard API with fallback
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                $(self).text('<?=lang("Copied!")?>');
                setTimeout(function() {
                    $(self).text(original);
                }, 1000);
            }).catch(function() {
                copyFallback(text, self, original);
            });
        } else {
            copyFallback(text, self, original);
        }
    });
    
    function copyFallback(text, elem, original) {
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(text).select();
        try {
            document.execCommand('copy');
            $(elem).text('<?=lang("Copied!")?>');
        } catch (e) {
            $(elem).text('<?=lang("Failed")?>');
        }
        temp.remove();
        setTimeout(function() {
            $(elem).text(original);
        }, 1000);
    }

    // Save notifications form
    $('#notificationsForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&' + csrfName + '=' + csrfHash;
        
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fe fe-loader spin"></i> <?=lang("Saving...")?>');
        
        $.ajax({
            url: '<?=cn("whatsapp/save_notification_settings")?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?=lang("Save All Templates")?>');
                if (response.status === 'success') {
                    showMessage(response.message, 'success');
                } else {
                    showMessage(response.message, 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?=lang("Save All Templates")?>');
                showMessage('<?=lang("Failed to save settings")?>', 'error');
            }
        });
    });

    // Send test message
    $('#testMessageForm').on('submit', function(e) {
        e.preventDefault();
        
        var phone = $('input[name="phone"]').val();
        var message = $('textarea[name="message"]').val();
        
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fe fe-loader spin"></i> <?=lang("Sending...")?>');
        
        var data = {
            phone: phone,
            message: message
        };
        data[csrfName] = csrfHash;
        
        $.ajax({
            url: '<?=cn("whatsapp/test_message")?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fe fe-send"></i> <?=lang("Send Test Message")?>');
                if (response.status === 'success') {
                    showMessage(response.message, 'success');
                } else {
                    showMessage(response.message || '<?=lang("Failed to send message")?>', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fe fe-send"></i> <?=lang("Send Test Message")?>');
                showMessage('<?=lang("Connection error")?>', 'error');
            }
        });
    });

    // Spin animation
    $('<style>.spin { animation: spin 1s linear infinite; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>').appendTo('head');
});
</script>
