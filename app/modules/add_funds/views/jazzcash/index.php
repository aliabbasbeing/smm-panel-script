<?php
  $option               = get_value($payment_params, 'option');
  $min_amount           = get_value($payment_params, 'min');
  $max_amount           = get_value($payment_params, 'max');
  $type                 = get_value($payment_params, 'type');
  $tnx_fee              = get_value($option, 'tnx_fee');
  $title                = get_value($option, 'title');
  $number               = get_value($option, 'number');
  $currency_rate_to_usd = get_value($option, 'rate_to_usd');
?>

<style>




  /* Header (JazzCash brand-inspired gradient) */
  .jc-header {
    background: linear-gradient(135deg, #7c3aed 0%, #f97316 100%);
    color: #fff;
        border-radius: 16px;
    padding: 28px 22px;
    text-align: center;
  }
  .jc-header .jc-logo {
    width: 100%;
    max-width: 220px;
    height: auto;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));
  }
  .jc-subtitle {
    opacity: 0.92;
    font-size: 1rem;
    margin-top: 10px;
  }

  /* Body */
  .jc-body {
    padding: 22px;
  }

  /* Account info section */
  .jc-section {
    border: 1px dashed rgba(124, 58, 237, 0.25);
    border-radius: 16px;
    padding: 18px;
    background: #faf5ff; /* subtle violet background */
  }
  .jc-section .jc-label {
    font-weight: 700;
    color: #0f172a;
    letter-spacing: 0.02em;
    font-size: 0.95rem;
    text-transform: uppercase;
  }
  .jc-section .jc-value {
    font-weight: 800;
    color: #6d28d9; /* deep violet accent */
    word-break: break-word;
    font-size: 1.6rem;
    line-height: 1.35;
  }

  /* Notes list */
  .jc-info-list {
    margin: 0;
    padding-left: 18px;
  }
  .jc-info-list li {
    margin-bottom: 6px;
    color: #334155;
  }

  /* Rate tag */
  .jc-rate-tag {
    display: inline-block;
    background: #f5f3ff;
    color: #5b21b6;
    border: 1px solid #ddd6fe;
    border-radius: 9999px;
    padding: 8px 14px;
    font-size: 0.95rem;
    margin-top: 10px;
  }

  /* Inputs and button */
  .form-control {
    border-radius: 10px;
  }
  .jc-submit-btn {
    border-radius: 12px !important;
    background: linear-gradient(135deg, #7c3aed 0%, #f97316 100%) !important;
    color: #fff !important;
    min-width: 180px;
    padding: 12px 20px;
    font-weight: 700;
    box-shadow: 0 6px 16px rgba(124,58,237,0.35);
    transition: transform 0.06s ease, box-shadow 0.2s ease;
  }
  .jc-submit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(124,58,237,0.45);
  }

  .jc-agree {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 16px;
  }

  /* Responsive layout rules */
  @media (max-width: 992px) {
    .add-funds-form-content { padding: 12px; }
    .jc-body { padding: 18px; }
    .jc-section { padding: 16px; }
    .jc-section .jc-value { font-size: 1.5rem; }
    .jc-header .jc-logo { max-width: 200px; }
  }
  @media (max-width: 768px) {
    .jc-header { padding: 24px 18px; }
    .jc-header .jc-logo { max-width: 180px; }
    .jc-subtitle { font-size: 0.95rem; }
    .jc-body { padding: 16px; }
    .jc-section { padding: 14px; border-radius: 14px; }
    .jc-section .jc-label { font-size: 0.9rem; text-align: center; }
    .jc-section .jc-value { font-size: 1.4rem; text-align: center; }
    .jc-rate-tag { display: block; width: 100%; text-align: center; }
    .form-control { font-size: 1rem; padding: 10px 12px; }
    .jc-submit-btn { width: 100%; min-width: 0; padding: 12px; }
  }
  @media (max-width: 576px) {
    .jc-header .jc-logo { max-width: 160px; }
    .jc-section .jc-value { font-size: 1.3rem; }
    .jc-info-list { padding-left: 16px; }
    .jc-body { padding: 14px; }
  }
