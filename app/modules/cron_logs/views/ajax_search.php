<?php if (!empty($cron_logs)) { ?>
<div class="col-md-12 col-xl-12">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><?=lang('Search_Results')?></h3>
      <div class="card-options">
        <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-bordered table-outline table-vcenter card-table">
        <thead>
          <tr>
            <th class="text-center w-1">
              <div class="custom-controls-stacked">
                <label class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input check-all" data-name="chk_1">
                  <span class="custom-control-label"></span>
                </label>
              </div>
            </th>
            <th class="text-center w-1"><?=lang('No_')?></th>
            <?php if (!empty($columns)) {
              foreach ($columns as $key => $row) { ?>
            <th><?=$row?></th>
            <?php }} ?>
            <th><?=lang('Response_Message')?></th>
            <?php if (get_role("admin")) { ?>
            <th class="text-center"><?=lang('Action')?></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($cron_logs)) {
            $i = 0;
            foreach ($cron_logs as $key => $row) {
            $i++;
          ?>
          <tr class="tr_<?=$row->id?>">
            <th class="text-center w-1">
              <div class="custom-controls-stacked">
                <label class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input chk_1" name="ids[]" value="<?=$row->id?>">
                  <span class="custom-control-label"></span>
                </label>
              </div>
            </th>
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
            <?php if (get_role("admin")) { ?>
            <td class="text-center">
              <a href="javascript:void(0)" 
                 class="btn btn-sm btn-outline-danger ajaxDeleteItem" 
                 data-id="<?=$row->id?>" 
                 data-url="<?=cn($module.'/ajax_delete_item/'.$row->id)?>">
                <i class="fe fe-trash"></i>
              </a>
            </td>
            <?php } ?>
          </tr>
          <?php }} ?>
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
        <h4 class="text-muted mt-3"><?=lang("No_results_found")?></h4>
      </div>
    </div>
  </div>
</div>
<?php } ?>
