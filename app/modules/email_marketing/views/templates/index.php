<!-- Enhanced Templates Page -->
<style>
.templates-page .page-header-enhanced {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  color: white;
}
.templates-page .template-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.templates-page .template-row:hover {
  background: rgba(40, 167, 69, 0.05);
}
.templates-page .action-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 2px;
  transition: all 0.2s ease;
}
.templates-page .action-btn:hover {
  transform: scale(1.1);
}
</style>

<div class="templates-page">
  <!-- Enhanced Header -->
  <div class="page-header-enhanced">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h2 class="mb-1 text-white">
          <i class="fe fe-file-text mr-2"></i>Email Templates
        </h2>
        <p class="mb-0 text-white-50">Design and manage your email templates with HTML support</p>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?php echo cn($module); ?>" class="btn btn-light btn-sm">
          <i class="fe fe-arrow-left mr-1"></i> Dashboard
        </a>
        <a href="<?php echo cn($module . '/template_create'); ?>" class="btn btn-warning ajaxModal">
          <i class="fe fe-plus mr-1"></i> New Template
        </a>
      </div>
    </div>
  </div>

  <div class="row" id="result_ajaxSearch">
    <?php if(!empty($templates)){ ?>
    <div class="col-md-12">
      <div class="card template-card">
        <div class="card-header" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
          <h3 class="card-title mb-0"><i class="fe fe-layers mr-2 text-success"></i>Template Library</h3>
          <div class="card-options">
            <span class="badge badge-success"><?php echo $total; ?> templates</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background: #f1f3f5;">
              <tr>
                <th class="border-top-0" style="width: 50px;">No.</th>
                <th class="border-top-0">Template Name</th>
                <th class="border-top-0">Subject</th>
                <th class="border-top-0">Description</th>
                <th class="border-top-0">Created</th>
                <th class="border-top-0 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $i = ($page - 1) * $per_page;
              foreach ($templates as $template) {
                $i++;
              ?>
              <tr class="tr_<?php echo $template->id; ?> template-row">
                <td class="font-weight-bold text-muted"><?php echo $i; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                      <i class="fe fe-file-text text-white"></i>
                    </div>
                    <strong><?php echo htmlspecialchars($template->name); ?></strong>
                  </div>
                </td>
                <td>
                  <span class="text-truncate d-inline-block" style="max-width: 250px;">
                    <?php echo htmlspecialchars($template->subject); ?>
                  </span>
                </td>
                <td>
                  <small class="text-muted">
                    <?php echo htmlspecialchars(substr($template->description ?: 'No description', 0, 50)); ?>
                    <?php echo strlen($template->description) > 50 ? '...' : ''; ?>
                  </small>
                </td>
                <td>
                  <small><i class="fe fe-calendar text-muted mr-1"></i><?php echo date('M d, Y', strtotime($template->created_at)); ?></small>
                </td>
                <td class="text-center">
                  <a href="<?php echo cn($module . '/template_edit/' . $template->ids); ?>" 
                    class="action-btn btn btn-outline-info btn-sm ajaxModal" 
                    data-toggle="tooltip" 
                    title="Edit Template">
                    <i class="fe fe-edit-2"></i>
                  </a>
                  <a href="javascript:void(0)" 
                    class="action-btn btn btn-outline-danger btn-sm actionItem" 
                    data-id="<?php echo $template->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_template_delete'); ?>" 
                    data-toggle="tooltip" 
                    title="Delete" 
                    data-confirm="Delete this template?">
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
              <a class="page-link" href="<?php echo cn($module . '/templates/' . $p); ?>" style="border-radius: 8px; margin: 0 2px;"><?php echo $p; ?></a>
            </li>
            <?php } ?>
          </ul>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php } else { ?>
      <div class="col-md-12">
        <div class="card template-card text-center py-5">
          <div class="card-body">
            <div class="mb-4">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fe fe-file-text text-white" style="font-size: 40px;"></i>
              </div>
            </div>
            <h3 class="text-muted mb-3">No Templates Yet</h3>
            <p class="text-muted mb-4">Create your first email template to get started</p>
            <a href="<?php echo cn($module . '/template_create'); ?>" class="btn btn-success btn-lg ajaxModal">
              <i class="fe fe-plus mr-2"></i> Create Your First Template
            </a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  
  <!-- Template Variables Help -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card template-card">
        <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-code mr-2"></i>Available Template Variables</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <h6><i class="fe fe-user text-primary mr-2"></i>User Variables</h6>
              <ul class="list-unstyled small">
                <li><code>{username}</code> - User's name</li>
                <li><code>{email}</code> - User's email</li>
                <li><code>{balance}</code> - User's balance</li>
              </ul>
            </div>
            <div class="col-md-4">
              <h6><i class="fe fe-globe text-success mr-2"></i>Site Variables</h6>
              <ul class="list-unstyled small">
                <li><code>{site_name}</code> - Website name</li>
                <li><code>{site_url}</code> - Website URL</li>
              </ul>
            </div>
            <div class="col-md-4">
              <h6><i class="fe fe-calendar text-info mr-2"></i>Date Variables</h6>
              <ul class="list-unstyled small">
                <li><code>{current_date}</code> - Today's date</li>
                <li><code>{current_year}</code> - Current year</li>
              </ul>
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
