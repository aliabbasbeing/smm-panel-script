<!-- Enhanced Campaigns Page -->
<style>
.campaigns-page .page-header-enhanced {
  background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  color: white;
}
.campaigns-page .stat-mini {
  background: rgba(255,255,255,0.2);
  border-radius: 8px;
  padding: 10px 15px;
  text-align: center;
}
.campaigns-page .campaign-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}
.campaigns-page .campaign-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.campaigns-page .campaign-row:hover {
  background: rgba(70, 127, 207, 0.05);
}
.campaigns-page .progress-enhanced {
  height: 8px;
  border-radius: 4px;
  background: #e9ecef;
}
.campaigns-page .action-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 2px;
  transition: all 0.2s ease;
}
.campaigns-page .action-btn:hover {
  transform: scale(1.1);
}
.campaigns-page .status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 500;
  font-size: 12px;
}
.campaigns-page .gmail-warning {
  background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
  border-radius: 10px;
  border-left: 4px solid #ffc107;
  padding: 15px;
  margin-bottom: 20px;
}
</style>

<div class="campaigns-page">
  <!-- Enhanced Header -->
  <div class="page-header-enhanced">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h2 class="mb-1 text-white">
          <i class="fe fe-mail mr-2"></i>Email Campaigns
        </h2>
        <p class="mb-0 text-white-50">Create and manage your email marketing campaigns</p>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?php echo cn($module); ?>" class="btn btn-light btn-sm">
          <i class="fe fe-arrow-left mr-1"></i> Dashboard
        </a>
        <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-warning ajaxModal">
          <i class="fe fe-plus mr-1"></i> New Campaign
        </a>
      </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row mt-3">
      <div class="col-6 col-md-3 mb-2 mb-md-0">
        <div class="stat-mini">
          <div class="h4 mb-0 text-white"><?php echo $total; ?></div>
          <small class="text-white-50">Total</small>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-2 mb-md-0">
        <div class="stat-mini">
          <div class="h4 mb-0 text-white"><?php 
            $running = 0;
            if(!empty($campaigns)){
              foreach($campaigns as $c){ if($c->status == 'running') $running++; }
            }
            echo $running;
          ?></div>
          <small class="text-white-50">Running</small>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini">
          <div class="h4 mb-0 text-white"><?php 
            $completed = 0;
            if(!empty($campaigns)){
              foreach($campaigns as $c){ if($c->status == 'completed') $completed++; }
            }
            echo $completed;
          ?></div>
          <small class="text-white-50">Completed</small>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini">
          <div class="h4 mb-0 text-white"><?php 
            $pending = 0;
            if(!empty($campaigns)){
              foreach($campaigns as $c){ if($c->status == 'pending') $pending++; }
            }
            echo $pending;
          ?></div>
          <small class="text-white-50">Pending</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Gmail Notice -->
  <div class="gmail-warning">
    <div class="d-flex align-items-center">
      <i class="fe fe-alert-circle text-warning mr-3" style="font-size: 24px;"></i>
      <div>
        <strong>Gmail Only:</strong> Only <code>@gmail.com</code> email addresses are accepted. Non-Gmail addresses will be rejected automatically.
      </div>
    </div>
  </div>

  <div class="row" id="result_ajaxSearch">
    <?php if(!empty($campaigns)){ ?>
    <div class="col-md-12">
      <div class="card campaign-card">
        <div class="card-header" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
          <h3 class="card-title mb-0"><i class="fe fe-list mr-2 text-primary"></i>Campaign List</h3>
          <div class="card-options">
            <span class="badge badge-primary"><?php echo $total; ?> campaigns</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background: #f1f3f5;">
              <tr>
                <th class="border-top-0" style="width: 50px;">No.</th>
                <th class="border-top-0">Campaign</th>
                <th class="border-top-0">Template</th>
                <th class="border-top-0">SMTP</th>
                <th class="border-top-0">Status</th>
                <th class="border-top-0">Progress</th>
                <th class="border-top-0">Statistics</th>
                <th class="border-top-0 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $i = ($page - 1) * $per_page;
              foreach ($campaigns as $campaign) {
                $i++;
                
                // Calculate progress
                $progress = 0;
                if($campaign->total_emails > 0){
                  $progress = round(($campaign->sent_emails / $campaign->total_emails) * 100);
                }
                
                // Status badge with enhanced styling
                $status_class = 'secondary';
                $status_icon = 'fe-inbox';
                switch($campaign->status){
                  case 'running':
                    $status_class = 'success';
                    $status_icon = 'fe-play-circle';
                    break;
                  case 'completed':
                    $status_class = 'info';
                    $status_icon = 'fe-check-circle';
                    break;
                  case 'paused':
                    $status_class = 'warning';
                    $status_icon = 'fe-pause-circle';
                    break;
                  case 'cancelled':
                    $status_class = 'danger';
                    $status_icon = 'fe-x-circle';
                    break;
                }
              ?>
              <tr class="tr_<?php echo $campaign->id; ?> campaign-row">
                <td class="font-weight-bold text-muted"><?php echo $i; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);">
                      <i class="fe fe-mail text-white"></i>
                    </div>
                    <div>
                      <strong class="d-block"><?php echo htmlspecialchars($campaign->name); ?></strong>
                      <small class="text-muted"><i class="fe fe-calendar mr-1"></i><?php echo date('M d, Y', strtotime($campaign->created_at)); ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="d-flex align-items-center">
                    <i class="fe fe-file-text text-success mr-2"></i>
                    <?php echo htmlspecialchars($campaign->template_name); ?>
                  </span>
                </td>
                <td>
                  <span class="d-flex align-items-center">
                    <i class="fe fe-server text-warning mr-2"></i>
                    <?php echo htmlspecialchars($campaign->smtp_name); ?>
                  </span>
                </td>
                <td>
                  <span class="status-badge badge badge-<?php echo $status_class; ?>">
                    <i class="fe <?php echo $status_icon; ?> mr-1"></i>
                    <?php echo ucfirst($campaign->status); ?>
                  </span>
                </td>
                <td style="min-width: 150px;">
                  <div class="d-flex justify-content-between mb-1">
                    <small class="font-weight-bold"><?php echo $progress; ?>%</small>
                    <small class="text-muted"><?php echo $campaign->sent_emails; ?>/<?php echo $campaign->total_emails; ?></small>
                  </div>
                  <div class="progress progress-enhanced">
                    <div class="progress-bar bg-<?php echo $status_class; ?>" style="width: <?php echo $progress; ?>%; border-radius: 4px;"></div>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <small class="mb-1"><i class="fe fe-check-circle text-success mr-1"></i><span class="text-success font-weight-bold"><?php echo $campaign->sent_emails; ?></span> sent</small>
                    <small class="mb-1"><i class="fe fe-eye text-info mr-1"></i><span class="text-info font-weight-bold"><?php echo $campaign->opened_emails; ?></span> opened</small>
                    <small><i class="fe fe-x-circle text-danger mr-1"></i><span class="text-danger font-weight-bold"><?php echo $campaign->failed_emails; ?></span> failed</small>
                  </div>
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center flex-wrap">
                    <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" 
                      class="action-btn btn btn-outline-primary btn-sm" 
                      data-toggle="tooltip" 
                      title="View Details">
                      <i class="fe fe-eye"></i>
                    </a>
                    
                    <?php if($campaign->status == 'pending' || $campaign->status == 'paused'){ ?>
                    <a href="<?php echo cn($module . '/campaign_edit/' . $campaign->ids); ?>" 
                      class="action-btn btn btn-outline-info btn-sm ajaxModal" 
                      data-toggle="tooltip" 
                      title="Edit">
                      <i class="fe fe-edit-2"></i>
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->status == 'pending'){ ?>
                    <a href="javascript:void(0)" 
                      class="action-btn btn btn-success btn-sm actionItem" 
                      data-id="<?php echo $campaign->ids; ?>" 
                      data-action="<?php echo cn($module . '/ajax_campaign_start'); ?>" 
                      data-toggle="tooltip" 
                      title="Start Campaign">
                      <i class="fe fe-play"></i>
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->status == 'running'){ ?>
                    <a href="javascript:void(0)" 
                      class="action-btn btn btn-warning btn-sm actionItem" 
                      data-id="<?php echo $campaign->ids; ?>" 
                      data-action="<?php echo cn($module . '/ajax_campaign_pause'); ?>" 
                      data-toggle="tooltip" 
                      title="Pause">
                      <i class="fe fe-pause"></i>
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->status == 'paused'){ ?>
                    <a href="javascript:void(0)" 
                      class="action-btn btn btn-success btn-sm actionItem" 
                      data-id="<?php echo $campaign->ids; ?>" 
                      data-action="<?php echo cn($module . '/ajax_campaign_resume'); ?>" 
                      data-toggle="tooltip" 
                      title="Resume">
                      <i class="fe fe-play"></i>
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->failed_emails > 0){ ?>
                    <a href="javascript:void(0)" 
                      class="action-btn btn btn-outline-warning btn-sm actionItem" 
                      data-id="<?php echo $campaign->ids; ?>" 
                      data-action="<?php echo cn($module . '/ajax_campaign_resend_failed'); ?>" 
                      data-toggle="tooltip" 
                      title="Resend <?php echo $campaign->failed_emails; ?> Failed" 
                      data-confirm="Resend <?php echo $campaign->failed_emails; ?> failed emails?">
                      <i class="fe fe-refresh-cw"></i>
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->status != 'running'){ ?>
                    <a href="javascript:void(0)" 
                      class="action-btn btn btn-outline-danger btn-sm actionItem" 
                      data-id="<?php echo $campaign->ids; ?>" 
                      data-action="<?php echo cn($module . '/ajax_campaign_delete'); ?>" 
                      data-toggle="tooltip" 
                      title="Delete" 
                      data-confirm="Delete this campaign permanently?">
                      <i class="fe fe-trash-2"></i>
                    </a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        
        <?php if($total > $per_page){ ?>
        <div class="card-footer d-flex justify-content-center" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
          <ul class="pagination mb-0">
            <?php
            $total_pages = ceil($total / $per_page);
            for($p = 1; $p <= $total_pages; $p++){
              $active = ($p == $page) ? 'active' : '';
            ?>
            <li class="page-item <?php echo $active; ?>">
              <a class="page-link" href="<?php echo cn($module . '/campaigns/' . $p); ?>" style="border-radius: 8px; margin: 0 2px;"><?php echo $p; ?></a>
            </li>
            <?php } ?>
          </ul>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } else { ?>
      <div class="col-md-12">
        <div class="card campaign-card text-center py-5">
          <div class="card-body">
            <div class="mb-4">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);">
                <i class="fe fe-mail text-white" style="font-size: 40px;"></i>
              </div>
            </div>
            <h3 class="text-muted mb-3">No Campaigns Yet</h3>
            <p class="text-muted mb-4">Create your first email marketing campaign to get started</p>
            <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-primary btn-lg ajaxModal">
              <i class="fe fe-plus mr-2"></i> Create Your First Campaign
            </a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<script>
$(document).ready(function(){
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
    
    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'json',
      data: {ids: ids, csrf_test_name: $('input[name="csrf_test_name"]').val()},
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          show_message(response.message, 'error');
        }
      }
    });
  });
});
</script>
