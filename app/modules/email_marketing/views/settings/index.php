<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-settings"></i> Email Marketing Settings
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Queue Metrics / Observability Section -->
<div class="row">
  <div class="col-md-12">
    <h3 class="mb-3"><i class="fe fe-activity"></i> System Metrics (Observability)</h3>
  </div>
</div>

<div class="row">
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Queue Size</h6>
            <span class="h2 mb-0 text-primary"><?php echo number_format($queue_metrics->queue_size); ?></span>
            <small class="text-muted d-block">pending emails</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-inbox text-primary mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Failed Emails</h6>
            <span class="h2 mb-0 text-danger"><?php echo number_format($queue_metrics->failed_count); ?></span>
            <small class="text-muted d-block">total failed</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Running Campaigns</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($queue_metrics->running_campaigns); ?></span>
            <small class="text-muted d-block">active now</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-play text-success mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Last Cron Run</h6>
            <span class="h5 mb-0"><?php echo $queue_metrics->last_cron_run; ?></span>
            <?php if($queue_metrics->last_cron_duration_sec > 0){ ?>
            <small class="text-muted d-block"><?php echo $queue_metrics->last_cron_duration_sec; ?>s duration</small>
            <?php } ?>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-clock text-info mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Last Cron Details -->
<?php if($queue_metrics->last_cron_run !== 'Never'){ ?>
<div class="row">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-terminal"></i> Last Cron Execution Details</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Sent</strong></div>
            <span class="h3 text-success"><?php echo $queue_metrics->last_cron_sent; ?></span>
          </div>
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Failed</strong></div>
            <span class="h3 text-danger"><?php echo $queue_metrics->last_cron_failed; ?></span>
          </div>
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Rejected (Domain)</strong></div>
            <span class="h3 text-warning"><?php echo $queue_metrics->last_cron_rejected_domain; ?></span>
          </div>
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Duration</strong></div>
            <span class="h3 text-info"><?php echo $queue_metrics->last_cron_duration_sec; ?>s</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- Settings Form -->
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-sliders"></i> Email Settings</h3>
      </div>
      <div class="card-body">
        <form id="settingsForm" action="<?php echo cn($module . '/ajax_save_settings'); ?>" method="POST">
          
          <!-- Domain Filter -->
          <div class="form-group">
            <label><strong>Email Domain Filter</strong></label>
            <select class="form-control" name="domain_filter" id="domain_filter">
              <option value="gmail_only" <?php echo $settings['email_domain_filter'] == 'gmail_only' ? 'selected' : ''; ?>>Gmail Only (@gmail.com)</option>
              <option value="custom" <?php echo $settings['email_domain_filter'] == 'custom' ? 'selected' : ''; ?>>Custom Domains</option>
              <option value="disabled" <?php echo $settings['email_domain_filter'] == 'disabled' ? 'selected' : ''; ?>>Disabled (Allow All)</option>
            </select>
            <small class="text-muted">Choose which email domains are allowed to receive emails</small>
          </div>
          
          <!-- Custom Domains -->
          <div class="form-group" id="custom_domains_group" style="display: <?php echo $settings['email_domain_filter'] == 'custom' ? 'block' : 'none'; ?>;">
            <label><strong>Allowed Domains</strong></label>
            <input type="text" class="form-control" name="allowed_domains" id="allowed_domains" 
                   value="<?php echo htmlspecialchars($settings['email_allowed_domains']); ?>" 
                   placeholder="gmail.com, yahoo.com, outlook.com">
            <small class="text-muted">Comma-separated list of allowed domains (e.g., gmail.com, yahoo.com)</small>
          </div>
          
          <!-- Current Filter Status -->
          <div class="alert alert-info">
            <strong>Current Filter:</strong>
            <?php if($settings['email_domain_filter'] == 'gmail_only'){ ?>
              Only @gmail.com addresses are allowed
            <?php } elseif($settings['email_domain_filter'] == 'disabled'){ ?>
              All email domains are allowed
            <?php } else { ?>
              Custom domains: <?php echo htmlspecialchars($settings['email_allowed_domains']); ?>
            <?php } ?>
          </div>
          
          <!-- Open Tracking -->
          <div class="form-group">
            <label class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="enable_open_tracking" value="1" 
                     <?php echo $settings['enable_open_tracking'] == 1 ? 'checked' : ''; ?>>
              <span class="custom-control-label"><strong>Enable Open Tracking</strong></span>
            </label>
            <small class="text-muted d-block">Add tracking pixel to emails to track opens</small>
          </div>
          
          <hr>
          
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-save"></i> Save Settings
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Show/hide custom domains field
  $('#domain_filter').on('change', function(){
    if($(this).val() == 'custom'){
      $('#custom_domains_group').show();
    } else {
      $('#custom_domains_group').hide();
    }
  });
  
  // Handle form submission
  $('#settingsForm').on('submit', function(e){
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
      beforeSend: function(){
        $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fe fe-loader"></i> Saving...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          show_message(response.message, 'error');
          $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Save Settings');
        }
      },
      error: function(){
        show_message('An error occurred', 'error');
        $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Save Settings');
      }
    });
  });
});
</script>
