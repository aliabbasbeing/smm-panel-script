
<div class="page-header">
  <h1 class="page-title">
    <i class="fe fe-clock"></i> <?=lang("Cron Logs Dashboard")?>
  </h1>
</div>

<div class="row">
  <!-- Summary Cards -->
  <div class="col-md-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h5 class="mb-0"><?=$summary->total_runs ?? 0?></h5>
          <small class="text-muted">Total Runs (<?=$days?>d)</small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h5 class="mb-0 text-success"><?=$summary->total_success ?? 0?></h5>
          <small class="text-muted">Successful</small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h5 class="mb-0 text-danger"><?=$summary->total_failed ?? 0?></h5>
          <small class="text-muted">Failed</small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h5 class="mb-0"><?=$summary->total_crons ?? 0?></h5>
          <small class="text-muted">Total Crons</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Cron Jobs Overview</h3>
        <div class="card-options">
          <a href="<?=cn('cron_logs/index')?>" class="btn btn-sm btn-primary">
            <i class="fe fe-list"></i> View All Logs
          </a>
        </div>
      </div>
      
      <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
          <thead>
            <tr>
              <th>Cron Name</th>
              <th class="text-center">Last Run</th>
              <th class="text-center">Status</th>
              <th class="text-center">Total Runs (<?=$days?>d)</th>
              <th class="text-center">Success Rate</th>
              <th class="text-center">Avg Time</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($last_runs)) {
              foreach ($last_runs as $row) {
                // Find stats for this cron
                $cron_stats = null;
                foreach ($stats as $stat) {
                  if ($stat->cron_name == $row->cron_name) {
                    $cron_stats = $stat;
                    break;
                  }
                }
                
                $success_rate = 0;
                if ($cron_stats && $cron_stats->total_runs > 0) {
                  $success_rate = ($cron_stats->success_count / $cron_stats->total_runs) * 100;
                }
            ?>
            <tr>
              <td>
                <strong><?=htmlspecialchars($row->cron_name)?></strong>
              </td>
              <td class="text-center">
                <small><?=date('Y-m-d H:i:s', strtotime($row->executed_at))?></small>
              </td>
              <td class="text-center">
                <?php if ($row->status == 'success') { ?>
                  <span class="badge badge-success">Success</span>
                <?php } elseif ($row->status == 'failed') { ?>
                  <span class="badge badge-danger">Failed</span>
                <?php } else { ?>
                  <span class="badge badge-warning">Rate Limited</span>
                <?php } ?>
              </td>
              <td class="text-center"><?=$cron_stats ? $cron_stats->total_runs : 1?></td>
              <td class="text-center">
                <div class="progress" style="height: 20px;">
                  <div class="progress-bar <?=$success_rate >= 80 ? 'bg-success' : ($success_rate >= 50 ? 'bg-warning' : 'bg-danger')?>" 
                       role="progressbar" 
                       style="width: <?=$success_rate?>%;" 
                       aria-valuenow="<?=$success_rate?>" 
                       aria-valuemin="0" 
                       aria-valuemax="100">
                    <?=number_format($success_rate, 1)?>%
                  </div>
                </div>
              </td>
              <td class="text-center">
                <?php if ($cron_stats && $cron_stats->avg_execution_time) { ?>
                  <?=number_format($cron_stats->avg_execution_time, 3)?>s
                <?php } else { ?>
                  N/A
                <?php } ?>
              </td>
              <td class="text-center">
                <button class="btn btn-sm btn-info trigger-cron" data-url="<?=base_url($row->cron_name)?>">
                  <i class="fe fe-play"></i> Trigger
                </button>
              </td>
            </tr>
            <?php }
            } else { ?>
            <tr>
              <td colspan="7" class="text-center">No cron executions found</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
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
          location.reload();
        } else {
          alert('Cron execution failed:\n' + response.message);
        }
      },
      error: function() {
        alert('Failed to execute cron. Please check your connection.');
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="fe fe-play"></i> Trigger');
      }
    });
  });
});
</script>
