<!-- Enhanced Campaign Details Page -->
<style>
.campaign-details .header-banner {
  background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);
  border-radius: 15px;
  padding: 25px;
  margin-bottom: 25px;
  color: white;
}
.campaign-details .stat-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: transform 0.2s ease;
}
.campaign-details .stat-card:hover {
  transform: translateY(-3px);
}
.campaign-details .stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}
.campaign-details .info-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.campaign-details .info-card .card-header {
  border-radius: 12px 12px 0 0;
}
.campaign-details .progress-xl {
  height: 20px;
  border-radius: 10px;
}
.campaign-details .filter-btn {
  border-radius: 20px;
  padding: 5px 15px;
  margin: 0 3px;
  font-size: 12px;
}
.campaign-details .log-row:hover {
  background: rgba(70, 127, 207, 0.05);
}
.campaign-details .smtp-badge {
  background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
  color: white;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 11px;
  font-weight: 500;
}
.campaign-details .cron-box {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 12px;
  font-family: monospace;
  font-size: 11px;
  word-break: break-all;
  border-left: 4px solid #17a2b8;
}
</style>

<div class="campaign-details">
  <!-- Header Banner -->
  <div class="header-banner">
    <div class="d-flex justify-content-between align-items-start flex-wrap">
      <div>
        <h2 class="mb-2 text-white">
          <i class="fe fe-mail mr-2"></i><?php echo htmlspecialchars($campaign->name); ?>
        </h2>
        <div class="d-flex align-items-center flex-wrap">
          <?php
          $status_class = 'secondary';
          $status_icon = 'fe-inbox';
          switch($campaign->status){
            case 'running': $status_class = 'success'; $status_icon = 'fe-play-circle'; break;
            case 'completed': $status_class = 'info'; $status_icon = 'fe-check-circle'; break;
            case 'paused': $status_class = 'warning'; $status_icon = 'fe-pause-circle'; break;
            case 'cancelled': $status_class = 'danger'; $status_icon = 'fe-x-circle'; break;
          }
          ?>
          <span class="badge badge-<?php echo $status_class; ?> mr-3" style="padding: 8px 16px; font-size: 14px; border-radius: 20px;">
            <i class="fe <?php echo $status_icon; ?> mr-1"></i><?php echo ucfirst($campaign->status); ?>
          </span>
          <span class="text-white-50"><i class="fe fe-calendar mr-1"></i>Created: <?php echo date('M d, Y', strtotime($campaign->created_at)); ?></span>
        </div>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-light btn-sm">
          <i class="fe fe-arrow-left mr-1"></i>Back
        </a>
        <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="btn btn-success btn-sm">
          <i class="fe fe-download mr-1"></i>Export
        </a>
        <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" class="btn btn-warning btn-sm">
          <i class="fe fe-users mr-1"></i>Recipients
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Total Emails</p>
              <h2 class="mb-0"><?php echo number_format($campaign->total_emails); ?></h2>
            </div>
            <div class="stat-icon" style="background: rgba(108, 117, 125, 0.1);">
              <i class="fe fe-mail text-secondary"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="border-left: 4px solid #28a745;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Sent</p>
              <h2 class="mb-0 text-success"><?php echo number_format($campaign->sent_emails); ?></h2>
            </div>
            <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1);">
              <i class="fe fe-check-circle text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="border-left: 4px solid #17a2b8;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Opened</p>
              <h2 class="mb-0 text-info"><?php echo number_format($campaign->opened_emails); ?></h2>
              <?php if($campaign->sent_emails > 0){ ?>
              <small class="text-muted"><?php echo round(($campaign->opened_emails / $campaign->sent_emails) * 100, 1); ?>% rate</small>
              <?php } ?>
            </div>
            <div class="stat-icon" style="background: rgba(23, 162, 184, 0.1);">
              <i class="fe fe-eye text-info"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card stat-card h-100" style="border-left: 4px solid #dc3545;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted text-uppercase small mb-1 font-weight-bold">Failed</p>
              <h2 class="mb-0 text-danger"><?php echo number_format($campaign->failed_emails); ?></h2>
            </div>
            <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1);">
              <i class="fe fe-x-circle text-danger"></i>
            </div>
          </div>
          <?php if($campaign->failed_emails > 0){ ?>
          <button class="btn btn-warning btn-sm btn-block mt-2 actionCampaignResendFailed" data-ids="<?php echo $campaign->ids; ?>">
            <i class="fe fe-refresh-cw mr-1"></i>Resend Failed
          </button>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

<!-- Campaign Info -->
<div class="row mb-4">
  <div class="col-md-6 mb-3">
    <div class="card info-card h-100">
      <div class="card-header" style="background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);">
        <h5 class="card-title text-white mb-0"><i class="fe fe-info mr-2"></i>Campaign Information</h5>
      </div>
      <div class="card-body">
        <table class="table table-sm mb-0">
          <tr>
            <td class="w-50 border-top-0"><strong>Status:</strong></td>
            <td class="border-top-0">
              <?php
              $status_class = 'secondary';
              switch($campaign->status){
                case 'running': $status_class = 'success'; break;
                case 'completed': $status_class = 'info'; break;
                case 'paused': $status_class = 'warning'; break;
                case 'cancelled': $status_class = 'danger'; break;
              }
              ?>
              <span class="badge badge-<?php echo $status_class; ?>" style="padding: 5px 12px; border-radius: 15px;"><?php echo ucfirst($campaign->status); ?></span>
            </td>
          </tr>
          <tr>
            <td><strong>Template:</strong></td>
            <td><i class="fe fe-file-text text-success mr-1"></i><?php echo htmlspecialchars($campaign->template_name); ?></td>
          </tr>
          <tr>
            <td><strong>SMTP Server(s):</strong></td>
            <td>
              <?php 
              // Get SMTP names from IDs
              $smtp_names = array();
              $selected_smtp_ids = array();
              
              // Parse SMTP IDs from JSON
              if(!empty($campaign->smtp_config_ids)){
                $selected_smtp_ids = json_decode($campaign->smtp_config_ids, true);
                if(!is_array($selected_smtp_ids)){
                  $selected_smtp_ids = array();
                }
              }
              
              // Fallback to single smtp_config_id if smtp_config_ids is empty
              if(empty($selected_smtp_ids) && !empty($campaign->smtp_config_id)){
                $selected_smtp_ids = array($campaign->smtp_config_id);
              }
              
              // Get SMTP names
              if(!empty($smtp_configs) && !empty($selected_smtp_ids)){
                foreach($smtp_configs as $smtp){
                  if(in_array($smtp->id, $selected_smtp_ids)){
                    $smtp_names[] = htmlspecialchars($smtp->name);
                  }
                }
              }
              
              if(!empty($smtp_names)){
                if(count($smtp_names) > 1){
                  echo '<span class="badge badge-info mr-1">Round-Robin</span>';
                }
                echo '<strong>' . implode(', ', $smtp_names) . '</strong>';
              } else {
                echo htmlspecialchars(isset($campaign->smtp_name) ? $campaign->smtp_name : 'Not set');
              }
              ?>
            </td>
          </tr>
          <tr>
            <td><strong>Hourly Limit:</strong></td>
            <td><?php echo $campaign->sending_limit_hourly ?: '<span class="text-muted">No limit</span>'; ?></td>
          </tr>
          <tr>
            <td><strong>Daily Limit:</strong></td>
            <td><?php echo $campaign->sending_limit_daily ?: '<span class="text-muted">No limit</span>'; ?></td>
          </tr>
          <tr>
            <td><strong>Created:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->created_at)); ?></td>
          </tr>
          <?php if($campaign->started_at){ ?>
          <tr>
            <td><strong>Started:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->started_at)); ?></td>
          </tr>
          <?php } ?>
          <?php if($campaign->completed_at){ ?>
          <tr>
            <td><strong>Completed:</strong></td>
            <td class="text-success"><?php echo date('M d, Y H:i', strtotime($campaign->completed_at)); ?></td>
          </tr>
          <?php } ?>
          <?php if($campaign->last_sent_at){ ?>
          <tr>
            <td><strong>Last Sent:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->last_sent_at)); ?></td>
          </tr>
          <?php } ?>
        </table>
        
        <div class="mt-3">
          <small class="text-muted d-block mb-2"><i class="fe fe-terminal mr-1"></i>Campaign Cron URL:</small>
          <div class="cron-box">
            <?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN') . '&campaign_id=' . $campaign->ids); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 mb-3">
    <div class="card info-card h-100">
      <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <h5 class="card-title text-white mb-0"><i class="fe fe-trending-up mr-2"></i>Progress & Health</h5>
      </div>
      <div class="card-body">
        <?php 
        $progress = 0;
        if($campaign->total_emails > 0){
          $progress = round(($campaign->sent_emails / $campaign->total_emails) * 100);
        }
        $remaining = $campaign->total_emails - $campaign->sent_emails;
        ?>
        
        <div class="text-center mb-3">
          <h1 class="display-4 mb-0 text-primary"><?php echo $progress; ?>%</h1>
          <p class="text-muted mb-0">Complete</p>
        </div>
        
        <div class="progress progress-xl mb-3">
          <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%;">
            <?php if($progress > 20) echo $campaign->sent_emails . ' sent'; ?>
          </div>
        </div>
        
        <div class="row text-center mb-3">
          <div class="col-4">
            <h4 class="mb-0 text-primary"><?php echo number_format($remaining); ?></h4>
            <small class="text-muted">Remaining</small>
          </div>
          <div class="col-4">
            <h4 class="mb-0 text-success"><?php echo $campaign->sent_emails > 0 ? round(($campaign->opened_emails / $campaign->sent_emails) * 100, 1) : 0; ?>%</h4>
            <small class="text-muted">Open Rate</small>
          </div>
          <div class="col-4">
            <h4 class="mb-0 text-danger"><?php echo $campaign->total_emails > 0 ? round(($campaign->failed_emails / $campaign->total_emails) * 100, 1) : 0; ?>%</h4>
            <small class="text-muted">Failure</small>
          </div>
        </div>
        
        <!-- Health Score -->
        <?php 
        $health_score = 100;
        $health_class = 'success';
        $health_issues = array();
        
        if($campaign->total_emails > 0){
          $failure_rate = ($campaign->failed_emails / $campaign->total_emails) * 100;
          if($failure_rate > 20){
            $health_score -= 30;
            $health_issues[] = 'High failure rate (' . round($failure_rate, 1) . '%)';
          } elseif($failure_rate > 10){
            $health_score -= 15;
            $health_issues[] = 'Moderate failure rate (' . round($failure_rate, 1) . '%)';
          }
        }
        
        if($campaign->sent_emails > 0){
          $open_rate = ($campaign->opened_emails / $campaign->sent_emails) * 100;
          if($open_rate < 10){
            $health_score -= 20;
            $health_issues[] = 'Low open rate (' . round($open_rate, 1) . '%)';
          } elseif($open_rate < 20){
            $health_score -= 10;
            $health_issues[] = 'Below average open rate (' . round($open_rate, 1) . '%)';
          }
        }
        
        if($campaign->status == 'running' && $campaign->last_sent_at){
          $hours_since_last = (strtotime(NOW) - strtotime($campaign->last_sent_at)) / 3600;
          if($hours_since_last > 24){
            $health_score -= 25;
            $health_issues[] = 'No emails sent in last 24 hours';
          }
        }
        
        if($health_score >= 80) $health_class = 'success';
        elseif($health_score >= 60) $health_class = 'warning';
        else $health_class = 'danger';
        ?>
        
        <div class="mt-3">
          <div class="d-flex justify-content-between mb-1">
            <strong>Health Score</strong>
            <span class="text-<?php echo $health_class; ?> font-weight-bold"><?php echo $health_score; ?>%</span>
          </div>
          <div class="progress" style="height: 25px; border-radius: 12px;">
            <div class="progress-bar bg-<?php echo $health_class; ?>" style="width: <?php echo $health_score; ?>%; border-radius: 12px;"></div>
          </div>
          <?php if(!empty($health_issues)){ ?>
          <small class="text-<?php echo $health_class; ?> d-block mt-2"><i class="fe fe-alert-triangle mr-1"></i><?php echo implode(', ', $health_issues); ?></small>
          <?php } else { ?>
          <small class="text-success d-block mt-2"><i class="fe fe-check-circle mr-1"></i>Campaign is performing well!</small>
          <?php } ?>
        </div>
        
        <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" class="btn btn-primary btn-block mt-3">
          <i class="fe fe-users mr-1"></i> Manage Recipients
        </a>
      </div>
    </div>
  </div>
