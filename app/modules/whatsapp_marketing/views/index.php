<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?> Dashboard</h3>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Campaigns</h5>
                                    <h2><?php echo $stats->total_campaigns; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Running Campaigns</h5>
                                    <h2><?php echo $stats->running_campaigns; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Messages</h5>
                                    <h2><?php echo $stats->total_messages; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Messages Sent</h5>
                                    <h2><?php echo $stats->total_sent; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Failed Messages</h5>
                                    <h2><?php echo $stats->total_failed; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Remaining</h5>
                                    <h2><?php echo $stats->total_remaining; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Completed</h5>
                                    <h2><?php echo $stats->completed_campaigns; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Failure Rate</h5>
                                    <h2><?php echo $stats->failure_rate; ?>%</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Quick Actions</h4>
                            <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-primary">
                                <i class="fa fa-list"></i> View Campaigns
                            </a>
                            <a href="<?php echo cn($module . '/templates'); ?>" class="btn btn-info">
                                <i class="fa fa-file-text"></i> Manage Templates
                            </a>
                            <a href="<?php echo cn($module . '/api'); ?>" class="btn btn-success">
                                <i class="fa fa-cog"></i> API Configuration
                            </a>
                            <a href="<?php echo cn($module . '/reports'); ?>" class="btn btn-warning">
                                <i class="fa fa-bar-chart"></i> View Reports
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <?php if(!empty($recent_logs)): ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Recent Activity</h4>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Phone Number</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_logs as $log): ?>
                                    <tr>
                                        <td><?php echo $log->campaign_name; ?></td>
                                        <td><?php echo $log->phone_number; ?></td>
                                        <td>
                                            <?php if($log->status == 'sent'): ?>
                                                <span class="badge badge-success">Sent</span>
                                            <?php elseif($log->status == 'failed'): ?>
                                                <span class="badge badge-danger">Failed</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?php echo ucfirst($log->status); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $log->created_at; ?></td>
                                        <td><?php echo $log->error_message ? substr($log->error_message, 0, 50) : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