</style>


  <div class="jc-card">
    <!-- Header -->
    <div class="jc-header">
      <img src="<?= BASE ?>/assets/images/payments/jazzcash.png"
           alt="JazzCash Logo"
           class="jc-logo mb-2">
      <p class="jc-subtitle mb-0">
        <?= sprintf(
              lang("you_can_deposit_funds_with_paypal_they_will_be_automaticly_added_into_your_account"),
              'JazzCash'
            ) ?>
      </p>
    </div>

    <!-- Body -->
    <div class="jc-body">
      <form class="form actionAddFundsForm"
            action="<?= cn('jazzcash/create_payment'); ?>"
            method="POST"
            novalidate>

        <!-- Account Info -->
        <div class="jc-section mb-4">
          <div class="row g-3 align-items-center">
            <div class="col-12 col-md-6">
              <div class="d-flex flex-column text-center text-md-start">
                <span class="jc-label">JazzCash Account Title</span>
                <span id="holderName" class="jc-value"><?= $title; ?></span>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="d-flex flex-column text-center text-md-start">
                <span class="jc-label">JazzCash Account Number</span>
                <span id="accountNumber" class="jc-value"><?= $number; ?></span>
              </div>
            </div>
          </div>

          <?php if ($currency_rate_to_usd > 1): ?>
            <div class="mt-3 text-center text-md-start">
              <span class="jc-rate-tag">
                1 USD = <strong><?= $currency_rate_to_usd; ?></strong> PKR
              </span>
            </div>
          <?php endif; ?>
        </div>

        <!-- Amount -->
        <div class="form-group mb-3">
          <label class="jc-label" for="jc-amount">
            <?= sprintf(lang("amount_usd"), 'PKR') ?>
          </label>
          <input id="jc-amount"
                 class="form-control form-control-lg"
                 type="number"
                 name="amount"
                 min="<?= $min_amount; ?>"
                 max="<?= $max_amount; ?>"
                 placeholder="<?= $min_amount; ?> (min)"
                 inputmode="decimal"
                 required>
        </div>

        <!-- Transaction ID -->
        <div class="form-group mb-3">
          <label class="jc-label" for="jc-order-id">
            Transaction ID
          </label>
          <input id="jc-order-id"
                 class="form-control form-control-lg"
                 type="text"
                 name="order_id"
                 placeholder="Enter your TX ID"
                 maxlength="100"
                 required>
        </div>

        <!-- Notes -->
        <div class="form-group mb-3">
          <label class="jc-label"><?= lang("note"); ?></label>
          <ul class="jc-info-list">
            <?php if ($tnx_fee > 0): ?>
              <li>
                <?= lang("transaction_fee"); ?>:
                <strong><?= $tnx_fee; ?>%</strong>
              </li>
            <?php endif; ?>
            <li>
              <?= lang("Minimal_payment"); ?>:
              <strong><?= $min_amount; ?> PKR</strong>
            </li>
            <?php if ($max_amount > 0): ?>
              <li>
                <?= lang("Maximal_payment"); ?>:
                <strong><?= $max_amount; ?> PKR</strong>
              </li>
            <?php endif; ?>
            <?php if ($currency_rate_to_usd > 1): ?>
              <li>
                <?= lang("currency_rate"); ?>:
                1 USD = <strong><?= $currency_rate_to_usd; ?></strong> PKR
              </li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Agreement Checkbox -->
        <div class="form-group jc-agree mb-3">
          <label class="custom-control custom-checkbox d-flex align-items-start">
            <input type="checkbox"
                   class="custom-control-input mt-1"
                   name="agree"
                   value="1"
                   required>
            <span class="custom-control-label text-uppercase ms-2">
              <strong>
                <?= lang("yes_i_understand_after_the_funds_added_i_will_not_ask_fraudulent_dispute_or_chargeback") ?>
              </strong>
            </span>
          </label>
        </div>

        <!-- Submit -->
        <div class="d-flex flex-wrap align-items-center">
          <input type="hidden" name="payment_id" value="<?= $payment_id; ?>">
          <input type="hidden" name="payment_method" value="<?= $type; ?>">

          <button type="submit" class="btn jc-submit-btn me-2 mb-2">
            <?= lang("Pay"); ?>
          </button>
        </div>

      </form>
    </div>
  </div>
