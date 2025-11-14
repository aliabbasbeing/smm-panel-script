<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_api_edit/' . $api->ids); ?>" data-redirect="<?php echo cn($module . '/api'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit API Configuration</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Configuration Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($api->name); ?>" placeholder="e.g., Gmail API" required>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label>API Host <span class="text-danger">*</span></label>
                        <input type="text" class="form-control square" name="host" value="<?php echo htmlspecialchars($api->host); ?>" placeholder="e.g., api.gmail.com" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Port <span class="text-danger">*</span></label>
                        <input type="number" class="form-control square" name="port" value="<?php echo $api->port; ?>" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Encryption</label>
                    <select class="form-control square" name="encryption">
                      <option value="none" <?php echo ($api->encryption == 'none') ? 'selected' : ''; ?>>None</option>
                      <option value="tls" <?php echo ($api->encryption == 'tls') ? 'selected' : ''; ?>>TLS</option>
                      <option value="ssl" <?php echo ($api->encryption == 'ssl') ? 'selected' : ''; ?>>SSL</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="username" value="<?php echo htmlspecialchars($api->username); ?>" placeholder="API username" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control square" name="password" placeholder="Leave empty to keep current password">
                    <small class="text-muted">Leave blank to keep existing password</small>
                  </div>
                  
                  <div class="form-group">
                    <label>From Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="from_name" value="<?php echo htmlspecialchars($api->from_name); ?>" placeholder="e.g., SMM Panel" required>
                  </div>
                  
                  <div class="form-group">
                    <label>From Phone Number <span class="text-danger">*</span></label>
                    <input type="phone_number" class="form-control square" name="from_phone_number" value="<?php echo htmlspecialchars($api->from_phone_number); ?>" placeholder="e.g., noreply@example.com" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Reply-To Phone Number</label>
                    <input type="phone_number" class="form-control square" name="reply_to" value="<?php echo htmlspecialchars($api->reply_to); ?>" placeholder="e.g., support@example.com">
                  </div>
                  
                  <div class="form-group">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="is_default" value="1" <?php echo $api->is_default ? 'checked' : ''; ?>>
                      <span class="custom-control-label">Set as default API</span>
                    </label>
                  </div>
                  
                  <div class="form-group">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="status" value="1" <?php echo $api->status ? 'checked' : ''; ?>>
                      <span class="custom-control-label">Active</span>
                    </label>
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
