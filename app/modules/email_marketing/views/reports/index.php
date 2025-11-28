<!-- Enhanced Reports Page -->
<style>
.reports-page .page-header-enhanced {
  background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%);
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  color: white;
}
.reports-page .stat-card-enhanced {
  border-radius: 12px;
  border: none;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: transform 0.2s ease;
}
.reports-page .stat-card-enhanced:hover {
  transform: translateY(-5px);
}
.reports-page .stat-icon-lg {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}
.reports-page .report-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.reports-page .campaign-row:hover {
  background: rgba(111, 66, 193, 0.05);
}
.reports-page .progress-bar-xl {
  height: 10px;
  border-radius: 5px;
}
.reports-page .action-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 2px;
  transition: all 0.2s ease;
}
.reports-page .action-btn:hover {
  transform: scale(1.1);
}
</style>

<div class="reports-page">
  <!-- Enhanced Header -->
  <div class="page-header-enhanced">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h2 class="mb-1 text-white">
          <i class="fe fe-bar-chart-2 mr-2"></i>Email Marketing Reports
        </h2>
        <p class="mb-0 text-white-50">Analytics, performance metrics, and campaign insights</p>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?php echo cn($module); ?>" class="btn btn-light btn-sm">
          <i class="fe fe-arrow-left mr-1"></i> Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- Overall Statistics -->
  <div class="row mb-4">
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card-enhanced h-100" style="border-top: 4px solid #467fcf;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Total Campaigns</p>
              <h2 class="mb-0 text-primary"><?php echo number_format($stats->total_campaigns); ?></h2>
            </div>
            <div class="stat-icon-lg" style="background: rgba(70, 127, 207, 0.1);">
              <i class="fe fe-mail text-primary"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card-enhanced h-100" style="border-top: 4px solid #28a745;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Total Sent</p>
              <h2 class="mb-0 text-success"><?php echo number_format($stats->total_sent); ?></h2>
            </div>
            <div class="stat-icon-lg" style="background: rgba(40, 167, 69, 0.1);">
              <i class="fe fe-send text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card-enhanced h-100" style="border-top: 4px solid #17a2b8;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Open Rate</p>
              <h2 class="mb-0 text-info"><?php echo $stats->open_rate; ?>%</h2>
            </div>
            <div class="stat-icon-lg" style="background: rgba(23, 162, 184, 0.1);">
              <i class="fe fe-eye text-info"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card-enhanced h-100" style="border-top: 4px solid #dc3545;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Failure Rate</p>
              <h2 class="mb-0 text-danger"><?php echo $stats->failure_rate; ?>%</h2>
            </div>
            <div class="stat-icon-lg" style="background: rgba(220, 53, 69, 0.1);">
              <i class="fe fe-x-circle text-danger"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Campaign Performance Table -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card report-card">
        <div class="card-header" style="background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-trending-up mr-2"></i>Campaign Performance Summary</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background: #f1f3f5;">
              <tr>
                <th class="border-top-0">Campaign</th>
                <th class="border-top-0">Status</th>
                <th class="border-top-0 text-center">Total</th>
                <th class="border-top-0 text-center">Sent</th>
                <th class="border-top-0 text-center">Opened</th>
                <th class="border-top-0 text-center">Failed</th>
                <th class="border-top-0">Success Rate</th>
                <th class="border-top-0">Open Rate</th>
                <th class="border-top-0 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($campaigns)){ 
                foreach($campaigns as $campaign){
                  $status_class = 'secondary';
                  $status_icon = 'fe-inbox';
                  switch($campaign->status){
                    case 'running': $status_class = 'success'; $status_icon = 'fe-play-circle'; break;
                    case 'completed': $status_class = 'info'; $status_icon = 'fe-check-circle'; break;
                    case 'paused': $status_class = 'warning'; $status_icon = 'fe-pause-circle'; break;
                    case 'cancelled': $status_class = 'danger'; $status_icon = 'fe-x-circle'; break;
                  }
                  
                  $success_rate = 0;
                  if($campaign->total_emails > 0){
                    $success_rate = round((($campaign->sent_emails - $campaign->failed_emails) / $campaign->total_emails) * 100, 1);
                  }
                  
                  $open_rate = 0;
                  if($campaign->sent_emails > 0){
                    $open_rate = round(($campaign->opened_emails / $campaign->sent_emails) * 100, 1);
                  }
              ?>
              <tr class="campaign-row">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%);">
                      <i class="fe fe-mail text-white"></i>
                    </div>
                    <div>
                      <strong><?php echo htmlspecialchars($campaign->name); ?></strong>
                      <br><small class="text-muted"><?php echo date('M d, Y', strtotime($campaign->created_at)); ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge badge-<?php echo $status_class; ?>" style="padding: 5px 10px; border-radius: 15px;">
                    <i class="fe <?php echo $status_icon; ?> mr-1"></i><?php echo ucfirst($campaign->status); ?>
                  </span>
                </td>
                <td class="text-center font-weight-bold"><?php echo number_format($campaign->total_emails); ?></td>
                <td class="text-center"><span class="text-success font-weight-bold"><?php echo number_format($campaign->sent_emails); ?></span></td>
                <td class="text-center"><span class="text-info font-weight-bold"><?php echo number_format($campaign->opened_emails); ?></span></td>
                <td class="text-center"><span class="text-danger font-weight-bold"><?php echo number_format($campaign->failed_emails); ?></span></td>
                <td style="min-width: 120px;">
                  <div class="d-flex align-items-center">
                    <span class="font-weight-bold mr-2"><?php echo $success_rate; ?>%</span>
                    <div class="progress flex-grow-1 progress-bar-xl">
                      <div class="progress-bar bg-success" style="width: <?php echo $success_rate; ?>%"></div>
                    </div>
                  </div>
                </td>
                <td style="min-width: 120px;">
                  <div class="d-flex align-items-center">
                    <span class="font-weight-bold mr-2"><?php echo $open_rate; ?>%</span>
                    <div class="progress flex-grow-1 progress-bar-xl">
                      <div class="progress-bar bg-info" style="width: <?php echo $open_rate; ?>%"></div>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" class="action-btn btn btn-outline-primary btn-sm" title="View Details">
                    <i class="fe fe-eye"></i>
                  </a>
                  <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="action-btn btn btn-outline-success btn-sm" title="Export CSV">
                    <i class="fe fe-download"></i>
                  </a>
                </td>
              </tr>
              <?php }} else { ?>
              <tr>
                <td colspan="9" class="text-center py-5">
                  <i class="fe fe-inbox text-muted" style="font-size: 50px;"></i>
                  <p class="text-muted mt-3 mb-0">No campaigns found</p>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Breakdown -->
  <div class="row mb-4">
    <div class="col-lg-6 mb-3">
      <div class="card report-card h-100">
        <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-send mr-2"></i>Email Delivery Statistics</h5>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-6 text-center">
              <h2 class="text-primary mb-0"><?php echo number_format($stats->total_emails); ?></h2>
              <small class="text-muted">Total Emails</small>
            </div>
            <div class="col-6 text-center">
              <h2 class="text-success mb-0"><?php echo number_format($stats->total_sent - $stats->total_failed); ?></h2>
              <small class="text-muted">Successfully Delivered</small>
            </div>
          </div>
          
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-check-circle text-success mr-2"></i>Sent</span>
              <span class="font-weight-bold"><?php echo number_format($stats->total_sent); ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-success" style="width: <?php echo $stats->total_emails > 0 ? round(($stats->total_sent / $stats->total_emails) * 100) : 0; ?>%"></div>
            </div>
          </div>
          
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-clock text-primary mr-2"></i>Pending</span>
              <span class="font-weight-bold"><?php echo number_format($stats->total_remaining); ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-primary" style="width: <?php echo $stats->total_emails > 0 ? round(($stats->total_remaining / $stats->total_emails) * 100) : 0; ?>%"></div>
            </div>
          </div>
          
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-x-circle text-danger mr-2"></i>Failed</span>
              <span class="font-weight-bold"><?php echo number_format($stats->total_failed); ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-danger" style="width: <?php echo $stats->total_emails > 0 ? round(($stats->total_failed / $stats->total_emails) * 100) : 0; ?>%"></div>
            </div>
          </div>
          
          <div class="mb-0">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-eye text-info mr-2"></i>Opened</span>
              <span class="font-weight-bold"><?php echo number_format($stats->total_opened); ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-info" style="width: <?php echo $stats->total_sent > 0 ? round(($stats->total_opened / $stats->total_sent) * 100) : 0; ?>%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6 mb-3">
      <div class="card report-card h-100">
        <div class="card-header" style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-layers mr-2"></i>Campaign Status Distribution</h5>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-play-circle text-success mr-2"></i>Running</span>
              <span class="font-weight-bold text-success"><?php echo $stats->running_campaigns; ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-success" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->running_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
            </div>
          </div>
          
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-check-circle text-info mr-2"></i>Completed</span>
              <span class="font-weight-bold text-info"><?php echo $stats->completed_campaigns; ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-info" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->completed_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
            </div>
          </div>
          
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-pause-circle text-warning mr-2"></i>Paused</span>
              <span class="font-weight-bold text-warning"><?php echo $stats->paused_campaigns; ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-warning" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->paused_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
            </div>
          </div>
          
          <div class="mb-0">
            <div class="d-flex justify-content-between mb-1">
              <span><i class="fe fe-inbox text-secondary mr-2"></i>Pending</span>
              <span class="font-weight-bold text-secondary"><?php echo $stats->pending_campaigns; ?></span>
            </div>
            <div class="progress progress-bar-xl">
              <div class="progress-bar bg-secondary" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->pending_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Export Options -->
  <div class="row">
    <div class="col-md-12">
      <div class="card report-card">
        <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-download mr-2"></i>Export & Analysis Options</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6><i class="fe fe-file-text text-primary mr-2"></i>Individual Campaign Reports</h6>
              <p class="text-muted">From each campaign's detail page, you can:</p>
              <ul class="text-muted small">
                <li>View real-time statistics (sent, opened, failed, bounced)</li>
                <li>Export full campaign report as CSV</li>
                <li>View recipient status and activity logs</li>
                <li>Track open rates and delivery rates</li>
                <li>See which SMTP server was used for each email</li>
                <li>Resend failed emails individually or in bulk</li>
              </ul>
            </div>
            <div class="col-md-6">
              <h6><i class="fe fe-zap text-success mr-2"></i>Quick Actions</h6>
              <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-primary mb-2">
                <i class="fe fe-mail mr-2"></i>View All Campaigns
              </a>
              <br>
              <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-success ajaxModal">
                <i class="fe fe-plus mr-2"></i>Create New Campaign
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
