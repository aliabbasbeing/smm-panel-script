<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-message-square"></i> <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Campaigns
        </a>
        <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="btn btn-sm btn-success">
          <i class="fe fe-download"></i> Export Report
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Campaign Stats -->
<div class="row">
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Total Messages</h6>
            <span class="h2 mb-0"><?php echo number_format($campaign->total_messages); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-message-square text-muted mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Sent</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($campaign->sent_messages); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-check-circle text-success mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Pending</h6>
            <span class="h2 mb-0 text-warning"><?php echo number_format($campaign->total_messages - $campaign->sent_messages - $campaign->failed_messages); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-clock text-warning mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Failed</h6>
            <span class="h2 mb-0 text-danger"><?php echo number_format($campaign->failed_messages); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Campaign Info & Progress -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign Information</h3>
      </div>
      <div class="card-body">
        <table class="table table-sm">
          <tr>
            <td class="w-50"><strong>Status:</strong></td>
            <td>
              <?php
              $status_class = 'secondary';
              switch($campaign->status){
                case 'running': $status_class = 'success'; break;
                case 'completed': $status_class = 'info'; break;
                case 'paused': $status_class = 'warning'; break;
                case 'cancelled': $status_class = 'danger'; break;
              }
              ?>
              <span class="badge badge-<?php echo $status_class; ?>"><?php echo ucfirst($campaign->status); ?></span>
            </td>
          </tr>
          <tr>
            <td><strong>Template:</strong></td>
            <td><?php echo htmlspecialchars($campaign->template_name); ?></td>
          </tr>
          <tr>
            <td><strong>API Config:</strong></td>
            <td><?php echo htmlspecialchars($campaign->api_name); ?></td>
          </tr>
          <tr>
            <td><strong>Hourly Limit:</strong></td>
            <td><?php echo $campaign->sending_limit_hourly ?: 'No limit'; ?></td>
          </tr>
          <tr>
            <td><strong>Daily Limit:</strong></td>
            <td><?php echo $campaign->sending_limit_daily ?: 'No limit'; ?></td>
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
            <td><?php echo date('M d, Y H:i', strtotime($campaign->completed_at)); ?></td>
          </tr>
          <?php } ?>
          <?php if($campaign->last_sent_at){ ?>
          <tr>
            <td><strong>Last Sent:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->last_sent_at)); ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><strong>Campaign Cron URL:</strong></td>
            <td>
              <small class="text-muted">
                <code><?php echo base_url('whatsapp_cron/run?token=' . get_option('whatsapp_cron_token', 'YOUR_TOKEN') . '&campaign_id=' . $campaign->ids); ?></code>
              </small>
              <br>
              <small class="text-info">Use this URL for campaign-specific cron job</small>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Progress</h3>
      </div>
      <div class="card-body">
        <?php 
        $progress = 0;
        if($campaign->total_messages > 0){
          $progress = round(($campaign->sent_messages / $campaign->total_messages) * 100);
        }
        $remaining = $campaign->total_messages - $campaign->sent_messages - $campaign->failed_messages;
        ?>
        <div class="mb-3">
          <div class="clearfix mb-2">
            <div class="float-start"><strong><?php echo $progress; ?>% Complete</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo $campaign->sent_messages; ?> / <?php echo $campaign->total_messages; ?></small></div>
          </div>
          <div class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
          </div>
        </div>
        
        <div class="row text-center">
          <div class="col">
            <div class="text-muted">Remaining</div>
            <div class="h4"><?php echo number_format($remaining); ?></div>
          </div>
          <div class="col">
            <div class="text-muted">Success Rate</div>
            <div class="h4">
              <?php 
              if($campaign->sent_messages > 0){
                echo round((($campaign->sent_messages - $campaign->failed_messages) / $campaign->sent_messages) * 100, 1) . '%';
              } else {
                echo '0%';
              }
              ?>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" class="btn btn-primary btn-block">
            <i class="fe fe-users"></i> Manage Recipients
          </a>
          
          <?php if($campaign->failed_messages > 0){ ?>
          <button class="btn btn-warning btn-block mt-2 actionItem" data-id="<?php echo $campaign->ids; ?>" data-action="<?php echo cn($module . '/ajax_campaign_resend_failed'); ?>" data-confirm="Are you sure you want to resend <?php echo $campaign->failed_messages; ?> failed message(s)?">
            <i class="fe fe-refresh-cw"></i> Resend Failed Messages (<?php echo $campaign->failed_messages; ?>)
          </button>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Logs -->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recent Activity (Last 50)</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Phone Number</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Error Message</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($logs)){ 
              foreach($logs as $log){
                $status_badge = 'secondary';
                switch($log->status){
                  case 'sent': $status_badge = 'success'; break;
                  case 'failed': $status_badge = 'danger'; break;
                  case 'pending': $status_badge = 'warning'; break;
                }
            ?>
            <tr>
              <td><?php echo htmlspecialchars($log->phone_number); ?></td>
              <td>
                <span class="badge badge-<?php echo $status_badge; ?>">
                  <?php echo ucfirst($log->status); ?>
                </span>
              </td>
              <td>
                <?php echo $log->sent_at ? date('M d, Y H:i:s', strtotime($log->sent_at)) : '-'; ?>
              </td>
              <td>
                <?php echo $log->error_message ? htmlspecialchars($log->error_message) : '-'; ?>
              </td>
            </tr>
            <?php 
              }
            } else { ?>
            <tr>
              <td colspan="4" class="text-center text-muted">No activity logs yet</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Hidden CSRF Token Field -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

<script>
$(document).ready(function(){
  // Define toast notification helper
  function showToast(message, type) {
    if (typeof $.toast === 'function') {
      $.toast({
        heading: type == 'success' ? 'Success' : 'Error',
        text: message,
        position: 'top-right',
        loaderBg: type == 'success' ? '#5ba035' : '#c9302c',
        icon: type,
        hideAfter: 3500
      });
    } else if (typeof show_message === 'function') {
      show_message(message, type);
    } else {
      alert(message);
    }
  }
  
  // Handle action buttons
  $('.actionItem').on('click', function(e){
    e.preventDefault();
    var $this = $(this);
    var ids = $this.data('id');
    var action = $this.data('action');
    var confirm_msg = $this.data('confirm');
    
    if(confirm_msg && !confirm(confirm_msg)){
      return;
    }
    
    // Get CSRF token dynamically
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = $('input[name="' + csrfName + '"]').val();
    
    var postData = {ids: ids};
    postData[csrfName] = csrfHash;
    
    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'json',
      data: postData,
      success: function(response){
        if(response.status == 'success'){
          showToast(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          showToast(response.message, 'error');
        }
      },
      error: function(xhr, status, error){
        if(xhr.status == 403){
          showToast('Permission denied. Please refresh the page and try again.', 'error');
        } else {
          showToast('An error occurred: ' + error, 'error');
        }
      }
    });
  });
});
</script>
