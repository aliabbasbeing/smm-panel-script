<style>
/* Custom Styling for Balance Logs Card */
.balance-logs-card {
  background: #06141b;
  border: 1px solid #0d3242;
  border-radius: 14px;
  box-shadow: 0 8px 18px -8px rgba(0,0,0,.6), 0 2px 6px -2px rgba(0,0,0,.5);
  overflow: hidden;
}
.balance-logs-card-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 22px 14px;
  background:linear-gradient(135deg,#042636,#052d40 55%,#041d28);
  border-bottom:1px solid #0e3b4e;
}
.balance-logs-card-title{
  margin:0;
  font-size:20px;
  font-weight:600;
  letter-spacing:.5px;
  color:#e9f6ff;
  text-shadow:0 1px 2px rgba(0,0,0,.65);
}
.badge-action-deduction {
  background-color: #dc3545;
}
.badge-action-addition {
  background-color: #28a745;
}
.badge-action-refund {
  background-color: #17a2b8;
}
.badge-action-manual_add {
  background-color: #007bff;
}
.badge-action-manual_deduct {
  background-color: #fd7e14;
}
.amount-positive {
  color: #28a745;
  font-weight: 600;
}
.amount-negative {
  color: #dc3545;
  font-weight: 600;
}
</style>

<div class="page-header d-md-none">
  <h1 class="page-title">
    <i class="fe fe-activity" aria-hidden="true"> </i> 
    <?=lang("Balance_Logs")?>
  </h1>
  <?php if (get_role('admin')): ?>
  <div class="page-options">
    <a href="<?=cn($module.'/view_execution_logs')?>" class="btn btn-info">
      <i class="fe fe-activity"></i> <?=lang('View_Cron_Logs')?>
    </a>
  </div>
  <?php endif; ?>
</div>

<div class="row" id="result_ajaxSearch">
  <?php if (!empty($balance_logs)) { ?>

<div class="col-md-12 col-xl-12">
  <div class="balance-logs-card">
    <div class="balance-logs-card-header">
      <h3 class="balance-logs-card-title"><?= lang('Balance_Change_History') ?></h3>
      
      <div class="d-flex align-items-center">
        <?php if (get_role('admin')): ?>
          <a href="<?=cn($module.'/view_execution_logs')?>" class="btn btn-info btn-sm mr-3">
            <i class="fe fe-activity"></i> <?=lang('View_Cron_Logs')?>
          </a>
        <?php endif; ?>
        
        <?php if (get_role('admin') || get_role('supporter')): ?>
          <div class="search-form">
            <form action="<?=cn($module."/search")?>" method="get" class="form-inline">
              <div class="form-group mr-2">
                <select name="search_type" class="form-control">
                  <option value="1"><?=lang('User_Email')?></option>
                  <option value="2"><?=lang('Related_ID')?></option>
                  <option value="3"><?=lang('Action_Type')?></option>
                </select>
              </div>
              <div class="form-group mr-2">
                <input type="text" name="query" class="form-control" placeholder="<?=lang('Search')?>" value="<?=get('query')?>">
              </div>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fe fe-search"></i> <?=lang('Search')?>
              </button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered table-outline table-vcenter card-table">
        <thead>
          <tr>
            <th class="text-center w-1"><?=lang('No_')?></th>
            <?php if (!empty($columns)) {
              foreach ($columns as $key => $row) { ?>
                <th><?=$row?></th>
            <?php }} ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if (!empty($balance_logs)) {
              $i = 0;
              $current_currency = get_current_currency();
              $currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol", '$');
              foreach ($balance_logs as $key => $row) {
                $i++;
                // Use helper functions for cleaner code
                $is_positive = is_balance_positive_action($row->action_type);
                $amount_class = $is_positive ? 'amount-positive' : 'amount-negative';
                $amount_prefix = $is_positive ? '+' : '-';
          ?>
          <tr class="tr_<?=$row->ids?>">
            <td class="text-center"><?=$i?></td>
            
            <?php if (get_role("admin") || get_role("supporter")) { ?>
            <td>
              <div class="title">
                <?php 
                  $user_name = trim($row->first_name . ' ' . $row->last_name);
                  echo $user_name ? $user_name : $row->email;
                ?>
              </div>
              <small class="text-muted">ID: <?=$row->uid?></small><br>
              <small class="text-muted"><?=$row->email?></small>
            </td>
            <?php } ?>
            
            <td>
              <?php
                $action_display = format_balance_action_display($row->action_type);
                $badge_class = get_balance_action_class($row->action_type);
              ?>
              <span class="badge <?=$badge_class?>"><?=$action_display?></span>
            </td>
            
            <td class="<?=$amount_class?>">
              <?=$amount_prefix . $currency_symbol . currency_format(convert_currency($row->amount), get_option('currency_decimal', 2))?>
            </td>
            
            <td>
              <?=$currency_symbol . currency_format(convert_currency($row->balance_before), get_option('currency_decimal', 2))?>
            </td>
            
            <td>
              <?=$currency_symbol . currency_format(convert_currency($row->balance_after), get_option('currency_decimal', 2))?>
            </td>
            
            <td><?=htmlspecialchars($row->description)?></td>
            
            <?php if (get_role("admin") || get_role("supporter")) { ?>
            <td>
              <?=$row->related_id ? htmlspecialchars($row->related_id) : '-'?>
            </td>
            
            <td>
              <?=$row->related_type ? htmlspecialchars($row->related_type) : '-'?>
            </td>
            <?php } ?>
            
            <td><?=convert_timezone($row->created, 'user')?></td>
          </tr>
          <?php }} ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="col-md-12">
  <div class="float-right">
    <?=$links?>
  </div>
</div>

<?php } else {
  echo Modules::run("blocks/empty_data");
} ?>
</div>
