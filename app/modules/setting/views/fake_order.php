
   <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-hash"></i> <?=lang("Fake Order Settings")?></h3>
      </div>
      <div class="card-body">
        <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
          <div class="row">

            <div class="col-md-12 col-lg-12">

              <h5 class="text-info"><i class="fe fe-toggle-left"></i> <?=lang("Enable Fake Order ID Increment")?></h5>
              <div class="form-group">
                <div class="form-label"><?=lang("Status")?></div>
                <label class="custom-switch">
                  <input type="hidden" name="fake_order_enabled" value="0">
                  <input type="checkbox" name="fake_order_enabled" class="custom-switch-input" <?=(get_option("fake_order_enabled", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?=lang("Active")?></span>
                </label>
                <br>
                <small class="text-muted" data-toggle="tooltip" data-placement="top" title="<?=lang("When enabled, order IDs will be incremented with a fake sequence to mask the real order count. This helps protect business information by hiding actual order volume from customers.")?>"><?=lang("Enable or disable automatic Fake Order ID Increment")?> <i class="fe fe-help-circle text-info" style="cursor:help;"></i></small>
              </div>

              <hr>

              <h5 class="text-info"><i class="fe fe-plus-circle"></i> <?=lang("Fake Order ID Offset")?></h5>
              <div class="form-group">
                <label class="form-label"><?=lang("Offset Value")?></label>
                <input type="number" class="form-control" name="fake_order_offset" value="<?=get_option('fake_order_offset', 1000)?>" min="0" max="10000000">
                <small class="text-muted" data-toggle="tooltip" data-placement="top" title="<?=lang("This value will be added to actual order IDs when displaying to users. For example, if offset is 1000 and real order ID is 5, displayed ID will be 1005.")?>"><?=lang("The base offset to add to actual order IDs when displaying to customers")?> <i class="fe fe-help-circle text-info" style="cursor:help;"></i></small>
              </div>

              <hr>

              <h5 class="text-info"><i class="fe fe-x-circle"></i> <?=lang("Fake Order ID Multiplier")?></h5>
              <div class="form-group">
                <label class="form-label"><?=lang("Multiplier Value")?></label>
                <input type="number" class="form-control" name="fake_order_multiplier" value="<?=get_option('fake_order_multiplier', 1)?>" min="1" max="100" step="1">
                <small class="text-muted" data-toggle="tooltip" data-placement="top" title="<?=lang("This multiplies the actual order ID before adding the offset. For example, with multiplier 2 and offset 1000, real order ID 5 becomes (5*2)+1000=1010.")?>"><?=lang("Multiplier applied to actual order IDs before adding offset")?> <i class="fe fe-help-circle text-info" style="cursor:help;"></i></small>
              </div>

              <hr>

              <div class="alert alert-info">
                <i class="fe fe-info"></i> 
                <strong><?=lang("How it works")?>:</strong> 
                <?=lang("When enabled, the displayed Order ID will be calculated as: (Actual Order ID × Multiplier) + Offset. This helps mask your real order volume while maintaining consistent, unique order IDs for tracking purposes.")?>
              </div>

              <div class="alert alert-warning">
                <i class="fe fe-alert-triangle"></i> 
                <strong><?=lang("Important")?>:</strong>
                <ul class="mb-0 mt-2">
                  <li><?=lang("The fake order ID is only for display purposes to customers")?></li>
                  <li><?=lang("Internal order tracking still uses real order IDs")?></li>
                  <li><strong><?=lang("Admin panel will continue to show actual order IDs")?></strong> (<?=lang("with fake ID preview")?>)</li>
                  <li><?=lang("Changing these settings will affect all existing and new orders")?></li>
                </ul>
              </div>

              <?php
              // Show example calculation
              $fake_enabled = get_option('fake_order_enabled', 0);
              $fake_offset = get_option('fake_order_offset', 1000);
              $fake_multiplier = get_option('fake_order_multiplier', 1);
              $example_real_id = 25;
              $example_fake_id = ($example_real_id * $fake_multiplier) + $fake_offset;
              ?>
              <div class="alert alert-success">
                <i class="fe fe-check-circle"></i> 
                <strong><?=lang("Example Calculation")?>:</strong>
                <br>
                <?=lang("Real Order ID")?> = <?=$example_real_id?><br>
                <?=lang("Multiplier")?> = <?=$fake_multiplier?><br>
                <?=lang("Offset")?> = <?=$fake_offset?><br>
                <?=lang("Displayed Order ID")?> = (<?=$example_real_id?> × <?=$fake_multiplier?>) + <?=$fake_offset?> = <strong><?=$example_fake_id?></strong>
              </div>

            </div> 
            <div class="col-md-8">
              <div class="form-footer">
                <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

    <script>
    $(document).ready(function(){
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
