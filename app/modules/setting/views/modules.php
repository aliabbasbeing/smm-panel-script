<div class="card content">
  <div class="card-header" style="border:0.1px solid #1B78FC;border-radius:3.5px 3.5px 0 0;background:#1B78FC;">
    <h3 class="card-title"><i class="fa fa-question-circle"></i> <?= lang("Modules") ?></h3>
  </div>

  <div class="card-body">

    <!-- Refill Expiry Days (options) -->
    <form class="actionForm" action="<?= cn("$module/ajax_general_settings") ?>" method="POST" data-redirect="<?= get_current_url(); ?>">
      <div class="row">
        <div class="col-md-12">
          <h5 class="text-info"><i class="fe fe-link"></i> <?= lang("refill_expiry_days") ?></h5>
          <div class="form-group">
            <label><?= lang("disable_refill_option_from_and_order_after_X_days") ?></label>
            <select name="refill_expiry_days" class="form-control square">
              <?php
              $selected_days = (int) get_option('refill_expiry_days', 30);
              for ($i = 1; $i <= 90; $i++):
              ?>
                <option value="<?= $i ?>" <?= ($selected_days === $i ? 'selected' : '') ?>><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-footer">
            <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?= lang("Save") ?></button>
          </div>
        </div>
      </div>
    </form>

  </div>
</div>

<script>
  $(document).ready(function() {
    plugin_editor('.plugin_editor', {height: 200, toolbar: 'code'});
  });
</script>