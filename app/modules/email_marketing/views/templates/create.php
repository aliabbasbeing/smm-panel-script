<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_template_create'); ?>" data-redirect="<?php echo cn($module . '/templates'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fas fa-file-text"></i> Create New Email Template</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Template Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" placeholder="e.g., Welcome Email" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Email Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="subject" placeholder="e.g., Welcome to {site_name}!" required>
                    <small class="text-muted">You can use variables like {username}, {email}, {site_name}</small>
                  </div>
                  
                  <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control square" name="description" rows="2" placeholder="Brief description of this template"></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label>Email Body (HTML) <span class="text-danger">*</span></label>
                    <textarea class="form-control square" name="body" id="email_body" rows="15" required></textarea>
                    <small class="text-muted">HTML content of the email. Use variables: {username}, {email}, {balance}, {site_name}, {site_url}</small>
                  </div>
                  
                  <div class="alert alert-info">
                    <strong>Available Variables:</strong>
                    <ul class="mb-0">
                      <li><code>{username}</code> - User's name</li>
                      <li><code>{email}</code> - User's email address</li>
                      <li><code>{balance}</code> - User's balance</li>
                      <li><code>{site_name}</code> - Website name</li>
                      <li><code>{site_url}</code> - Website URL</li>
                      <li><code>{current_date}</code> - Current date</li>
                    </ul>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn round btn-primary btn-min-width me-1 mb-1">Submit</button>
            <button type="button" class="btn round btn-default btn-min-width me-1 mb-1" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
