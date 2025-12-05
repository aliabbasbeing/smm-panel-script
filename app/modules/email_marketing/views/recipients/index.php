<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-users"></i> Manage Recipients - <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Campaign
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Import Options Row 1: Database Imports -->
<div class="row">
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-database"></i> Import Active Users (With Orders)</h3>
      </div>
      <div class="card-body">
        <p>Import active users who have placed at least 1 order</p>
        <div class="alert alert-info mb-3">
          <small><strong>Note:</strong> Only users with order history will be imported to ensure better targeting.</small>
        </div>
        <form id="importUsersForm" action="<?php echo cn($module . '/ajax_import_from_users'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-download"></i> Import Active Users
          </button>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-users"></i> Import All Users (From Database)</h3>
      </div>
      <div class="card-body">
        <p>Import ALL registered users from the database</p>
        <div class="alert alert-warning mb-3">
          <small><strong>Note:</strong> This will import all users regardless of order history. Use for broader campaigns.</small>
        </div>
        <form id="importAllUsersForm" action="<?php echo cn($module . '/ajax_import_all_users'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <button type="submit" class="btn btn-warning">
            <i class="fe fe-download"></i> Import All Users
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Import Options Row 2: CSV and Manual Input -->
<div class="row">
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-upload"></i> Import from CSV/TXT File</h3>
      </div>
      <div class="card-body">
        <p>Upload a CSV file with email addresses (format: email,name)</p>
        <form id="importCSVForm" action="<?php echo cn($module . '/ajax_import_from_csv'); ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <div class="form-group">
            <input type="file" class="form-control" name="csv_file" accept=".csv,.txt" required>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-upload"></i> Upload & Import
          </button>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-plus-circle"></i> Add Manual Email Address</h3>
      </div>
      <div class="card-body">
        <p>Add individual email addresses one by one</p>
        <form id="addManualEmailForm" action="<?php echo cn($module . '/ajax_add_manual_email'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <div class="form-group">
            <label>Email Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" placeholder="example@email.com" required>
          </div>
          <div class="form-group">
            <label>Name (Optional)</label>
            <input type="text" class="form-control" name="name" placeholder="John Doe">
          </div>
          <button type="submit" class="btn btn-success">
            <i class="fe fe-plus"></i> Add Email
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Recipients List -->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Recipients List (Showing last 100)</h3>
        <div class="card-options">
          <span class="badge badge-primary">Total: <?php echo number_format($campaign->total_emails); ?></span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Priority</th>
              <th>Email</th>
              <th>Name</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Opened At</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($recipients)){ 
              foreach($recipients as $recipient){
                $status_badge = 'secondary';
                switch($recipient->status){
                  case 'sent': $status_badge = 'success'; break;
                  case 'opened': $status_badge = 'info'; break;
                  case 'failed': $status_badge = 'danger'; break;
                  case 'bounced': $status_badge = 'warning'; break;
                }
                // Determine priority label
                $priority_label = 'Imported';
                $priority_badge = 'secondary';
                if(isset($recipient->priority) && $recipient->priority == 1){
                  $priority_label = 'Manual';
                  $priority_badge = 'success';
                }
            ?>
            <tr>
              <td><span class="badge badge-<?php echo $priority_badge; ?>"><?php echo $priority_label; ?></span></td>
              <td><?php echo htmlspecialchars($recipient->email); ?></td>
              <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($recipient->status); ?></span></td>
              <td><?php echo $recipient->sent_at ? date('M d, H:i', strtotime($recipient->sent_at)) : '-'; ?></td>
              <td><?php echo $recipient->opened_at ? date('M d, H:i', strtotime($recipient->opened_at)) : '-'; ?></td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="6" class="text-center">
                <p class="text-muted">No recipients added yet. Import users or upload a CSV file above.</p>
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
  // Handle import from users (active users with orders)
  $('#importUsersForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    // Add CSRF token if it exists
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 60000, // 60 seconds timeout
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importing...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-download"></i> Import Active Users');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while importing users.';
        
        if (status === 'timeout') {
          errorMsg = 'Request timed out. The import may be taking too long. Please check if users have been imported or try again with fewer users.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
          try {
            var response = JSON.parse(xhr.responseText);
            if (response.message) {
              errorMsg = response.message;
            }
          } catch(e) {
            // Not JSON, show generic error
          }
        }
        
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-download"></i> Import Active Users');
      }
    });
  });
  
  // Handle import ALL users from database
  $('#importAllUsersForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    // Add CSRF token if it exists
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 120000, // 120 seconds timeout for larger imports
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importing...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-download"></i> Import All Users');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while importing users.';
        
        if (status === 'timeout') {
          errorMsg = 'Request timed out. The import may be taking too long. Please check if users have been imported.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-download"></i> Import All Users');
      }
    });
  });
  
  // Handle CSV import
  $('#importCSVForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = new FormData($form[0]);
    
    // Add CSRF token if it exists
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
      timeout: 60000, // 60 seconds timeout
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-upload"></i> Upload & Import');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while uploading CSV.';
        
        if (status === 'timeout') {
          errorMsg = 'Request timed out. The upload may be taking too long. Please try with a smaller file.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-upload"></i> Upload & Import');
      }
    });
  });
  
  // Handle manual email addition
  $('#addManualEmailForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    // Add CSRF token if it exists
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 30000, // 30 seconds timeout
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          // Clear form
          $form.find('input[name="email"]').val('');
          $form.find('input[name="name"]').val('');
          setTimeout(function(){
            location.reload();
          }, 500);
        } else {
          show_message(response.message, 'error');
          $form.find('button').prop('disabled', false).html('<i class="fe fe-plus"></i> Add Email');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while adding email.';
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-plus"></i> Add Email');
      }
    });
  });
});
</script>
