<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_campaign_edit/' . $campaign->ids); ?>" data-redirect="<?php echo cn($module . '/campaigns'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Campaign: <?php echo htmlspecialchars($campaign->name); ?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Campaign Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($campaign->name); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Message Template <span class="text-danger">*</span></label>
                    <select class="form-control square" name="template_id" required>
                      <?php if(!empty($templates)){ 
                        foreach($templates as $template){
                      ?>
                      <option value="<?php echo $template->id; ?>" <?php echo $template->id == $campaign->template_id ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($template->name); ?>
                      </option>
                      <?php }} ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>API Configuration <span class="text-danger">*</span></label>
                    <select class="form-control square" name="api_config_id" required>
                      <?php if(!empty($api_configs)){ 
                        foreach($api_configs as $api){
                      ?>
                      <option value="<?php echo $api->id; ?>" <?php echo $api->id == $campaign->api_config_id ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($api->name); ?>
                      </option>
                      <?php }} ?>
                    </select>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Hourly Limit</label>
                        <input type="number" class="form-control square" name="sending_limit_hourly" value="<?php echo $campaign->sending_limit_hourly; ?>" min="1">
                        <small class="text-muted">Max messages per hour</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Daily Limit</label>
                        <input type="number" class="form-control square" name="sending_limit_daily" value="<?php echo $campaign->sending_limit_daily; ?>" min="1">
                        <small class="text-muted">Max messages per day</small>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn round btn-primary btn-min-width me-1 mb-1">Update</button>
            <button type="button" class="btn round btn-default btn-min-width me-1 mb-1" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
