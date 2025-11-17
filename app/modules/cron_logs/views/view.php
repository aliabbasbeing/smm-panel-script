
<div class="page-header">
  <h1 class="page-title">
    <i class="fe fe-file-text"></i> <?=lang("Cron Log Details")?>
  </h1>
  <div class="page-header-actions">
    <a href="<?=cn('cron_logs/index')?>" class="btn btn-sm btn-secondary">
      <i class="fe fe-arrow-left"></i> Back to Logs
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Log Entry #<?=$log->id?></h3>
        <div class="card-options">
          <?php if ($log->status == 'success') { ?>
            <span class="badge badge-success">Success</span>
          <?php } elseif ($log->status == 'failed') { ?>
            <span class="badge badge-danger">Failed</span>
          <?php } else { ?>
            <span class="badge badge-warning">Rate Limited</span>
          <?php } ?>
        </div>
      </div>
      
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th width="40%">Cron Name</th>
                  <td><?=htmlspecialchars($log->cron_name)?></td>
                </tr>
                <tr>
                  <th>Executed At</th>
                  <td><?=date('Y-m-d H:i:s', strtotime($log->executed_at))?></td>
                </tr>
                <tr>
                  <th>Status</th>
                  <td>
                    <?php if ($log->status == 'success') { ?>
                      <span class="badge badge-success">Success</span>
                    <?php } elseif ($log->status == 'failed') { ?>
                      <span class="badge badge-danger">Failed</span>
                    <?php } else { ?>
                      <span class="badge badge-warning">Rate Limited</span>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <th>Response Code</th>
                  <td><?=$log->response_code ?? 'N/A'?></td>
                </tr>
                <tr>
                  <th>Execution Time</th>
                  <td><?=$log->execution_time ? number_format($log->execution_time, 3) . ' seconds' : 'N/A'?></td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              <label><strong>Response Message</strong></label>
              <div class="border p-3" style="max-height: 300px; overflow-y: auto; background-color: #f8f9fa;">
                <?php if ($log->response_message) { ?>
                  <pre style="white-space: pre-wrap; word-wrap: break-word; margin: 0;"><?=htmlspecialchars($log->response_message)?></pre>
                <?php } else { ?>
                  <em class="text-muted">No message recorded</em>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card-footer">
        <button class="btn btn-info trigger-cron" data-url="<?=base_url($log->cron_name)?>">
          <i class="fe fe-play"></i> Re-run This Cron
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.trigger-cron').click(function() {
    var btn = $(this);
    var url = btn.data('url');
    
    if (!confirm('Are you sure you want to manually trigger this cron?\n\n' + url)) {
      return;
    }
    
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Running...');
    
    $.ajax({
      url: '<?=cn("cron_logs/trigger")?>',
      type: 'POST',
      data: { cron_url: url },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          alert('Cron executed successfully!\n\nResponse Code: ' + response.response_code);
          window.location.href = '<?=cn("cron_logs/index")?>';
        } else {
          alert('Cron execution failed:\n' + response.message);
        }
      },
      error: function() {
        alert('Failed to execute cron. Please check your connection.');
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="fe fe-play"></i> Re-run This Cron');
      }
    });
  });
});
</script>