</div>
  <!-- Recent Recipients -->
<div class="row mb-4">
  <div class="col-md-12">
    <div class="card info-card">
      <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%);">
        <h5 class="card-title text-white mb-0"><i class="fe fe-users mr-2"></i>Recipients (Last 100)</h5>
        <div>
          <button type="button" class="filter-btn btn btn-light btn-sm filter-recipients active" data-filter="all">All</button>
          <button type="button" class="filter-btn btn btn-outline-light btn-sm filter-recipients" data-filter="pending">Pending</button>
          <button type="button" class="filter-btn btn btn-outline-light btn-sm filter-recipients" data-filter="sent">Sent</button>
          <button type="button" class="filter-btn btn btn-outline-light btn-sm filter-recipients" data-filter="failed">Failed</button>
          <button type="button" class="filter-btn btn btn-outline-light btn-sm filter-recipients" data-filter="opened">Opened</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead style="background: #f8f9fa;">
            <tr>
              <th class="border-top-0">Email</th>
              <th class="border-top-0">Name</th>
              <th class="border-top-0">Status</th>
              <th class="border-top-0">Sent At</th>
              <th class="border-top-0">Opened At</th>
              <th class="border-top-0">Error</th>
              <th class="border-top-0 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="recipients-table-body">
            <?php if(!empty($recipients)){ 
              foreach($recipients as $recipient){
                $status_badge = 'secondary';
                $status_icon = 'fe-clock';
                switch($recipient->status){
                  case 'sent': $status_badge = 'success'; $status_icon = 'fe-check'; break;
                  case 'opened': $status_badge = 'info'; $status_icon = 'fe-eye'; break;
                  case 'failed': $status_badge = 'danger'; $status_icon = 'fe-x'; break;
                  case 'bounced': $status_badge = 'warning'; $status_icon = 'fe-alert-triangle'; break;
                }
            ?>
            <tr class="recipient-row log-row" data-status="<?php echo $recipient->status; ?>">
              <td><i class="fe fe-mail text-muted mr-2"></i><?php echo htmlspecialchars($recipient->email); ?></td>
              <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
              <td>
                <span class="badge badge-<?php echo $status_badge; ?>" style="padding: 5px 10px; border-radius: 15px;">
                  <i class="fe <?php echo $status_icon; ?> mr-1" style="font-size: 10px;"></i><?php echo ucfirst($recipient->status); ?>
                </span>
              </td>
              <td><?php echo $recipient->sent_at ? '<small>' . date('M d, H:i', strtotime($recipient->sent_at)) . '</small>' : '<span class="text-muted">-</span>'; ?></td>
              <td><?php echo $recipient->opened_at ? '<small>' . date('M d, H:i', strtotime($recipient->opened_at)) . '</small>' : '<span class="text-muted">-</span>'; ?></td>
              <td>
                <?php if($recipient->error_message){ ?>
                <span class="text-danger small" title="<?php echo htmlspecialchars($recipient->error_message); ?>">
                  <?php echo htmlspecialchars(substr($recipient->error_message, 0, 40)); ?>...
                </span>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td class="text-center">
                <?php if($recipient->status == 'failed'){ ?>
                <button class="btn btn-sm btn-warning actionResendSingleEmail" data-recipient-id="<?php echo $recipient->id; ?>" title="Resend">
                  <i class="fe fe-refresh-cw"></i>
                </button>
                <?php } ?>
              </td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="7" class="text-center py-4">
                <i class="fe fe-inbox text-muted" style="font-size: 40px;"></i>
                <p class="text-muted mt-2 mb-0">No recipients yet. <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>">Add recipients</a></p>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Filter recipients by status
  $('.filter-recipients').on('click', function(){
    var filter = $(this).data('filter');
    
    $('.filter-recipients').removeClass('active btn-light').addClass('btn-outline-light');
    $(this).removeClass('btn-outline-light').addClass('active btn-light');
    
    if(filter === 'all'){
      $('.recipient-row').show();
    } else {
      $('.recipient-row').hide();
      $('.recipient-row[data-status="' + filter + '"]').show();
    }
  });
});
</script>

<!-- Activity Logs -->
<div class="row mb-4">
  <div class="col-md-12">
    <div class="card info-card">
      <div class="card-header" style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
        <h5 class="card-title text-white mb-0"><i class="fe fe-activity mr-2"></i>Activity Log (Last 50)</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead style="background: #f8f9fa;">
            <tr>
              <th class="border-top-0">Email</th>
              <th class="border-top-0">Subject</th>
              <th class="border-top-0">SMTP Used</th>
              <th class="border-top-0">Status</th>
              <th class="border-top-0">Timestamp</th>
              <th class="border-top-0">Error</th>
              <th class="border-top-0 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($logs)){ 
              foreach($logs as $log){
                $status_badge = 'secondary';
                $status_icon = 'fe-clock';
                switch($log->status){
                  case 'sent': $status_badge = 'success'; $status_icon = 'fe-check'; break;
                  case 'opened': $status_badge = 'info'; $status_icon = 'fe-eye'; break;
                  case 'failed': $status_badge = 'danger'; $status_icon = 'fe-x'; break;
                }
                
                $smtp_name_used = '-';
                if(!empty($log->smtp_name)){
                  $smtp_name_used = htmlspecialchars($log->smtp_name);
                } elseif(!empty($log->smtp_config_id) && !empty($smtp_configs)){
                  foreach($smtp_configs as $smtp){
                    if($smtp->id == $log->smtp_config_id){
                      $smtp_name_used = htmlspecialchars($smtp->name);
                      break;
                    }
                  }
                }
            ?>
            <tr class="log-row">
              <td><i class="fe fe-mail text-muted mr-2"></i><?php echo htmlspecialchars($log->email); ?></td>
              <td class="text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($log->subject); ?></td>
              <td>
                <?php if($smtp_name_used != '-'){ ?>
                <span class="smtp-badge"><i class="fe fe-server mr-1"></i><?php echo $smtp_name_used; ?></span>
                <?php if(!empty($log->smtp_config_id)){ ?>
                <small class="text-muted d-block mt-1">ID: <?php echo $log->smtp_config_id; ?></small>
                <?php } ?>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td>
                <span class="badge badge-<?php echo $status_badge; ?>" style="padding: 5px 10px; border-radius: 15px;">
                  <i class="fe <?php echo $status_icon; ?> mr-1" style="font-size: 10px;"></i><?php echo ucfirst($log->status); ?>
                </span>
              </td>
              <td><small><?php echo date('M d, Y H:i:s', strtotime($log->created_at)); ?></small></td>
              <td>
                <?php if($log->error_message){ ?>
                <span class="text-danger small" title="<?php echo htmlspecialchars($log->error_message); ?>">
                  <?php echo htmlspecialchars(substr($log->error_message, 0, 35)); ?>...
                </span>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td class="text-center">
                <?php if($log->status == 'failed'){ ?>
                <button class="btn btn-sm btn-warning actionResendSingleEmail" data-recipient-id="<?php echo $log->recipient_id; ?>" title="Resend">
                  <i class="fe fe-refresh-cw"></i>
                </button>
                <?php } ?>
              </td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="7" class="text-center py-4">
                <i class="fe fe-activity text-muted" style="font-size: 40px;"></i>
                <p class="text-muted mt-2 mb-0">No activity logs yet</p>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div><!-- End campaign-details -->

<script>
$(document).ready(function(){
  // Handle resend failed emails for campaign
  $(document).on('click', '.actionCampaignResendFailed', function(e){
    e.preventDefault();
    var ids = $(this).data('ids');
    
    if(!confirm('Resend all failed emails for this campaign?')){
      return;
    }
    
    $.ajax({
      url: '<?php echo cn($module . '/ajax_campaign_resend_failed'); ?>',
      type: 'POST',
      dataType: 'JSON',
      data: { ids: ids },
      success: function(data){
        _notif({ message: data.message, type: data.status });
        if(data.status == 'success'){
          setTimeout(function(){ location.reload(); }, 1500);
        }
      }
    });
  });
  
  // Handle resend single email
  $(document).on('click', '.actionResendSingleEmail', function(e){
    e.preventDefault();
    var recipient_id = $(this).data('recipient-id');
    var $btn = $(this);
    
    if(!confirm('Resend this email?')){
      return;
    }
    
    $btn.prop('disabled', true);
    
    $.ajax({
      url: '<?php echo cn($module . '/ajax_resend_single_email'); ?>',
      type: 'POST',
      dataType: 'JSON',
      data: { recipient_id: recipient_id },
      success: function(data){
        $btn.prop('disabled', false);
        _notif({ message: data.message, type: data.status });
        if(data.status == 'success'){
          setTimeout(function(){ location.reload(); }, 1500);
        }
      },
      error: function(){
        $btn.prop('disabled', false);
        _notif({ message: 'An error occurred', type: 'error' });
      }
    });
  });
});
</script>
