<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <a href="<?php echo cn($module . '/campaign_create'); ?>" class="ajaxModal">
          <span class="add-new" data-toggle="tooltip" data-placement="bottom" title="Add New Campaign">
            <i class="fa fa-plus-square text-primary" aria-hidden="true"></i>
          </span>
        </a>
        WhatsApp Campaigns
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row" id="result_ajaxSearch">
  <?php if(!empty($campaigns)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign List</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Campaign Name</th>
              <th>Template</th>
              <th>API Config</th>
              <th>Status</th>
              <th>Progress</th>
              <th>Statistics</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($campaigns as $campaign) {
              $i++;
              
              // Calculate progress
              $progress = 0;
              if($campaign->total_messages > 0){
                $progress = round(($campaign->sent_messages / $campaign->total_messages) * 100);
              }
              
              // Status badge
              $status_class = 'secondary';
              switch($campaign->status){
                case 'running':
                  $status_class = 'success';
                  break;
                case 'completed':
                  $status_class = 'info';
                  break;
                case 'paused':
                  $status_class = 'warning';
                  break;
                case 'cancelled':
                  $status_class = 'danger';
                  break;
              }
            ?>
            <tr class="tr_<?php echo $campaign->id; ?>">
              <td class="w-1"><?php echo $i; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($campaign->name); ?></strong>
                <br><small class="text-muted">Created: <?php echo date('M d, Y', strtotime($campaign->created_at)); ?></small>
              </td>
              <td><?php echo htmlspecialchars($campaign->template_name); ?></td>
              <td><?php echo htmlspecialchars($campaign->api_name); ?></td>
              <td>
                <span class="badge badge-<?php echo $status_class; ?>">
                  <?php echo ucfirst($campaign->status); ?>
                </span>
              </td>
              <td>
                <div class="clearfix">
                  <div class="float-left">
                    <strong><?php echo $progress; ?>%</strong>
                  </div>
                  <div class="float-right">
                    <small class="text-muted"><?php echo $campaign->sent_messages; ?> / <?php echo $campaign->total_messages; ?></small>
                  </div>
                </div>
                <div class="progress progress-sm">
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%" 
                    aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </td>
              <td>
                <small>
                  <i class="fe fe-check-circle text-success"></i> <?php echo $campaign->sent_messages; ?> sent<br>
                  <i class="fe fe-clock text-warning"></i> <?php echo $campaign->pending_messages; ?> pending<br>
                  <i class="fe fe-x-circle text-danger"></i> <?php echo $campaign->failed_messages; ?> failed
                </small>
              </td>
              <td>
                <div class="btn-group">
                  <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" 
                    class="btn btn-sm btn-icon" 
                    data-toggle="tooltip" 
                    title="View Details">
                    <i class="fe fe-eye"></i>
                  </a>
                  
                  <?php if($campaign->status == 'pending' || $campaign->status == 'paused'){ ?>
                  <a href="<?php echo cn($module . '/campaign_edit/' . $campaign->ids); ?>" 
                    class="btn btn-sm btn-icon ajaxModal" 
                    data-toggle="tooltip" 
                    title="Edit">
                    <i class="fe fe-edit"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status == 'pending'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-success actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_start'); ?>" 
                    data-toggle="tooltip" 
                    title="Start Campaign">
                    <i class="fe fe-play"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status == 'running'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-warning actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_pause'); ?>" 
                    data-toggle="tooltip" 
                    title="Pause Campaign">
                    <i class="fe fe-pause"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status == 'paused'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-success actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_resume'); ?>" 
                    data-toggle="tooltip" 
                    title="Resume Campaign">
                    <i class="fe fe-play"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->failed_messages > 0){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-warning actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_resend_failed'); ?>" 
                    data-toggle="tooltip" 
                    title="Resend Failed Messages (<?php echo $campaign->failed_messages; ?>)" 
                    data-confirm="Are you sure you want to resend <?php echo $campaign->failed_messages; ?> failed message(s)?">
                    <i class="fe fe-refresh-cw"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status != 'running'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-danger actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_delete'); ?>" 
                    data-toggle="tooltip" 
                    title="Delete" 
                    data-confirm="Are you sure you want to delete this campaign?">
                    <i class="fe fe-trash"></i>
                  </a>
                  <?php } ?>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <?php if($total > $per_page){ ?>
    <div class="card-footer">
      <ul class="pagination">
        <?php
        $total_pages = ceil($total / $per_page);
        for($p = 1; $p <= $total_pages; $p++){
          $active = ($p == $page) ? 'active' : '';
        ?>
        <li class="page-item <?php echo $active; ?>">
          <a class="page-link" href="<?php echo cn($module . '/campaigns/' . $p); ?>"><?php echo $p; ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
    <div class="col-md-12">
      <?php echo Modules::run("blocks/empty_data"); ?>
      <div class="text-center mt-3">
        <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-primary ajaxModal">
          <i class="fe fe-plus"></i> Create Your First Campaign
        </a>
      </div>
    </div>
  <?php } ?>
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
