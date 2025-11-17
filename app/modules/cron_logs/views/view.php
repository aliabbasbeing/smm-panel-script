<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> Cron Log Details</h3>
                    <div class="card-options">
                        <a href="<?php echo cn('cron_logs'); ?>" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Log ID:</label>
                                <p><?php echo esc($log->id); ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Cron Name:</label>
                                <p><?php echo esc($log->cron_name); ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Executed At:</label>
                                <p><?php echo date('Y-m-d H:i:s', strtotime($log->executed_at)); ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status:</label>
                                <p>
                                    <?php if ($log->status == 'success'): ?>
                                        <span class="badge badge-success badge-lg">Success</span>
                                    <?php elseif ($log->status == 'failed'): ?>
                                        <span class="badge badge-danger badge-lg">Failed</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning badge-lg">Running</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Response Code:</label>
                                <p><?php echo $log->response_code ? esc($log->response_code) : 'N/A'; ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Execution Time:</label>
                                <p><?php echo $log->execution_time ? number_format($log->execution_time, 4) . ' seconds' : 'N/A'; ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Response Message:</label>
                                <?php if (!empty($log->response_message)): ?>
                                    <pre class="bg-light p-3 border rounded"><?php echo esc($log->response_message); ?></pre>
                                <?php else: ?>
                                    <p class="text-muted">No response message</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Created At:</label>
                                <p><?php echo date('Y-m-d H:i:s', strtotime($log->created)); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="<?php echo cn('cron_logs/delete/' . $log->id); ?>" class="btn btn-danger actionDelete">
                            <i class="fa fa-trash"></i> Delete This Log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
