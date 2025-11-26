<style>
.status-badge {
  padding: 5px 10px;
  border-radius: 3px;
  font-size: 12px;
  font-weight: bold;
}
.status-success {
  background-color: #28a745;
  color: white;
}
.status-failed {
  background-color: #dc3545;
  color: white;
}
.execution-time {
  font-family: monospace;
  color: #6c757d;
}
</style>

<div class="page-header">
  <h1 class="page-title">
    <i class="fe fe-activity" aria-hidden="true"></i>
    <?=lang("Cron_Logs")?>
  </h1>
  <div class="page-options d-flex">
    <a href="<?=cn('balance_logs')?>" class="btn btn-secondary">
      <i class="fe fe-arrow-left mr-2"></i><?=lang("Back_to_Balance_Logs")?>
    </a>
  </div>
</div>

<!-- Filter Section -->
<div class="row">
  <div class="col-md-12">
    <div class="card p-0">
      <div class="card-header">
        <h3 class="card-title" style="color: #fff !important;"><?=lang("Filter_Cron_Logs")?></h3>
      </div>
      <div class="card-body">
        <form method="GET" action="<?=cn($module.'/view_execution_logs')?>">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label><?=lang("Search_Cron_Name")?></label>
                <input type="text" name="search" class="form-control" placeholder="Search cron name..." value="<?=htmlspecialchars($search_cron)?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label><?=lang("Status")?></label>
                <select name="status" class="form-control">
                  <option value=""><?=lang("All_Status")?></option>
                  <option value="Success" <?=($filter_status == 'Success') ? 'selected' : ''?>><?=lang("Success")?></option>
                  <option value="Failed" <?=($filter_status == 'Failed') ? 'selected' : ''?>><?=lang("Failed")?></option>
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary"><i class="fe fe-filter mr-2"></i><?=lang("Filter")?></button>
                <a href="<?=cn($module.'/view_execution_logs')?>" class="btn btn-secondary"><i class="fe fe-refresh-cw mr-2"></i><?=lang("Reset")?></a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Summary -->
<div class="row">
  <div class="col-md-3 col-sm-6">
    <div class="card p-0">
      <div class="card-body text-center">
        <div class="text-muted mb-2">
          <i class="fe fe-zap" style="font-size: 2rem;"></i>
        </div>
        <h3 class="mb-1"><?=$total_crons?></h3>
        <p class="text-muted mb-0"><?=lang("Active_Crons")?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="text-muted mb-2">
          <i class="fe fe-activity" style="font-size: 2rem;"></i>
        </div>
        <h3 class="mb-1"><?=$overall_stats->total_executions?></h3>
        <p class="text-muted mb-0"><?=lang("Total_Executions")?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card p-0">
      <div class="card-body text-center">
        <div class="text-success mb-2">
          <i class="fe fe-check-circle" style="font-size: 2rem;"></i>
        </div>
        <h3 class="mb-1"><?=$overall_stats->total_success?></h3>
        <p class="text-muted mb-0"><?=lang("Successful")?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card p-0">
      <div class="card-body text-center">
        <div class="text-danger mb-2">
          <i class="fe fe-x-circle" style="font-size: 2rem;"></i>
        </div>
        <h3 class="mb-1"><?=$overall_stats->total_failed?></h3>
        <p class="text-muted mb-0"><?=lang("Failed")?></p>
      </div>
    </div>
  </div>
</div>

