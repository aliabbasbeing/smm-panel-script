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

<!-- Cron List Table -->
<?php if (!empty($cron_list)) { ?>
<div class="row">
  <div class="col-md-12">
    <div class="card p-0">
      <div class="card-header">
        <h3 class="card-title"><?=lang('Cron_List')?></h3>
        <div class="card-options">
          <span class="badge badge-primary"><?=count($cron_list)?> Crons</span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
          <thead>
            <tr>
              <th><?=lang('No_')?></th>
              <th><?=lang("Cron_Name")?></th>
              <th><?=lang("Last_Run")?></th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = 0;
            foreach ($cron_list as $item) { 
              $i++;
            ?>
            <tr>
              <td class="text-center"><?=$i?></td>
              <td><strong><?=htmlspecialchars($item->cron_name)?></strong></td>
              <td><?=convert_timezone($item->executed_at, 'user')?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
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
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>