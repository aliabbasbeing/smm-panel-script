<?php
  $option                 = get_value($payment_params, 'option');
  $min_amount             = get_value($payment_params, 'min');
  $max_amount             = get_value($payment_params, 'max');
  $type                   = get_value($payment_params, 'type');
  $tnx_fee                = get_value($option, 'tnx_fee');
  $currency_rate_to_usd   = get_value($option, 'rate_to_usd');
  $currency_code          = get_value($option, 'currency_code');
  $account_number         = get_value($payment_params, 'number');
  $holder_name            = get_value($payment_params, 'holder');
  $whatsapp_number        = get_value($payment_params, 'whatsapp');
  $email                  = get_value($payment_params, 'email');
?>
<div class="add-funds-form-content">
  <form class="form actionAddFundsForm" action="#" method="POST">
    <div class="row">
      <div class="col-md-12">

        <div class="for-group text-center">
          <img src="/assets/images/payments/sadapay.png" alt="Sadapay icon" height="100">
          <p class="p-t-10"><small>You can deposit funds with Sadapay and they will be automatically added into your account within 1 hour!</small></p>
        </div>
        
        <div class="for-group text-center text-uppercase">
          <strong>SADAPAY ACCOUNT TITLE:</strong><strong><h2><?php echo $holder_name; ?></strong></h2>
        </div>

        <div class="for-group text-center">
          <strong>SADAPAY ACCOUNT NUMBER:</strong><strong><h2><?php echo $account_number; ?></strong></h2>
        </div>

        <div class="form-group">
          <label>Transaction ID</label>
          <input class="form-control square" type="number" name="transaction_id" placeholder="1234567890" required>
        </div>
        
        <div class="form-group">
          <label>Amount (PKR)</label>
          <input class="form-control square" type="number" name="amount" placeholder="1">
        </div>

        <div class="form-group">
          <label><?php echo lang("note"); ?></label>
          <ul>
            <?php
              if ($tnx_fee > 0) {
            ?>
            <li><?=lang("transaction_fee")?>: <strong><?php echo $tnx_fee; ?>%</strong></li>
            <?php } ?>
            <li><?=lang("Minimal_payment")?>: <strong><?php echo $min_amount.$currency_code; ?></strong></li>
            <?php
              if ($max_amount > 0) {
            ?>
            <li><?=lang("Maximal_payment")?>: <strong><?php echo $max_amount.$currency_code; ?></strong></li>
            <?php } ?>
            <?php
              if ( $currency_rate_to_usd  > 1) {
            ?>
            <li><?=lang("currency_rate")?>: 1USD = <strong><?php echo $currency_rate_to_usd; ?></strong><?php echo $currency_code; ?></li>
            <?php }?>
            <li><?php echo lang("clicking_return_to_shop_merchant_after_payment_successfully_completed"); ?></li>
          </ul>
        </div>

        <div class="form-group">
          <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="agree" value="1">
            <span class="custom-control-label text-uppercase"><strong><?=lang("yes_i_understand_after_the_funds_added_i_will_not_ask_fraudulent_dispute_or_chargeback")?></strong></span>
          </label>
        </div>
        
        <div class="form-actions left">
          <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>">
          <input type="hidden" name="payment_method" value="<?php echo $type; ?>">
          <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1">
            <?=lang("Pay")?>
          </button>
        </div>
      </div>  
    </div>
  </form>
</div>