<!-- Last Run Summary Table -->
<?php if (!empty($cron_summary)) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card p-0">
      <div class="card-header">
        <h3 class="card-title"><?=lang('Last_Run_Summary')?></h3>
        <div class="card-options">
          <span class="badge badge-primary"><?=count($cron_summary)?> Crons</span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
          <thead>
            <tr>
              <th><?=lang('No_')?></th>
              <th><?=lang("Cron_Name")?></th>
              <th><?=lang("Last_Execution")?></th>
              <th><?=lang("Status")?></th>
              <th><?=lang("Response_Code")?></th>
              <th><?=lang("Execution_Time")?></th>
              <th><?=lang("Total_Runs")?></th>
              <th><?=lang("Success_Rate")?></th>
              <th><?=lang("Avg_Time")?></th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = 0;
            foreach ($cron_summary as $item) { 
              $i++;
              $last_run = $item->last_run;
              $stats = $item->stats;
              $success_rate = $stats->total_runs > 0 ? round(($stats->success_count / $stats->total_runs) * 100) : 0;
            ?>
            <tr>
              <td class="text-center"><?=$i?></td>
              <td><strong><?=htmlspecialchars($item->cron_name)?></strong></td>
              <td><?=convert_timezone($last_run->executed_at, 'user')?></td>
              <td>
                <span class="status-badge status-<?=strtolower($last_run->status)?>">
                  <?=$last_run->status?>
                </span>
              </td>
              <td>
                <?php if ($last_run->response_code) { ?>
                  <span class="badge badge-<?=($last_run->response_code == 200) ? 'success' : 'danger'?>">
                    <?=$last_run->response_code?>
                  </span>
                <?php } else { ?>
                  <span class="text-muted">N/A</span>
                <?php } ?>
              </td>
              <td class="execution-time">
                <?=$last_run->execution_time ? number_format($last_run->execution_time, 3) . 's' : 'N/A'?>
              </td>
              <td class="text-center">
                <span class="badge badge-info"><?=$stats->total_runs?></span>
              </td>
              <td class="text-center">
                <?php 
                  $badge_class = $success_rate >= 90 ? 'success' : ($success_rate >= 70 ? 'warning' : 'danger');
                ?>
                <span class="badge badge-<?=$badge_class?>"><?=$success_rate?>%</span>
              </td>
              <td class="execution-time">
                <?=$stats->avg_time ? number_format($stats->avg_time, 2) . 's' : 'N/A'?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Detailed Information -->
<div class="row">
  <div class="col-md-12">
    <div class="card p-0">
      <div class="card-header">
        <h3 class="card-title"><?=lang('Detailed_Last_Execution_Info')?></h3>
      </div>
      <div class="card-body">
        <?php foreach ($cron_summary as $item) { 
          $last_run = $item->last_run;
          $stats = $item->stats;
        ?>
        <div class="mb-4 pb-3 border-bottom">
          <div class="row">
            <div class="col-md-12">
              <h5>
                <i class="fe fe-code text-primary mr-2"></i>
                <strong><?=htmlspecialchars($item->cron_name)?></strong>
                <span class="status-badge status-<?=strtolower($last_run->status)?> ml-2">
                  <?=$last_run->status?>
                </span>
              </h5>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-3">
              <small class="text-muted"><?=lang("Last_Execution")?></small>
              <p class="mb-0"><strong><?=convert_timezone($last_run->executed_at, 'user')?></strong></p>
            </div>
            <div class="col-md-2">
              <small class="text-muted"><?=lang("Execution_Time")?></small>
              <p class="mb-0 execution-time">
                <strong><?=$last_run->execution_time ? number_format($last_run->execution_time, 3) . 's' : 'N/A'?></strong>
              </p>
            </div>
            <div class="col-md-2">
              <small class="text-muted"><?=lang("Response_Code")?></small>
              <p class="mb-0">
                <?php if ($last_run->response_code) { ?>
                  <span class="badge badge-<?=($last_run->response_code == 200) ? 'success' : 'danger'?>">
                    <?=$last_run->response_code?>
                  </span>
                <?php } else { ?>
                  <span class="text-muted">N/A</span>
                <?php } ?>
              </p>
            </div>
            <div class="col-md-2">
              <small class="text-muted"><?=lang("Total_Runs")?></small>
              <p class="mb-0"><strong><?=$stats->total_runs?></strong></p>
            </div>
            <div class="col-md-3">
              <small class="text-muted"><?=lang("Success_Failed_Ratio")?></small>
              <p class="mb-0">
                <span class="text-success"><strong><?=$stats->success_count?></strong></span> / 
                <span class="text-danger"><strong><?=$stats->failed_count?></strong></span>
              </p>
            </div>
          </div>
          <?php if ($last_run->response_message) { ?>
          <div class="row mt-3">
            <div class="col-md-12">
              <small class="text-muted"><?=lang("Last_Response_Message")?></small>
              <div class="alert alert-<?=($last_run->status == 'Success') ? 'success' : 'danger'?> mt-2 mb-0">
                <small><?=htmlspecialchars($last_run->response_message)?></small>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php } else { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card p-0">
      <div class="card-body">
        <div class="text-center py-5">
          <i class="fe fe-info text-muted" style="font-size: 48px;"></i>
          <h4 class="text-muted mt-3"><?=lang("No_cron_logs_found")?></h4>
          <p class="text-muted"><?=lang("Cron_logs_will_appear_here_after_crons_are_executed")?></p>
          <?php if ($filter_status || $search_cron) { ?>
            <a href="<?=cn($module.'/view_execution_logs')?>" class="btn btn-primary mt-3">
              <i class="fe fe-refresh-cw mr-2"></i><?=lang("Clear_Filters")?>
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>