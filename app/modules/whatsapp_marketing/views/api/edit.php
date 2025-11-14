<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_api_edit/' . $api->ids); ?>" data-redirect="<?php echo cn($module . '/api'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit WhatsApp API Configuration</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Configuration Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($api->name); ?>" placeholder="e.g., Main WhatsApp API" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API URL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="api_url" value="<?php echo htmlspecialchars($api->api_url); ?>" placeholder="e.g., https://api.whatsapp.com/send" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="api_key" value="<?php echo htmlspecialchars($api->api_key); ?>" placeholder="Your WhatsApp API key" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API Type</label>
                    <select class="form-control square" name="api_type">
                      <option value="whatsapp_business" <?php echo ($api->api_type == 'whatsapp_business') ? 'selected' : ''; ?>>WhatsApp Business API</option>
                      <option value="third_party" <?php echo ($api->api_type == 'third_party') ? 'selected' : ''; ?>>Third Party API</option>
                      <option value="custom" <?php echo ($api->api_type == 'custom') ? 'selected' : ''; ?>>Custom Integration</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Instance ID</label>
                    <input type="text" class="form-control square" name="instance_id" value="<?php echo htmlspecialchars($api->instance_id); ?>" placeholder="Instance ID (if applicable)">
                    <small class="form-text text-muted">Required for WhatsApp Business API</small>
                  </div>
                  
                  <div class="form-group">
                    <label>WhatsApp Phone Number</label>
                    <input type="text" class="form-control square" name="phone_number" value="<?php echo htmlspecialchars($api->phone_number); ?>" placeholder="e.g., +1234567890">
                    <small class="form-text text-muted">WhatsApp Business phone number</small>
                  </div>
                  
                  <div class="form-group">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="is_default" value="1" <?php echo ($api->is_default) ? 'checked' : ''; ?>>
                      <span class="custom-control-label">Set as default API configuration</span>
                    </label>
                  </div>
                  
                  <div class="form-group">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="status" value="1" <?php echo ($api->status) ? 'checked' : ''; ?>>
                      <span class="custom-control-label">Active</span>
                    </label>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
