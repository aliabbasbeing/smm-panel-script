<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-bar-chart-2"></i> Email Marketing Reports
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Overall Statistics -->
<div class="row">
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Total Campaigns</h6>
            <span class="h2 mb-0"><?php echo number_format($stats->total_campaigns); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-mail text-primary mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Total Sent</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($stats->total_sent); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-send text-success mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Open Rate</h6>
            <span class="h2 mb-0 text-info"><?php echo $stats->open_rate; ?>%</span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-eye text-info mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Failure Rate</h6>
            <span class="h2 mb-0 text-danger"><?php echo $stats->failure_rate; ?>%</span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Campaign Performance Table -->
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Campaign Performance Summary</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th>Campaign Name</th>
              <th>Status</th>
              <th>Total Emails</th>
              <th>Sent</th>
              <th>Opened</th>
              <th>Failed</th>
              <th>Success Rate</th>
              <th>Open Rate</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($campaigns)){ 
              foreach($campaigns as $campaign){
                $status_class = 'secondary';
                switch($campaign->status){
                  case 'running': $status_class = 'success'; break;
                  case 'completed': $status_class = 'info'; break;
                  case 'paused': $status_class = 'warning'; break;
                  case 'cancelled': $status_class = 'danger'; break;
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
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($campaign->name); ?></strong>
                <br><small class="text-muted"><?php echo date('M d, Y', strtotime($campaign->created_at)); ?></small>
              </td>
              <td><span class="badge badge-<?php echo $status_class; ?>"><?php echo ucfirst($campaign->status); ?></span></td>
              <td><?php echo number_format($campaign->total_emails); ?></td>
              <td><span class="text-success"><?php echo number_format($campaign->sent_emails); ?></span></td>
              <td><span class="text-info"><?php echo number_format($campaign->opened_emails); ?></span></td>
              <td><span class="text-danger"><?php echo number_format($campaign->failed_emails); ?></span></td>
              <td>
                <div class="clearfix">
                  <div class="float-start"><strong><?php echo $success_rate; ?>%</strong></div>
                </div>
                <div class="progress progress-sm">
                  <div class="progress-bar bg-success" style="width: <?php echo $success_rate; ?>%"></div>
                </div>
              </td>
              <td>
                <div class="clearfix">
                  <div class="float-start"><strong><?php echo $open_rate; ?>%</strong></div>
                </div>
                <div class="progress progress-sm">
                  <div class="progress-bar bg-info" style="width: <?php echo $open_rate; ?>%"></div>
                </div>
              </td>
              <td>
                <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" class="btn btn-sm btn-primary" title="View Details">
                  <i class="fe fe-eye"></i>
                </a>
                <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="btn btn-sm btn-success" title="Export CSV">
                  <i class="fe fe-download"></i>
                </a>
              </td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="9" class="text-center">No campaigns found</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Email Statistics Breakdown -->
<div class="row mt-4">
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Email Delivery Statistics</h3>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-6">
            <div class="text-muted">Total Emails</div>
            <div class="h3"><?php echo number_format($stats->total_emails); ?></div>
          </div>
          <div class="col-6">
            <div class="text-muted">Successfully Sent</div>
            <div class="h3 text-success"><?php echo number_format($stats->total_sent - $stats->total_failed); ?></div>
          </div>
        </div>
        
        <div class="mb-3">
          <div class="clearfix">
            <div class="float-start"><strong>Sent Emails</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo number_format($stats->total_sent); ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-success" style="width: <?php echo $stats->total_emails > 0 ? round(($stats->total_sent / $stats->total_emails) * 100) : 0; ?>%"></div>
          </div>
        </div>
        
        <div class="mb-3">
          <div class="clearfix">
            <div class="float-start"><strong>Pending Emails</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo number_format($stats->total_remaining); ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-primary" style="width: <?php echo $stats->total_emails > 0 ? round(($stats->total_remaining / $stats->total_emails) * 100) : 0; ?>%"></div>
          </div>
        </div>
        
        <div class="mb-3">
          <div class="clearfix">
            <div class="float-start"><strong>Failed Emails</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo number_format($stats->total_failed); ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-danger" style="width: <?php echo $stats->total_emails > 0 ? round(($stats->total_failed / $stats->total_emails) * 100) : 0; ?>%"></div>
          </div>
        </div>
        
        <div class="mb-0">
          <div class="clearfix">
            <div class="float-start"><strong>Opened Emails</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo number_format($stats->total_opened); ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-info" style="width: <?php echo $stats->total_sent > 0 ? round(($stats->total_opened / $stats->total_sent) * 100) : 0; ?>%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Campaign Status Distribution</h3>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="clearfix">
            <div class="float-start"><strong>Running</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo $stats->running_campaigns; ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-success" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->running_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
          </div>
        </div>
        
        <div class="mb-3">
          <div class="clearfix">
            <div class="float-start"><strong>Completed</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo $stats->completed_campaigns; ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-info" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->completed_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
          </div>
        </div>
        
        <div class="mb-3">
          <div class="clearfix">
            <div class="float-start"><strong>Paused</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo $stats->paused_campaigns; ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-warning" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->paused_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
          </div>
        </div>
        
        <div class="mb-0">
          <div class="clearfix">
            <div class="float-start"><strong>Pending</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo $stats->pending_campaigns; ?></small></div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar bg-secondary" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->pending_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Export Options -->
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Export Options</h3>
      </div>
      <div class="card-body">
        <h4>Individual Campaign Reports</h4>
        <p>From each campaign's detail page, you can:</p>
        <ul>
          <li>View real-time statistics (sent, opened, failed, bounced)</li>
          <li>Export full campaign report as CSV</li>
          <li>View recipient status and activity logs</li>
          <li>Track open rates and delivery rates</li>
          <li>Resend failed emails individually or in bulk</li>
        </ul>
        
        <p class="mt-3">
          <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-primary">
            <i class="fe fe-mail"></i> View All Campaigns
          </a>
        </p>
      </div>
    </div>
  </div>
</div>
