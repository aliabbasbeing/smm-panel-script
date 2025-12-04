   <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-star"></i> <?=lang("Visual Effects")?></h3>
      </div>
      <div class="card-body">
        <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
          <div class="row">

            <div class="col-md-12 col-lg-12">

              <h5 class="text-info"><i class="fe fe-toggle-left"></i> <?=lang("Enable Visual Effects")?></h5>
              <div class="form-group">
                <div class="form-label"><?=lang("Status")?></div>
                <label class="custom-switch">
                  <input type="hidden" name="visual_effects_enabled" value="0">
                  <input type="checkbox" name="visual_effects_enabled" class="custom-switch-input" <?=(get_option("visual_effects_enabled", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?=lang("Active")?></span>
                </label>
                <br>
                <small class="text-muted"><?=lang("Enable or disable visual effects across all pages")?></small>
              </div>

              <hr>

              <h5 class="text-info"><i class="fe fe-layers"></i> <?=lang("Effect Type")?></h5>
              <div class="form-group">
                <select name="visual_effects_type" class="form-control square">
                  <?php
                    $effect_types = array(
                      'snow'      => 'Snow ❄️',
                      'bubbles'   => 'Bubbles 🫧',
                      'confetti'  => 'Confetti 🎉',
                      'stars'     => 'Stars ⭐',
                      'hearts'    => 'Hearts ❤️',
                      'leaves'    => 'Leaves 🍂',
                    );
                    foreach ($effect_types as $key => $row) {
                  ?>
                  <option value="<?php echo $key; ?>" <?=(get_option('visual_effects_type', 'snow') == $key)? 'selected': ''?>> <?php echo $row; ?></option>
                  <?php }?>
                </select>
                <small class="text-muted"><?=lang("Choose the type of visual effect to display")?></small>
              </div>

              <hr>

              <h5 class="text-info"><i class="fe fe-droplet"></i> <?=lang("Effect Color")?></h5>
              <div class="form-group">
                <input type="color" name="visual_effects_color" class="form-control" style="width: 100px; height: 40px; padding: 5px;" value="<?=get_option('visual_effects_color', '#ffffff')?>">
                <small class="text-muted"><?=lang("Primary color of the effect particles")?></small>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-4">
                  <h5 class="text-info"><i class="fe fe-maximize-2"></i> <?=lang("Particle Size")?></h5>
                  <div class="form-group">
                    <select name="visual_effects_size" class="form-control square">
                      <?php
                        $sizes = array(
                          'small'   => 'Small',
                          'medium'  => 'Medium',
                          'large'   => 'Large',
                          'mixed'   => 'Mixed',
                        );
                        foreach ($sizes as $key => $row) {
                      ?>
                      <option value="<?php echo $key; ?>" <?=(get_option('visual_effects_size', 'medium') == $key)? 'selected': ''?>> <?php echo $row; ?></option>
                      <?php }?>
                    </select>
                    <small class="text-muted"><?=lang("Size of the effect particles")?></small>
                  </div>
                </div>

                <div class="col-md-4">
                  <h5 class="text-info"><i class="fe fe-grid"></i> <?=lang("Particle Density")?></h5>
                  <div class="form-group">
                    <select name="visual_effects_density" class="form-control square">
                      <?php
                        $densities = array(
                          'light'   => 'Light (25 particles)',
                          'medium'  => 'Medium (50 particles)',
                          'heavy'   => 'Heavy (100 particles)',
                          'extreme' => 'Extreme (150 particles)',
                        );
                        foreach ($densities as $key => $row) {
                      ?>
                      <option value="<?php echo $key; ?>" <?=(get_option('visual_effects_density', 'medium') == $key)? 'selected': ''?>> <?php echo $row; ?></option>
                      <?php }?>
                    </select>
                    <small class="text-muted"><?=lang("Number of particles on screen")?></small>
                  </div>
                </div>

                <div class="col-md-4">
                  <h5 class="text-info"><i class="fe fe-zap"></i> <?=lang("Animation Speed")?></h5>
                  <div class="form-group">
                    <select name="visual_effects_speed" class="form-control square">
                      <?php
                        $speeds = array(
                          'slow'    => 'Slow',
                          'normal'  => 'Normal',
                          'fast'    => 'Fast',
                        );
                        foreach ($speeds as $key => $row) {
                      ?>
                      <option value="<?php echo $key; ?>" <?=(get_option('visual_effects_speed', 'normal') == $key)? 'selected': ''?>> <?php echo $row; ?></option>
                      <?php }?>
                    </select>
                    <small class="text-muted"><?=lang("Speed of particle animation")?></small>
                  </div>
                </div>
              </div>

              <hr>

              <div class="alert alert-info">
                <i class="fe fe-info"></i> 
                <strong><?=lang("Note")?>:</strong> 
                <?=lang("Visual effects are designed to be lightweight and will not affect page interactions or click events. Effects are rendered on a separate layer above the content.")?> 
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
