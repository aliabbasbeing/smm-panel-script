<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?>
      </h1>
      <p class="text-muted">Manage your WhatsApp marketing campaigns, templates, and API configurations</p>
    </div>
  </div>
</div>

<!-- Overall Statistics Section -->
<div class="row">
  <div class="col-md-12">
    <h3 class="mb-3"><i class="fe fe-bar-chart-2"></i> Overall Performance</h3>
  </div>
</div>

<div class="row">
  <!-- Total Messages Sent -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Total Sent</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($stats->total_sent); ?></span>
            <small class="text-muted d-block">out of <?php echo number_format($stats->total_messages); ?></small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-send text-success mb-0"></span>
          </div>
        </div>
        <?php if($stats->total_messages > 0){ ?>
        <div class="progress progress-sm mt-2">
          <div class="progress-bar bg-success" style="width: <?php echo round(($stats->total_sent / $stats->total_messages) * 100); ?>%"></div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  
  <!-- Remaining Messages -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Remaining</h6>
            <span class="h2 mb-0 text-primary"><?php echo number_format($stats->total_remaining); ?></span>
            <small class="text-muted d-block">pending delivery</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-clock text-primary mb-0"></span>
          </div>
        </div>
        <?php if($stats->total_messages > 0){ ?>
        <div class="progress progress-sm mt-2">
          <div class="progress-bar bg-primary" style="width: <?php echo round(($stats->total_remaining / $stats->total_messages) * 100); ?>%"></div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  
  <!-- Failed Messages -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Failed</h6>
            <span class="h2 mb-0 text-danger"><?php echo number_format($stats->total_failed); ?></span>
            <small class="text-muted d-block"><?php echo $stats->failure_rate; ?>% failure rate</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
          </div>
        </div>
        <?php if($stats->total_messages > 0){ ?>
        <div class="progress progress-sm mt-2">
          <div class="progress-bar bg-danger" style="width: <?php echo round(($stats->total_failed / $stats->total_messages) * 100); ?>%"></div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  
  <!-- Delivered Messages -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Delivered</h6>
            <span class="h2 mb-0 text-info"><?php echo number_format($stats->total_delivered); ?></span>
            <small class="text-muted d-block"><?php echo $stats->delivery_rate; ?>% delivery rate</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-check-circle text-info mb-0"></span>
          </div>
        </div>
        <?php if($stats->total_sent > 0){ ?>
        <div class="progress progress-sm mt-2">
          <div class="progress-bar bg-info" style="width: <?php echo round(($stats->total_delivered / $stats->total_sent) * 100); ?>%"></div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<!-- Quick Access Cards -->
<div class="row mt-4">
  <div class="col-md-12">
    <h3 class="mb-3"><i class="fe fe-grid"></i> Quick Access</h3>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="row row-cards">
      
      <!-- Campaigns Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-blue mr-3">
              <i class="fe fe-message-square"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/campaigns'); ?>" class="text-inherit">Campaigns</a></h4>
              <small class="text-muted">Manage WhatsApp campaigns</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Templates Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-green mr-3">
              <i class="fe fe-file-text"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/templates'); ?>" class="text-inherit">Templates</a></h4>
              <small class="text-muted">Message templates</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- API Config Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-orange mr-3">
              <i class="fe fe-settings"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/api'); ?>" class="text-inherit">API Config</a></h4>
              <small class="text-muted">API settings</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Reports Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-purple mr-3">
              <i class="fe fe-bar-chart-2"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/reports'); ?>" class="text-inherit">Reports</a></h4>
              <small class="text-muted">Analytics & Reports</small>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

<!-- Campaign Statistics -->
<div class="row mt-4">
  <div class="col-md-12">
    <h3 class="mb-3"><i class="fe fe-message-square"></i> Campaign Overview</h3>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign Status Distribution</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <div class="clearfix">
                <div class="float-left">
                  <strong>Running Campaigns</strong>
                </div>
                <div class="float-right">
                  <small class="text-muted"><?php echo $stats->running_campaigns; ?></small>
                </div>
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-success" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->running_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
              </div>
            </div>
            
            <div class="mb-3">
              <div class="clearfix">
                <div class="float-left">
                  <strong>Completed Campaigns</strong>
                </div>
                <div class="float-right">
                  <small class="text-muted"><?php echo $stats->completed_campaigns; ?></small>
                </div>
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-info" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->completed_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="mb-3">
              <div class="clearfix">
                <div class="float-left">
                  <strong>Paused Campaigns</strong>
                </div>
                <div class="float-right">
                  <small class="text-muted"><?php echo $stats->paused_campaigns; ?></small>
                </div>
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-warning" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->paused_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
              </div>
            </div>
            
            <div class="mb-3">
              <div class="clearfix">
                <div class="float-left">
                  <strong>Pending Campaigns</strong>
                </div>
                <div class="float-right">
                  <small class="text-muted"><?php echo $stats->pending_campaigns; ?></small>
                </div>
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-secondary" style="width: <?php echo $stats->total_campaigns > 0 ? round(($stats->pending_campaigns / $stats->total_campaigns) * 100) : 0; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row mt-3">
          <div class="col-md-12 text-center">
            <h1 class="display-4 mb-0"><?php echo $stats->total_campaigns; ?></h1>
            <p class="text-muted">Total Campaigns</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Quick Stats</h3>
      </div>
      <div class="card-body">
        <div class="list-group list-group-flush">
          <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background-color: transparent !important;">
            <span>Total Campaigns</span>
            <span class="badge badge-primary badge-pill"><?php echo $stats->total_campaigns; ?></span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background-color: transparent !important;">
            <span>Active Campaigns</span>
            <span class="badge badge-success badge-pill"><?php echo $stats->running_campaigns; ?></span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background-color: transparent !important;">
            <span>Total Messages Sent</span>
            <span class="badge badge-info badge-pill"><?php echo number_format($stats->total_sent); ?></span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background-color: transparent !important;">
            <span>Success Rate</span>
            <span class="badge badge-success badge-pill"><?php echo $stats->total_messages > 0 ? round((($stats->total_sent - $stats->total_failed) / $stats->total_messages) * 100, 1) : 0; ?>%</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background-color: transparent !important;">
            <span>Delivery Rate</span>
            <span class="badge badge-info badge-pill"><?php echo $stats->delivery_rate; ?>%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<?php if(!empty($recent_logs)){ ?>
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fe fe-activity"></i> Recent Activity</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Campaign</th>
              <th>Phone Number</th>
              <th>Message</th>
              <th>Status</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recent_logs as $log){ 
              $status_badge = 'secondary';
              switch($log->status){
                case 'sent': $status_badge = 'success'; break;
                case 'delivered': $status_badge = 'info'; break;
                case 'read': $status_badge = 'primary'; break;
                case 'failed': $status_badge = 'danger'; break;
              }
            ?>
            <tr>
              <td><?php echo htmlspecialchars($log->campaign_name); ?></td>
              <td><?php echo htmlspecialchars($log->phone_number); ?></td>
              <td><?php echo htmlspecialchars(substr($log->message, 0, 40)); ?><?php echo strlen($log->message) > 40 ? '...' : ''; ?></td>
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($log->status); ?></span></td>
              <td><?php echo date('M d, H:i', strtotime($log->created_at)); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fe fe-info"></i> Getting Started</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h4>Quick Start Guide</h4>
            <ol class="mb-3">
              <li><strong>Configure API:</strong> Set up your WhatsApp Business API in <a href="<?php echo cn($module . '/api'); ?>">API Config</a></li>
              <li><strong>Create Template:</strong> Design your message template in <a href="<?php echo cn($module . '/templates'); ?>">Templates</a></li>
              <li><strong>Create Campaign:</strong> Set up a new campaign in <a href="<?php echo cn($module . '/campaigns'); ?>">Campaigns</a></li>
              <li><strong>Add Recipients:</strong> Import users or upload CSV file with phone numbers</li>
              <li><strong>Start Campaign:</strong> Click "Start Sending" to begin</li>
            </ol>
          </div>
          <div class="col-md-6">
            <h4>Cron Setup</h4>
            <p><strong>Option 1: Process All Running Campaigns</strong></p>
            <div class="alert alert-info">
              <code>* * * * * curl "<?php echo base_url('cron/whatsapp_marketing?token=' . get_option('whatsapp_cron_token', 'YOUR_TOKEN')); ?>"</code>
            </div>
            <p><small class="text-muted">This processes all running campaigns together.</small></p>
            
            <p class="mt-3"><strong>Option 2: Campaign-Specific Cron (Recommended)</strong></p>
            <div class="alert alert-success">
              <code>* * * * * curl "<?php echo base_url('cron/whatsapp_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID'); ?>"</code>
            </div>
            <p><small class="text-muted">Run separate cron jobs for each campaign to avoid interference. Get campaign-specific URL from campaign details page.</small></p>
            
            <h5 class="mt-3">Template Variables</h5>
            <p><small>Use these variables in your message templates:</small></p>
            <ul class="small">
              <li><code>{username}</code> - User's name</li>
              <li><code>{phone}</code> - User's phone number</li>
              <li><code>{balance}</code> - User's balance</li>
              <li><code>{site_name}</code> - Website name</li>
              <li><code>{site_url}</code> - Website URL</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>