<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bar-chart"></i> WhatsApp Marketing Reports</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Campaigns</h5>
                            <h2><?php echo $stats->total_campaigns; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Messages Sent</h5>
                            <h2><?php echo $stats->total_sent; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Failed</h5>
                            <h2><?php echo $stats->total_failed; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Failure Rate</h5>
                            <h2><?php echo $stats->failure_rate; ?>%</h2>
                        </div>
                    </div>
                </div>
            </div>
            <h4>Campaign Reports</h4>
            <?php if(!empty($campaigns)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Sent</th>
                        <th>Failed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($campaigns as $campaign): ?>
                    <tr>
                        <td><?php echo $campaign->name; ?></td>
                        <td><span class="badge badge-<?php echo $campaign->status == 'completed' ? 'primary' : 'success'; ?>"><?php echo $campaign->status; ?></span></td>
                        <td><?php echo $campaign->total_messages; ?></td>
                        <td><?php echo $campaign->sent_messages; ?></td>
                        <td><?php echo $campaign->failed_messages; ?></td>
                        <td>
                            <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No campaigns found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
