<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_campaign_edit/' . $campaign->ids); ?>" data-redirect="<?php echo cn($module . '/campaigns'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Campaign</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Campaign Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($campaign->name); ?>" placeholder="Enter campaign name" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Email Template <span class="text-danger">*</span></label>
                    <select class="form-control square" name="template_id" required>
                      <option value="">Select Template</option>
                      <?php if(!empty($templates)){ 
                        foreach($templates as $template){
                      ?>
                      <option value="<?php echo $template->id; ?>" <?php echo ($campaign->template_id == $template->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($template->name); ?>
                      </option>
                      <?php }} ?>
                    </select>
                    <small class="text-muted">Choose an email template for this campaign</small>
                  </div>
                  
                  <?php 
                  // Parse existing SMTP IDs
                  $selected_smtp_ids = array();
                  if(!empty($campaign->smtp_config_ids)){
                    $selected_smtp_ids = json_decode($campaign->smtp_config_ids, true);
                    if(!is_array($selected_smtp_ids)){
                      $selected_smtp_ids = array();
                    }
                  }
                  // Fallback to single smtp_config_id if smtp_config_ids is empty
                  if(empty($selected_smtp_ids) && !empty($campaign->smtp_config_id)){
                    $selected_smtp_ids = array($campaign->smtp_config_id);
                  }
                  ?>
                  <div class="form-group">
                    <label>SMTP Configurations <span class="text-danger">*</span></label>
                    <div class="smtp-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                      <?php if(!empty($smtp_configs)){ 
                        foreach($smtp_configs as $smtp){
                          if($smtp->status == 1){
                      ?>
                      <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" name="smtp_config_ids[]" value="<?php echo $smtp->id; ?>" id="smtp_edit_<?php echo $smtp->id; ?>" <?php echo in_array($smtp->id, $selected_smtp_ids) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="smtp_edit_<?php echo $smtp->id; ?>">
                          <?php echo htmlspecialchars($smtp->name); ?>
                          <?php echo $smtp->is_default ? ' <span class="badge badge-primary">Default</span>' : ''; ?>
                        </label>
                      </div>
                      <?php }}} ?>
                    </div>
                    <small class="text-muted">Select one or more SMTP servers. Multiple SMTPs will be rotated round-robin during sending.</small>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Hourly Limit</label>
                        <input type="number" class="form-control square" name="sending_limit_hourly" value="<?php echo $campaign->sending_limit_hourly; ?>" placeholder="e.g., 100" min="1">
                        <small class="text-muted">Max emails per hour (leave empty for no limit)</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Daily Limit</label>
                        <input type="number" class="form-control square" name="sending_limit_daily" value="<?php echo $campaign->sending_limit_daily; ?>" placeholder="e.g., 1000" min="1">
                        <small class="text-muted">Max emails per day (leave empty for no limit)</small>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1">Submit</button>
            <button type="button" class="btn round btn-default btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
