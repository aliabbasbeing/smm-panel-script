<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?></h3>
                    <div class="card-options">
                        <a href="<?php echo cn('cron_logs'); ?>" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Logs
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="<?php echo cn('cron_logs/save_settings'); ?>" method="POST" class="actionForm">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Notification Settings</h5>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="enable_email_notifications" value="1" <?php echo (isset($settings['enable_email_notifications']) && $settings['enable_email_notifications'] == '1') ? 'checked' : ''; ?>>
                                        <span class="custom-control-label">Enable Email Notifications for Failed Cron Jobs</span>
                                    </label>
                                    <small class="form-text text-muted">
                                        Send an email notification when a cron job fails.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notification Email Address</label>
                                    <input type="email" class="form-control" name="notification_email" value="<?php echo isset($settings['notification_email']) ? esc($settings['notification_email']) : ''; ?>" placeholder="admin@example.com">
                                    <small class="form-text text-muted">
                                        Email address to receive failure notifications.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mt-4">
                                <h5 class="mb-3">Log Retention</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Keep Logs For (Days)</label>
                                    <input type="number" class="form-control" name="log_retention_days" value="<?php echo isset($settings['log_retention_days']) ? esc($settings['log_retention_days']) : '30'; ?>" min="1" max="365">
                                    <small class="form-text text-muted">
                                        Number of days to keep cron logs before automatic cleanup. Default is 30 days.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
