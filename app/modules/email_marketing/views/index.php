<!-- Enhanced Email Marketing Dashboard -->
<style>
.email-marketing-dashboard .stat-card {
  border-radius: 12px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: none;
  overflow: hidden;
}
.email-marketing-dashboard .stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.email-marketing-dashboard .stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}
.email-marketing-dashboard .quick-action-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  cursor: pointer;
  text-decoration: none;
}
.email-marketing-dashboard .quick-action-card:hover {
  border-color: #467fcf;
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(70, 127, 207, 0.2);
}
.email-marketing-dashboard .quick-action-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin: 0 auto 15px;
}
.email-marketing-dashboard .section-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid rgba(70, 127, 207, 0.2);
}
.email-marketing-dashboard .section-header i {
  font-size: 24px;
  color: #467fcf;
}
.email-marketing-dashboard .activity-item {
  padding: 12px 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  transition: background 0.2s;
}
.email-marketing-dashboard .activity-item:hover {
  background: rgba(70, 127, 207, 0.05);
}
.email-marketing-dashboard .activity-item:last-child {
  border-bottom: none;
}
.email-marketing-dashboard .info-card {
  border-radius: 12px;
  border-left: 4px solid #467fcf;
}
.email-marketing-dashboard .gmail-notice {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid #dc3545;
}
</style>

