<!-- Enhanced SMTP Configuration Page -->
<style>
.smtp-page .page-header-enhanced {
  background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  color: white;
}
.smtp-page .smtp-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.smtp-page .smtp-row:hover {
  background: rgba(253, 126, 20, 0.05);
}
.smtp-page .action-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 2px;
  transition: all 0.2s ease;
}
.smtp-page .action-btn:hover {
  transform: scale(1.1);
}
.smtp-page .encryption-badge {
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 11px;
  font-weight: 600;
}
</style>

<div class="smtp-page">
  <!-- Enhanced Header -->
  <div class="page-header-enhanced">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h2 class="mb-1 text-white">
          <i class="fe fe-server mr-2"></i>SMTP Configurations
        </h2>
        <p class="mb-0 text-white-50">Configure mail servers for sending emails with round-robin support</p>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?php echo cn($module); ?>" class="btn btn-light btn-sm">
          <i class="fe fe-arrow-left mr-1"></i> Dashboard
        </a>
        <a href="<?php echo cn($module . '/smtp_create'); ?>" class="btn btn-dark ajaxModal">
          <i class="fe fe-plus mr-1"></i> Add SMTP
        </a>
      </div>
    </div>
  </div>

  <div class="row" id="result_ajaxSearch">
    <?php if(!empty($smtp_configs)){ ?>
    <div class="col-md-12">
      <div class="card smtp-card">
        <div class="card-header" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
          <h3 class="card-title mb-0"><i class="fe fe-settings mr-2 text-warning"></i>SMTP Server List</h3>
          <div class="card-options">
            <span class="badge badge-warning"><?php echo $total; ?> servers</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background: #f1f3f5;">
              <tr>
                <th class="border-top-0" style="width: 50px;">No.</th>
                <th class="border-top-0">Name</th>
                <th class="border-top-0">Host</th>
                <th class="border-top-0">Port</th>
                <th class="border-top-0">Encryption</th>
                <th class="border-top-0">From Email</th>
                <th class="border-top-0">Default</th>
                <th class="border-top-0">Status</th>
                <th class="border-top-0 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $i = ($page - 1) * $per_page;
              foreach ($smtp_configs as $smtp) {
                $i++;
              ?>
              <tr class="tr_<?php echo $smtp->id; ?> smtp-row">
                <td class="font-weight-bold text-muted"><?php echo $i; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
                      <i class="fe fe-server text-white"></i>
                    </div>
                    <strong><?php echo htmlspecialchars($smtp->name); ?></strong>
                  </div>
                </td>
                <td>
                  <code style="background: #f8f9fa; padding: 3px 8px; border-radius: 4px;"><?php echo htmlspecialchars($smtp->host); ?></code>
                </td>
                <td>
                  <span class="badge badge-secondary"><?php echo $smtp->port; ?></span>
                </td>
                <td>
                  <?php 
                  $enc_class = 'secondary';
                  if($smtp->encryption == 'tls') $enc_class = 'info';
                  elseif($smtp->encryption == 'ssl') $enc_class = 'success';
                  ?>
                  <span class="encryption-badge badge badge-<?php echo $enc_class; ?>">
                    <i class="fe fe-lock mr-1"></i><?php echo strtoupper($smtp->encryption); ?>
                  </span>
                </td>
                <td>
                  <small><i class="fe fe-mail text-muted mr-1"></i><?php echo htmlspecialchars($smtp->from_email); ?></small>
                </td>
                <td>
                  <?php if($smtp->is_default){ ?>
                  <span class="badge badge-success" style="padding: 5px 10px; border-radius: 15px;">
                    <i class="fe fe-star mr-1"></i>Default
                  </span>
                  <?php } else { ?>
                  <span class="text-muted">-</span>
                  <?php } ?>
                </td>
                <td>
                  <?php if($smtp->status == 1){ ?>
                  <span class="badge badge-success" style="padding: 5px 10px; border-radius: 15px;">
                    <i class="fe fe-check-circle mr-1"></i>Active
                  </span>
                  <?php } else { ?>
                  <span class="badge badge-danger" style="padding: 5px 10px; border-radius: 15px;">
                    <i class="fe fe-x-circle mr-1"></i>Inactive
                  </span>
                  <?php } ?>
                </td>
                <td class="text-center">
                  <a href="<?php echo cn($module . '/smtp_edit/' . $smtp->ids); ?>" 
                    class="action-btn btn btn-outline-info btn-sm ajaxModal" 
                    data-toggle="tooltip" 
                    title="Edit SMTP">
                    <i class="fe fe-edit-2"></i>
                  </a>
                  <a href="javascript:void(0)" 
                    class="action-btn btn btn-outline-danger btn-sm actionItem" 
                    data-id="<?php echo $smtp->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_smtp_delete'); ?>" 
                    data-toggle="tooltip" 
                    title="Delete" 
                    data-confirm="Delete this SMTP configuration?">
                    <i class="fe fe-trash-2"></i>
                  </a>
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
              <a class="page-link" href="<?php echo cn($module . '/smtp/' . $p); ?>" style="border-radius: 8px; margin: 0 2px;"><?php echo $p; ?></a>
            </li>
            <?php } ?>
          </ul>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } else { ?>
      <div class="col-md-12">
        <div class="card smtp-card text-center py-5">
          <div class="card-body">
            <div class="mb-4">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
                <i class="fe fe-server text-white" style="font-size: 40px;"></i>
              </div>
            </div>
            <h3 class="text-muted mb-3">No SMTP Configurations</h3>
            <p class="text-muted mb-4">Add your first SMTP server to start sending emails</p>
            <a href="<?php echo cn($module . '/smtp_create'); ?>" class="btn btn-warning btn-lg ajaxModal">
              <i class="fe fe-plus mr-2"></i> Add Your First SMTP
            </a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  
  <!-- SMTP Information -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card smtp-card">
        <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-info mr-2"></i>SMTP Configuration Tips</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <h6><i class="fe fe-zap text-warning mr-2"></i>Multi-SMTP Support</h6>
              <p class="small text-muted">You can assign multiple SMTP servers to a campaign. Emails will be sent using round-robin rotation between servers.</p>
            </div>
            <div class="col-md-4">
              <h6><i class="fe fe-lock text-success mr-2"></i>Encryption</h6>
              <p class="small text-muted"><strong>TLS</strong> (port 587) is recommended. <strong>SSL</strong> (port 465) is also supported. Use <strong>None</strong> only for testing.</p>
            </div>
            <div class="col-md-4">
              <h6><i class="fe fe-star text-info mr-2"></i>Default Server</h6>
              <p class="small text-muted">Mark one SMTP as default. It will be pre-selected when creating new campaigns.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
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
