
<div class="page-header">
  <h1 class="page-title">
    <i class="fe fe-list"></i> <?=lang("Cron Execution Logs")?>
  </h1>
  <div class="page-header-actions">
    <a href="<?=cn('cron_logs/dashboard')?>" class="btn btn-sm btn-info">
      <i class="fe fe-bar-chart"></i> Dashboard
    </a>
  </div>
</div>

<!-- Filters -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Filters</h3>
      </div>
      <div class="card-body">
        <form method="get" action="<?=cn('cron_logs/index')?>">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Cron Name</label>
                <select name="cron_name" class="form-control">
                  <option value="">All Crons</option>
                  <?php if (!empty($cron_names)) {
                    foreach ($cron_names as $name) {
                  ?>
                    <option value="<?=htmlspecialchars($name)?>" <?=$filters['cron_name'] == $name ? 'selected' : ''?>>
                      <?=htmlspecialchars($name)?>
                    </option>
                  <?php }} ?>
                </select>
              </div>
            </div>
            
            <div class="col-md-2">
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="">All Status</option>
                  <option value="success" <?=$filters['status'] == 'success' ? 'selected' : ''?>>Success</option>
                  <option value="failed" <?=$filters['status'] == 'failed' ? 'selected' : ''?>>Failed</option>
                  <option value="rate_limited" <?=$filters['status'] == 'rate_limited' ? 'selected' : ''?>>Rate Limited</option>
                </select>
              </div>
            </div>
            
            <div class="col-md-2">
              <div class="form-group">
                <label>Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?=htmlspecialchars($filters['date_from'])?>">
              </div>
            </div>
            
            <div class="col-md-2">
              <div class="form-group">
                <label>Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?=htmlspecialchars($filters['date_to'])?>">
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary">
                    <i class="fe fe-search"></i> Filter
                  </button>
                  <a href="<?=cn('cron_logs/index')?>" class="btn btn-secondary">
                    <i class="fe fe-refresh-cw"></i> Reset
                  </a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Summary Stats -->
<div class="row">
  <div class="col-md-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h5 class="mb-0"><?=$summary->total_runs ?? 0?></h5>
          <small class="text-muted">Total Runs (7 days)</small>
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
          <h5 class="mb-0"><?=$summary->avg_execution_time ? number_format($summary->avg_execution_time, 3) . 's' : 'N/A'?></h5>
          <small class="text-muted">Avg Execution Time</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Logs Table -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Execution Logs (<?=$logs->total?> total)</h3>
        <div class="card-options">
          <button class="btn btn-sm btn-danger cleanup-logs">
            <i class="fe fe-trash-2"></i> Cleanup Old Logs
          </button>
        </div>
      </div>
      
      <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
          <thead>
            <tr>
              <th class="text-center w-1">ID</th>
              <th>Cron Name</th>
              <th class="text-center">Executed At</th>
              <th class="text-center">Status</th>
              <th class="text-center">Response Code</th>
              <th class="text-center">Execution Time</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($logs->data)) {
              foreach ($logs->data as $row) {
            ?>
            <tr>
              <td class="text-center"><?=$row->id?></td>
              <td>
                <small><?=htmlspecialchars($row->cron_name)?></small>
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
              <td class="text-center"><?=$row->response_code ?? 'N/A'?></td>
              <td class="text-center">
                <?=$row->execution_time ? number_format($row->execution_time, 3) . 's' : 'N/A'?>
              </td>
              <td class="text-center">
                <a href="<?=cn("cron_logs/view/{$row->id}")?>" class="btn btn-sm btn-info">
                  <i class="fe fe-eye"></i> View
                </a>
              </td>
            </tr>
            <?php }
            } else { ?>
            <tr>
              <td colspan="7" class="text-center">No logs found</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <?php if ($logs->pages > 1) { ?>
      <div class="card-footer">
        <div class="text-center">
          <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $logs->pages; $i++) {
              $params_copy = $filters;
              $params_copy['page'] = $i;
              $query_string = http_build_query($params_copy);
            ?>
            <li class="page-item <?=$i == $logs->page ? 'active' : ''?>">
              <a class="page-link" href="<?=cn('cron_logs/index')?>?<?=$query_string?>"><?=$i?></a>
            </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.cleanup-logs').click(function() {
    var days = prompt('Delete logs older than how many days?', '30');
    
    if (!days || isNaN(days) || days < 1) {
      return;
    }
    
    if (!confirm('Are you sure you want to delete logs older than ' + days + ' days?')) {
      return;
    }
    
    $.ajax({
      url: '<?=cn("cron_logs/cleanup")?>',
      type: 'POST',
      data: { days: days },
      dataType: 'json',
      success: function(response) {
        alert(response.message);
        location.reload();
      },
      error: function() {
        alert('Failed to cleanup logs.');
      }
    });
  });
});
</script>
