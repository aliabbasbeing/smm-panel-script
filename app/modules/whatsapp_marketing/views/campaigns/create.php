<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_campaign_create'); ?>" data-redirect="<?php echo cn($module . '/campaigns'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-whatsapp"></i> Create New WhatsApp Campaign</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Campaign Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" placeholder="Enter campaign name" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Message Template <span class="text-danger">*</span></label>
                    <select class="form-control square" name="template_id" required>
                      <option value="">Select Template</option>
                      <?php if(!empty($templates)){ 
                        foreach($templates as $template){
                      ?>
                      <option value="<?php echo $template->id; ?>"><?php echo htmlspecialchars($template->name); ?></option>
                      <?php }} ?>
                    </select>
                    <small class="text-muted">Choose a message template for this campaign</small>
                  </div>
                  
                  <div class="form-group">
                    <label>API Configuration <span class="text-danger">*</span></label>
                    <select class="form-control square" name="api_config_id" required>
                      <option value="">Select API Config</option>
                      <?php if(!empty($api_configs)){ 
                        foreach($api_configs as $api){
                          if($api->status == 1){
                      ?>
                      <option value="<?php echo $api->id; ?>" <?php echo $api->is_default ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($api->name); ?>
                        <?php echo $api->is_default ? ' (Default)' : ''; ?>
                      </option>
                      <?php }}} ?>
                    </select>
                    <small class="text-muted">Select WhatsApp API to send messages</small>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Hourly Limit</label>
                        <input type="number" class="form-control square" name="sending_limit_hourly" placeholder="e.g., 100" min="1">
                        <small class="text-muted">Max messages per hour (leave empty for no limit)</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Daily Limit</label>
                        <input type="number" class="form-control square" name="sending_limit_daily" placeholder="e.g., 1000" min="1">
                        <small class="text-muted">Max messages per day (leave empty for no limit)</small>
                      </div>
                    </div>
                  </div>
                  
                  <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> After creating the campaign, you'll be able to add recipients and start sending.
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
