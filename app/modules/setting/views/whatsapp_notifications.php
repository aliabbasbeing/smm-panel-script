<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-whatsapp"></i> WhatsApp Notifications</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">Configure WhatsApp notification templates for various events. Each notification can be enabled/disabled individually.</p>
        
        <?php
        // Load the WhatsApp notification library
        $this->load->library('whatsapp_notification');
        $notifications = $this->whatsapp_notification->get_all_notifications();
        
        if (empty($notifications)): ?>
            <div class="alert alert-warning">
                <strong>Notice:</strong> WhatsApp notification templates are not set up yet. Please run the database migration file: 
                <code>/database/whatsapp-notifications.sql</code>
            </div>
        <?php else: ?>
            
            <form method="POST" action="<?php echo cn('setting/ajax_whatsapp_notifications'); ?>" accept-charset="utf-8">
                <?php foreach ($notifications as $notification): 
                    $variables = json_decode($notification->variables, true);
                    if (!is_array($variables)) {
                        $variables = array();
                    }
                ?>
                    
                    <div class="border rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">
                                        <i class="fa fa-bell text-primary"></i> 
                                        <?php echo htmlspecialchars($notification->event_name); ?>
                                    </h4>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               name="notification_status[<?php echo $notification->event_type; ?>]" 
                                               id="status_<?php echo $notification->event_type; ?>"
                                               value="1"
                                               <?php echo ($notification->status == 1) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="status_<?php echo $notification->event_type; ?>">
                                            <strong><?php echo ($notification->status == 1) ? 'Enabled' : 'Disabled'; ?></strong>
                                        </label>
                                    </div>
                                </div>
                                
                                <?php if (!empty($notification->description)): ?>
                                    <p class="text-muted small mb-3">
                                        <i class="fa fa-info-circle"></i> <?php echo htmlspecialchars($notification->description); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label>Message Template</label>
                                    <textarea 
                                        class="form-control" 
                                        name="notification_template[<?php echo $notification->event_type; ?>]" 
                                        rows="8"
                                        placeholder="Enter message template"><?php echo htmlspecialchars($notification->template); ?></textarea>
                                </div>
                                
                                <?php if (!empty($variables)): ?>
                                    <div class="alert alert-info mb-0">
                                        <strong>Available Variables:</strong>
                                        <div class="mt-2">
                                            <?php foreach ($variables as $var): ?>
                                                <code class="mr-2">{<?php echo htmlspecialchars($var); ?>}</code>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="d-block mt-2">Use these variables in your template. They will be replaced with actual values when the notification is sent.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Save Notification Settings
                    </button>
                </div>
            </form>
            
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle form submission
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var actionUrl = $(this).attr('action');
        
        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                // Show loading state
                $('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    show_message(response.message || 'Settings saved successfully!', 'success');
                } else {
                    show_message(response.message || 'Failed to save settings', 'error');
                }
            },
            error: function() {
                show_message('An error occurred while saving settings', 'error');
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('<i class="fa fa-save"></i> Save Notification Settings');
            }
        });
    });
    
    // Update label text when switch changes
    $('.custom-control-input').on('change', function() {
        var label = $(this).siblings('.custom-control-label').find('strong');
        if ($(this).is(':checked')) {
            label.text('Enabled');
        } else {
            label.text('Disabled');
        }
    });
});

function show_message(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>';
    
    $('.card-body').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}
</script>
