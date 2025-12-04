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
                      'snow'      => 'Snow ❄️ - Classic snowfall',
                      'bubbles'   => 'Bubbles 🫧 - Floating bubbles',
                      'confetti'  => 'Confetti 🎉 - Party celebration',
                      'stars'     => 'Stars ⭐ - Twinkling stars',
                      'hearts'    => 'Hearts ❤️ - Floating hearts',
                      'leaves'    => 'Leaves 🍂 - Autumn leaves',
                      'rain'      => 'Rain 🌧️ - Rainfall effect',
                      'fireflies' => 'Fireflies ✨ - Glowing fireflies',
                      'sakura'    => 'Sakura 🌸 - Cherry blossoms',
                      'diamonds'  => 'Diamonds 💎 - Sparkling gems',
                      'ribbons'   => 'Ribbons 🎀 - Flowing ribbons',
                      'sparkles'  => 'Sparkles ✦ - Twinkling sparkles',
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
                <small class="text-muted"><?=lang("Primary color of the effect particles (some effects use preset color palettes)")?></small>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-4">
                  <h5 class="text-info"><i class="fe fe-maximize-2"></i> <?=lang("Particle Size")?></h5>
                  <div class="form-group">
                    <select name="visual_effects_size" class="form-control square">
                      <?php
                        $sizes = array(
                          'tiny'    => 'Tiny (1-3px)',
                          'small'   => 'Small (2-6px)',
                          'medium'  => 'Medium (5-12px)',
                          'large'   => 'Large (10-20px)',
                          'xlarge'  => 'X-Large (15-30px)',
                          'mixed'   => 'Mixed (2-25px)',
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
                          'minimal' => 'Minimal (15 particles)',
                          'light'   => 'Light (30 particles)',
                          'medium'  => 'Medium (60 particles)',
                          'heavy'   => 'Heavy (100 particles)',
                          'extreme' => 'Extreme (150 particles) ⚠️',
                          'insane'  => 'Insane (200 particles) ⚠️⚠️',
                        );
                        foreach ($densities as $key => $row) {
                      ?>
                      <option value="<?php echo $key; ?>" <?=(get_option('visual_effects_density', 'medium') == $key)? 'selected': ''?>> <?php echo $row; ?></option>
                      <?php }?>
                    </select>
                    <small class="text-muted"><?=lang("Number of particles on screen")?></small>
                    <br><small class="text-warning"><?=lang("Note: Extreme/Insane density may impact performance on lower-end devices")?></small>
                  </div>
                </div>

                <div class="col-md-4">
                  <h5 class="text-info"><i class="fe fe-zap"></i> <?=lang("Animation Speed")?></h5>
                  <div class="form-group">
                    <select name="visual_effects_speed" class="form-control square">
                      <?php
                        $speeds = array(
                          'very_slow' => 'Very Slow',
                          'slow'      => 'Slow',
                          'normal'    => 'Normal',
                          'fast'      => 'Fast',
                          'very_fast' => 'Very Fast',
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

              <div class="alert alert-success">
                <i class="fe fe-check-circle"></i> 
                <strong><?=lang("Effect Tips")?>:</strong>
                <ul class="mb-0 mt-2">
                  <li><strong>Snow/Rain</strong> - Great for seasonal themes</li>
                  <li><strong>Confetti/Ribbons</strong> - Perfect for celebrations</li>
                  <li><strong>Fireflies/Sparkles</strong> - Elegant ambient effects</li>
                  <li><strong>Sakura/Leaves</strong> - Beautiful nature themes</li>
                  <li><strong>Hearts</strong> - Ideal for Valentine's or romantic themes</li>
                  <li><strong>Diamonds/Stars</strong> - Luxurious appearance</li>
                </ul>
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