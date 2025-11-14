<?php if (!empty($balance_logs)) { ?>

<div class="col-md-12 col-xl-12">
  <div class="balance-logs-card">
    <div class="balance-logs-card-header">
      <h3 class="balance-logs-card-title"><?= lang('Balance_Change_History') ?></h3>
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

<?php } else {
  echo Modules::run("blocks/empty_data");
} ?>
