<!-- Enhanced Recipients Management Page -->
<style>
.recipients-page .page-header-enhanced {
  background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%);
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 25px;
  color: white;
}
.recipients-page .import-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 15px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  height: 100%;
}
.recipients-page .import-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.recipients-page .import-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
}
.recipients-page .recipient-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.recipients-page .recipient-row:hover {
  background: rgba(111, 66, 193, 0.05);
}
.recipients-page .priority-badge {
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 11px;
  font-weight: 600;
}
.recipients-page .gmail-info {
  background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
  border-radius: 10px;
  border-left: 4px solid #ffc107;
  padding: 15px;
  margin-bottom: 20px;
}
</style>

<div class="recipients-page">
  <!-- Enhanced Header -->
  <div class="page-header-enhanced">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h2 class="mb-1 text-white">
          <i class="fe fe-users mr-2"></i>Manage Recipients
        </h2>
        <p class="mb-0 text-white-50">
          Campaign: <strong><?php echo htmlspecialchars($campaign->name); ?></strong>
          <span class="mx-2">|</span>
          <span class="badge badge-light"><?php echo number_format($campaign->total_emails); ?> recipients</span>
        </p>
      </div>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" class="btn btn-light btn-sm">
          <i class="fe fe-arrow-left mr-1"></i> Back to Campaign
        </a>
      </div>
    </div>
  </div>

  <!-- Gmail Notice -->
  <div class="gmail-info">
    <div class="d-flex align-items-center">
      <i class="fe fe-alert-circle text-warning mr-3" style="font-size: 24px;"></i>
      <div>
        <strong>Important:</strong> Only <code>@gmail.com</code> email addresses will be processed. Non-Gmail addresses will be automatically rejected during sending.
      </div>
    </div>
  </div>

  <!-- Import Options -->
  <div class="row mb-4">
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card import-card text-center p-4">
        <div class="import-icon" style="background: linear-gradient(135deg, #467fcf 0%, #5c7cfa 100%);">
          <i class="fe fe-database text-white" style="font-size: 24px;"></i>
        </div>
        <h5 class="mb-2">Active Users</h5>
        <p class="text-muted small mb-3">Import users with order history</p>
        <form id="importUsersForm" action="<?php echo cn($module . '/ajax_import_from_users'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <button type="submit" class="btn btn-primary btn-block">
            <i class="fe fe-download mr-1"></i> Import
          </button>
        </form>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card import-card text-center p-4">
        <div class="import-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
          <i class="fe fe-users text-white" style="font-size: 24px;"></i>
        </div>
        <h5 class="mb-2">All Users</h5>
        <p class="text-muted small mb-3">Import all registered users</p>
        <form id="importAllUsersForm" action="<?php echo cn($module . '/ajax_import_all_users'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <button type="submit" class="btn btn-warning btn-block">
            <i class="fe fe-download mr-1"></i> Import All
          </button>
        </form>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card import-card text-center p-4">
        <div class="import-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
          <i class="fe fe-upload text-white" style="font-size: 24px;"></i>
        </div>
        <h5 class="mb-2">CSV Upload</h5>
        <p class="text-muted small mb-3">Upload email,name CSV file</p>
        <form id="importCSVForm" action="<?php echo cn($module . '/ajax_import_from_csv'); ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <div class="form-group mb-2">
            <input type="file" class="form-control form-control-sm" name="csv_file" accept=".csv,.txt" required style="font-size: 12px;">
          </div>
          <button type="submit" class="btn btn-success btn-block">
            <i class="fe fe-upload mr-1"></i> Upload
          </button>
        </form>
      </div>
    </div>
    
    <div class="col-lg-3 col-sm-6 mb-3">
      <div class="card import-card text-center p-4">
        <div class="import-icon" style="background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%);">
          <i class="fe fe-plus-circle text-white" style="font-size: 24px;"></i>
        </div>
        <h5 class="mb-2">Manual Add</h5>
        <p class="text-muted small mb-3">Add single email (high priority)</p>
        <form id="addManualEmailForm" action="<?php echo cn($module . '/ajax_add_manual_email'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <div class="form-group mb-2">
            <input type="email" class="form-control form-control-sm" name="email" placeholder="email@gmail.com" required>
          </div>
          <div class="form-group mb-2">
            <input type="text" class="form-control form-control-sm" name="name" placeholder="Name (optional)">
          </div>
          <button type="submit" class="btn btn-purple btn-block" style="background: #6f42c1; border-color: #6f42c1; color: white;">
            <i class="fe fe-plus mr-1"></i> Add
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Recipients List -->
  <div class="row">
    <div class="col-md-12">
      <div class="card recipient-card">
        <div class="card-header" style="background: linear-gradient(135deg, #6f42c1 0%, #9775fa 100%); border-radius: 12px 12px 0 0;">
          <h5 class="card-title text-white mb-0"><i class="fe fe-list mr-2"></i>Recipients List</h5>
          <div class="card-options">
            <span class="badge badge-light"><?php echo number_format($campaign->total_emails); ?> total</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background: #f1f3f5;">
              <tr>
                <th class="border-top-0">Priority</th>
                <th class="border-top-0">Email</th>
                <th class="border-top-0">Name</th>
                <th class="border-top-0">Status</th>
                <th class="border-top-0">Sent At</th>
                <th class="border-top-0">Opened At</th>
              </tr>
            </thead>
            <tbody>
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
                  $priority_label = 'Imported';
                  $priority_badge = 'secondary';
                  if(isset($recipient->priority) && $recipient->priority == 1){
                    $priority_label = 'Manual';
                    $priority_badge = 'success';
                  }
              ?>
              <tr class="recipient-row">
                <td>
                  <span class="priority-badge badge badge-<?php echo $priority_badge; ?>">
                    <i class="fe <?php echo $priority_badge == 'success' ? 'fe-star' : 'fe-layers'; ?> mr-1"></i><?php echo $priority_label; ?>
                  </span>
                </td>
                <td>
                  <i class="fe fe-mail text-muted mr-2"></i><?php echo htmlspecialchars($recipient->email); ?>
                </td>
                <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
                <td>
                  <span class="badge badge-<?php echo $status_badge; ?>" style="padding: 5px 10px; border-radius: 15px;">
                    <i class="fe <?php echo $status_icon; ?> mr-1" style="font-size: 10px;"></i><?php echo ucfirst($recipient->status); ?>
                  </span>
                </td>
                <td><?php echo $recipient->sent_at ? '<small>' . date('M d, H:i', strtotime($recipient->sent_at)) . '</small>' : '<span class="text-muted">-</span>'; ?></td>
                <td><?php echo $recipient->opened_at ? '<small>' . date('M d, H:i', strtotime($recipient->opened_at)) . '</small>' : '<span class="text-muted">-</span>'; ?></td>
              </tr>
              <?php }} else { ?>
              <tr>
                <td colspan="6" class="text-center py-5">
                  <div class="mb-3">
                    <i class="fe fe-inbox text-muted" style="font-size: 50px;"></i>
                  </div>
                  <h4 class="text-muted">No Recipients Yet</h4>
                  <p class="text-muted mb-0">Use the import options above to add recipients to this campaign</p>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer text-center text-muted" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
          <small><i class="fe fe-info mr-1"></i>Showing last 100 recipients. Total: <?php echo number_format($campaign->total_emails); ?></small>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Handle import from users (active users with orders)
  $('#importUsersForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 60000,
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){ location.reload(); }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-download mr-1"></i> Import');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = status === 'timeout' ? 'Request timed out' : 'An error occurred';
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-download mr-1"></i> Import');
      }
    });
  });
  
  // Handle import ALL users from database
  $('#importAllUsersForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 120000,
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){ location.reload(); }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-download mr-1"></i> Import All');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = status === 'timeout' ? 'Request timed out' : 'An error occurred';
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-download mr-1"></i> Import All');
      }
    });
  });
  
  // Handle CSV import
  $('#importCSVForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = new FormData($form[0]);
    
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.append('csrf_test_name', csrfToken);
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: formData,
      processData: false,
      contentType: false,
      timeout: 60000,
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){ location.reload(); }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-upload mr-1"></i> Upload');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = status === 'timeout' ? 'Request timed out' : 'An error occurred';
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-upload mr-1"></i> Upload');
      }
    });
  });
  
  // Handle manual email addition
  $('#addManualEmailForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 30000,
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          $form.find('input[name="email"]').val('');
          $form.find('input[name="name"]').val('');
          setTimeout(function(){ location.reload(); }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-plus mr-1"></i> Add');
        }
      },
      error: function(xhr, status, error){
        show_message('An error occurred', 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-plus mr-1"></i> Add');
      }
    });
  });
});
</script>