<div class="email-marketing-dashboard">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h1 class="page-title mb-1">
            <i class="<?php echo $module_icon; ?>" style="color: #467fcf;"></i> <?php echo $module_name; ?>
          </h1>
          <p class="text-muted mb-0">Manage your email marketing campaigns, templates, and SMTP configurations</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-primary ajaxModal">
            <i class="fe fe-plus"></i> New Campaign
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Gmail Domain Notice -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="gmail-notice">
        <div class="d-flex align-items-center">
          <div class="mr-3">
            <i class="fe fe-alert-triangle text-danger" style="font-size: 32px;"></i>
          </div>
          <div>
            <h5 class="mb-1"><strong>Gmail Only Policy Active</strong></h5>
            <p class="mb-0 text-muted">This email marketing system only sends emails to <strong>@gmail.com</strong> addresses. Non-Gmail addresses will be automatically rejected during campaign processing.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <div class="card-body text-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1 text-white-50 text-uppercase small font-weight-bold">Emails Sent</p>
              <h2 class="mb-0 font-weight-bold"><?php echo number_format($stats->total_sent); ?></h2>
              <small class="text-white-50">of <?php echo number_format($stats->total_emails); ?> total</small>
            </div>
            <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
              <i class="fe fe-send text-white"></i>
            </div>
          </div>
          <?php if($stats->total_emails > 0){ ?>
          <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" style="width: <?php echo round(($stats->total_sent / $stats->total_emails) * 100); ?>%"></div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);">
        <div class="card-body text-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1 text-white-50 text-uppercase small font-weight-bold">Pending</p>
              <h2 class="mb-0 font-weight-bold"><?php echo number_format($stats->total_remaining); ?></h2>
              <small class="text-white-50">awaiting delivery</small>
            </div>
            <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
              <i class="fe fe-clock text-white"></i>
            </div>
          </div>
          <?php if($stats->total_emails > 0){ ?>
          <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" style="width: <?php echo round(($stats->total_remaining / $stats->total_emails) * 100); ?>%"></div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="background: linear-gradient(135deg, #dc3545 0%, #e85b5b 100%);">
        <div class="card-body text-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1 text-white-50 text-uppercase small font-weight-bold">Failed</p>
              <h2 class="mb-0 font-weight-bold"><?php echo number_format($stats->total_failed); ?></h2>
              <small class="text-white-50"><?php echo $stats->failure_rate; ?>% failure rate</small>
            </div>
            <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
              <i class="fe fe-x-circle text-white"></i>
            </div>
          </div>
          <?php if($stats->total_emails > 0){ ?>
          <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" style="width: <?php echo round(($stats->total_failed / $stats->total_emails) * 100); ?>%"></div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%);">
        <div class="card-body text-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-1 text-white-50 text-uppercase small font-weight-bold">Opened</p>
              <h2 class="mb-0 font-weight-bold"><?php echo number_format($stats->total_opened); ?></h2>
              <small class="text-white-50"><?php echo $stats->open_rate; ?>% open rate</small>
            </div>
            <div class="stat-icon" style="background: rgba(255,255,255,0.2);">
              <i class="fe fe-eye text-white"></i>
            </div>
          </div>
          <?php if($stats->total_sent > 0){ ?>
          <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" style="width: <?php echo round(($stats->total_opened / $stats->total_sent) * 100); ?>%"></div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="section-header">
        <i class="fe fe-zap"></i>
        <h4 class="mb-0">Quick Actions</h4>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="<?php echo cn($module . '/campaigns'); ?>" class="card quick-action-card h-100 text-center p-4 d-block">
        <div class="quick-action-icon" style="background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);">
          <i class="fe fe-mail text-white"></i>
        </div>
        <h5 class="mb-1">Campaigns</h5>
        <p class="text-muted mb-0 small">Create & manage email campaigns</p>
        <span class="badge badge-primary mt-2"><?php echo $stats->total_campaigns; ?> total</span>
      </a>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="<?php echo cn($module . '/templates'); ?>" class="card quick-action-card h-100 text-center p-4 d-block">
        <div class="quick-action-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
          <i class="fe fe-file-text text-white"></i>
        </div>
        <h5 class="mb-1">Templates</h5>
        <p class="text-muted mb-0 small">Design email templates</p>
        <span class="badge badge-success mt-2">HTML Ready</span>
      </a>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="<?php echo cn($module . '/smtp'); ?>" class="card quick-action-card h-100 text-center p-4 d-block">
        <div class="quick-action-icon" style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
          <i class="fe fe-server text-white"></i>
        </div>
        <h5 class="mb-1">SMTP Config</h5>
        <p class="text-muted mb-0 small">Configure mail servers</p>
        <span class="badge badge-warning mt-2">Multi-SMTP</span>
      </a>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <a href="<?php echo cn($module . '/reports'); ?>" class="card quick-action-card h-100 text-center p-4 d-block">
        <div class="quick-action-icon" style="background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%);">
          <i class="fe fe-bar-chart-2 text-white"></i>
        </div>
        <h5 class="mb-1">Reports</h5>
        <p class="text-muted mb-0 small">Analytics & performance</p>
        <span class="badge badge-info mt-2">Detailed Stats</span>
      </a>
    </div>
  </div>

  <!-- Campaign Overview & Quick Stats -->
  <div class="row mb-4">
    <div class="col-lg-8 mb-3">
      <div class="card h-100" style="border-radius: 12px;">
        <div class="card-header" style="background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%); border-radius: 12px 12px 0 0;">
          <h3 class="card-title text-white mb-0"><i class="fe fe-pie-chart mr-2"></i>Campaign Status Overview</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="d-flex align-items-center mb-4">
                <div class="mr-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(40, 167, 69, 0.1);">
                    <i class="fe fe-play text-success" style="font-size: 20px;"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="font-weight-bold">Running</span>
                    <span class="text-success font-weight-bold"><?php echo $stats->running_campaigns; ?></span>
                  </div>
                  <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->running_campaigns / $stats->total_campaigns) * 100) : 0; ?>%; border-radius: 4px;"></div>
                  </div>
                </div>
              </div>
              
              <div class="d-flex align-items-center mb-4">
                <div class="mr-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(23, 162, 184, 0.1);">
                    <i class="fe fe-check-circle text-info" style="font-size: 20px;"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="font-weight-bold">Completed</span>
                    <span class="text-info font-weight-bold"><?php echo $stats->completed_campaigns; ?></span>
                  </div>
                  <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar bg-info" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->completed_campaigns / $stats->total_campaigns) * 100) : 0; ?>%; border-radius: 4px;"></div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="d-flex align-items-center mb-4">
                <div class="mr-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255, 193, 7, 0.1);">
                    <i class="fe fe-pause text-warning" style="font-size: 20px;"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="font-weight-bold">Paused</span>
                    <span class="text-warning font-weight-bold"><?php echo $stats->paused_campaigns; ?></span>
                  </div>
                  <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar bg-warning" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->paused_campaigns / $stats->total_campaigns) * 100) : 0; ?>%; border-radius: 4px;"></div>
                  </div>
                </div>
              </div>
              
              <div class="d-flex align-items-center mb-4">
                <div class="mr-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(108, 117, 125, 0.1);">
                    <i class="fe fe-inbox text-secondary" style="font-size: 20px;"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="font-weight-bold">Pending</span>
                    <span class="text-secondary font-weight-bold"><?php echo $stats->pending_campaigns; ?></span>
                  </div>
                  <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar bg-secondary" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->pending_campaigns / $stats->total_campaigns) * 100) : 0; ?>%; border-radius: 4px;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="text-center pt-3 border-top">
            <h1 class="display-4 mb-0 text-primary"><?php echo $stats->total_campaigns; ?></h1>
            <p class="text-muted mb-0">Total Campaigns</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4 mb-3">
      <div class="card h-100" style="border-radius: 12px;">
        <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 12px 12px 0 0;">
          <h3 class="card-title text-white mb-0"><i class="fe fe-activity mr-2"></i>Performance</h3>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
              <div class="d-flex align-items-center">
                <i class="fe fe-layers text-primary mr-2"></i>
                <span>Total Campaigns</span>
              </div>
              <span class="badge badge-primary badge-pill px-3 py-2"><?php echo $stats->total_campaigns; ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
              <div class="d-flex align-items-center">
                <i class="fe fe-zap text-success mr-2"></i>
                <span>Active Now</span>
              </div>
              <span class="badge badge-success badge-pill px-3 py-2"><?php echo $stats->running_campaigns; ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
              <div class="d-flex align-items-center">
                <i class="fe fe-send text-info mr-2"></i>
                <span>Emails Sent</span>
              </div>
              <span class="badge badge-info badge-pill px-3 py-2"><?php echo number_format($stats->total_sent); ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
              <div class="d-flex align-items-center">
                <i class="fe fe-trending-up text-success mr-2"></i>
                <span>Success Rate</span>
              </div>
              <span class="badge badge-success badge-pill px-3 py-2"><?php echo $stats->total_emails > 0 ? round((($stats->total_sent - $stats->total_failed) / $stats->total_emails) * 100, 1) : 0; ?>%</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
              <div class="d-flex align-items-center">
                <i class="fe fe-eye text-info mr-2"></i>
                <span>Open Rate</span>
              </div>
              <span class="badge badge-info badge-pill px-3 py-2"><?php echo $stats->open_rate; ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <?php if(!empty($recent_logs)){ ?>
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card" style="border-radius: 12px;">
        <div class="card-header" style="background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%); border-radius: 12px 12px 0 0;">
          <h3 class="card-title text-white mb-0"><i class="fe fe-activity mr-2"></i>Recent Activity</h3>
          <div class="card-options">
            <a href="<?php echo cn($module . '/reports'); ?>" class="btn btn-sm btn-light">View All</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background: #f8f9fa;">
              <tr>
                <th class="border-top-0">Campaign</th>
                <th class="border-top-0">Email</th>
                <th class="border-top-0">Subject</th>
                <th class="border-top-0">Status</th>
                <th class="border-top-0">Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($recent_logs as $log){ 
                $status_badge = 'secondary';
                $status_icon = 'fe-clock';
                switch($log->status){
                  case 'sent': $status_badge = 'success'; $status_icon = 'fe-check'; break;
                  case 'opened': $status_badge = 'info'; $status_icon = 'fe-eye'; break;
                  case 'failed': $status_badge = 'danger'; $status_icon = 'fe-x'; break;
                }
              ?>
              <tr class="activity-item">
                <td>
                  <span class="font-weight-bold"><?php echo htmlspecialchars($log->campaign_name); ?></span>
                </td>
                <td>
                  <span class="text-muted"><?php echo htmlspecialchars($log->email); ?></span>
                </td>
                <td>
                  <span class="text-truncate d-inline-block" style="max-width: 200px;"><?php echo htmlspecialchars($log->subject); ?></span>
                </td>
                <td>
                  <span class="badge badge-<?php echo $status_badge; ?> d-flex align-items-center" style="width: fit-content;">
                    <i class="fe <?php echo $status_icon; ?> mr-1" style="font-size: 10px;"></i>
                    <?php echo ucfirst($log->status); ?>
                  </span>
                </td>
                <td>
                  <small class="text-muted"><?php echo date('M d, H:i', strtotime($log->created_at)); ?></small>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>

  <!-- Getting Started Guide -->
  <div class="row">
    <div class="col-md-12">
      <div class="card info-card" style="border-radius: 12px;">
        <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%); border-radius: 12px 12px 0 0;">
          <h3 class="card-title text-white mb-0"><i class="fe fe-book-open mr-2"></i>Getting Started Guide</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h5 class="mb-3"><i class="fe fe-list text-primary mr-2"></i>Quick Start Steps</h5>
              <div class="d-flex mb-3">
                <div class="mr-3">
                  <span class="badge badge-primary badge-pill" style="width: 28px; height: 28px; line-height: 20px;">1</span>
                </div>
                <div>
                  <strong>Configure SMTP</strong>
                  <p class="text-muted mb-0 small">Add at least one SMTP server in <a href="<?php echo cn($module . '/smtp'); ?>">SMTP Config</a></p>
                </div>
              </div>
              <div class="d-flex mb-3">
                <div class="mr-3">
                  <span class="badge badge-primary badge-pill" style="width: 28px; height: 28px; line-height: 20px;">2</span>
                </div>
                <div>
                  <strong>Create Template</strong>
                  <p class="text-muted mb-0 small">Design your email in <a href="<?php echo cn($module . '/templates'); ?>">Templates</a></p>
                </div>
              </div>
              <div class="d-flex mb-3">
                <div class="mr-3">
                  <span class="badge badge-primary badge-pill" style="width: 28px; height: 28px; line-height: 20px;">3</span>
                </div>
                <div>
                  <strong>Create Campaign</strong>
                  <p class="text-muted mb-0 small">Set up your campaign in <a href="<?php echo cn($module . '/campaigns'); ?>">Campaigns</a></p>
                </div>
              </div>
              <div class="d-flex mb-3">
                <div class="mr-3">
                  <span class="badge badge-primary badge-pill" style="width: 28px; height: 28px; line-height: 20px;">4</span>
                </div>
                <div>
                  <strong>Add Recipients</strong>
                  <p class="text-muted mb-0 small">Import users or upload CSV file</p>
                </div>
              </div>
              <div class="d-flex">
                <div class="mr-3">
                  <span class="badge badge-success badge-pill" style="width: 28px; height: 28px; line-height: 20px;">5</span>
                </div>
                <div>
                  <strong>Start Sending!</strong>
                  <p class="text-muted mb-0 small">Click "Start Sending" to begin</p>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <h5 class="mb-3"><i class="fe fe-clock text-primary mr-2"></i>Cron Setup</h5>
              
              <div class="alert alert-info mb-3" style="border-radius: 8px;">
                <strong><i class="fe fe-globe mr-1"></i> Process All Campaigns:</strong>
                <code class="d-block mt-2 small" style="word-break: break-all;">* * * * * curl "<?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN')); ?>"</code>
              </div>
              
              <div class="alert alert-success mb-3" style="border-radius: 8px;">
                <strong><i class="fe fe-target mr-1"></i> Campaign-Specific (Recommended):</strong>
                <code class="d-block mt-2 small" style="word-break: break-all;">* * * * * curl "<?php echo base_url('cron/email_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID'); ?>"</code>
              </div>
              
              <h6 class="mt-4 mb-2"><i class="fe fe-code text-primary mr-2"></i>Template Variables</h6>
              <div class="row">
                <div class="col-6">
                  <ul class="list-unstyled small mb-0">
                    <li><code>{username}</code> - User's name</li>
                    <li><code>{email}</code> - User's email</li>
                    <li><code>{balance}</code> - User's balance</li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled small mb-0">
                    <li><code>{site_name}</code> - Website name</li>
                    <li><code>{site_url}</code> - Website URL</li>
                    <li><code>{current_date}</code> - Today's date</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
