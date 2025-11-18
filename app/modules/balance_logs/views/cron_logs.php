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
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?=lang("Filter_Cron_Logs")?></h3>
      </div>
      <div class="card-body">
        <form method="GET" action="<?=cn($module.'/cron_logs')?>">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label><?=lang("Cron_Name")?></label>
                <select name="cron_name" class="form-control">
                  <option value=""><?=lang("All_Crons")?></option>
                  <?php if (!empty($cron_names)) {
                    foreach ($cron_names as $cron) { ?>
                      <option value="<?=htmlspecialchars($cron->cron_name)?>" <?=($filter_cron == $cron->cron_name) ? 'selected' : ''?>>
                        <?=htmlspecialchars($cron->cron_name)?>
                      </option>
                    <?php }
                  } ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label><?=lang("Status")?></label>
                <select name="status" class="form-control">
                  <option value=""><?=lang("All_Status")?></option>
                  <option value="Success" <?=($filter_status == 'Success') ? 'selected' : ''?>><?=lang("Success")?></option>
                  <option value="Failed" <?=($filter_status == 'Failed') ? 'selected' : ''?>><?=lang("Failed")?></option>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label><?=lang("Date_From")?></label>
                <input type="date" name="date_from" class="form-control" value="<?=htmlspecialchars($filter_date_from)?>">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label><?=lang("Date_To")?></label>
                <input type="date" name="date_to" class="form-control" value="<?=htmlspecialchars($filter_date_to)?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary"><i class="fe fe-filter mr-2"></i><?=lang("Filter")?></button>
                <a href="<?=cn($module.'/cron_logs')?>" class="btn btn-secondary"><i class="fe fe-refresh-cw mr-2"></i><?=lang("Reset")?></a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Last Run Summary -->
<?php if (!empty($last_runs)) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?=lang('Last_Run_Summary')?></h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th><?=lang("Cron_Name")?></th>
                <th><?=lang("Last_Execution")?></th>
                <th><?=lang("Status")?></th>
                <th><?=lang("Execution_Time")?></th>
                <th><?=lang("Response_Code")?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($last_runs as $cron_name => $last_run) { ?>
              <tr>
                <td><strong><?=htmlspecialchars($cron_name)?></strong></td>
                <td><?=convert_timezone($last_run->executed_at, 'user')?></td>
                <td>
                  <span class="status-badge status-<?=strtolower($last_run->status)?>">
                    <?=$last_run->status?>
                  </span>
                </td>
                <td class="execution-time">
                  <?=$last_run->execution_time ? number_format($last_run->execution_time, 3) . 's' : 'N/A'?>
                </td>
                <td><?=$last_run->response_code ?? 'N/A'?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- Cron Logs Table -->
<div class="row">
  <?php if (!empty($cron_logs)) { ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?=lang('Cron_Execution_Logs')?></h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
          <thead>
            <tr>
              <th><?=lang('No_')?></th>
              <th><?=lang('Cron_Name')?></th>
              <th><?=lang('Executed_At')?></th>
              <th><?=lang('Status')?></th>
              <th><?=lang('Response_Code')?></th>
              <th><?=lang('Execution_Time')?></th>
              <th><?=lang('Response_Message')?></th>
            </tr>
          </thead>
          <tbody>
            <?php
              $i = 0;
              foreach ($cron_logs as $row) {
              $i++;
            ?>
            <tr>
              <td class="text-center"><?=$i?></td>
              <td><strong><?=htmlspecialchars($row->cron_name)?></strong></td>
              <td><?=convert_timezone($row->executed_at, 'user')?></td>
              <td>
                <span class="status-badge status-<?=strtolower($row->status)?>">
                  <?=$row->status?>
                </span>
              </td>
              <td><?=$row->response_code ?? 'N/A'?></td>
              <td class="execution-time">
                <?=$row->execution_time ? number_format($row->execution_time, 3) . 's' : 'N/A'?>
              </td>
              <td>
                <?php if ($row->response_message) { ?>
                  <small><?=htmlspecialchars(substr($row->response_message, 0, 100))?><?=(strlen($row->response_message) > 100) ? '...' : ''?></small>
                <?php } else { ?>
                  <small class="text-muted">-</small>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php } else { ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <i class="fe fe-info text-muted" style="font-size: 48px;"></i>
          <h4 class="text-muted mt-3"><?=lang("No_cron_logs_found")?></h4>
          <p class="text-muted"><?=lang("Cron_logs_will_appear_here_after_crons_are_executed")?></p>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<?php if (!empty($links)) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="float-right">
      <?=$links?>
    </div>
  </div>
</div>
<?php } ?>
