
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-lock"></i> <?=lang("Google OAuth Settings")?></h3>
      </div>
      <div class="card-body">
        <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
          <div class="row">

            <div class="col-md-12 col-lg-12">

              <h5 class="text-info"><i class="fe fe-toggle-right"></i> <?=lang("Enable Google Login")?></h5>
              <div class="form-group">
                <div class="form-label"><?=lang("Status")?></div>
                <label class="custom-switch">
                  <input type="hidden" name="enable_google_login" value="0">
                  <input type="checkbox" name="enable_google_login" class="custom-switch-input" <?=(get_option("enable_google_login", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?=lang("Active")?></span>
                </label>
                <br>
                <small class="text-muted"><?=lang("Enable or disable Google login on the login page")?></small>
              </div>

              <h5 class="text-info"><i class="fe fe-key"></i> <?=lang("Google OAuth Credentials")?></h5>
              <div class="alert alert-info">
                <strong><i class="fe fe-info"></i> <?=lang("How to get Google OAuth credentials")?></strong>
                <ol class="mt-2 mb-0">
                  <li><?=lang("Go to")?> <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                  <li><?=lang("Create a new project or select an existing one")?></li>
                  <li><?=lang("Navigate to APIs & Services > Credentials")?></li>
                  <li><?=lang("Click 'Create Credentials' and select 'OAuth client ID'")?></li>
                  <li><?=lang("Choose 'Web application' as the application type")?></li>
                  <li><?=lang("Add authorized redirect URI")?>: <code><?=cn('auth/google_callback')?></code></li>
                  <li><?=lang("Copy the Client ID and Client Secret below")?></li>
                </ol>
              </div>

              <div class="form-group">
                <label class="form-label"><?=lang("Google Client ID")?> <span class="text-danger">*</span></label>
                <input class="form-control" name="google_client_id" value="<?=get_option('google_client_id', '')?>" placeholder="<?=lang("Enter Google Client ID")?>">
                <small class="text-muted"><?=lang("Your Google OAuth 2.0 Client ID")?></small>
              </div>

              <div class="form-group">
                <label class="form-label"><?=lang("Google Client Secret")?> <span class="text-danger">*</span></label>
                <input class="form-control" name="google_client_secret" value="<?=get_option('google_client_secret', '')?>" placeholder="<?=lang("Enter Google Client Secret")?>">
                <small class="text-muted"><?=lang("Your Google OAuth 2.0 Client Secret")?></small>
              </div>

              <div class="form-group">
                <label class="form-label"><?=lang("Authorized Redirect URI")?></label>
                <input class="form-control" value="<?=cn('auth/google_callback')?>" readonly>
                <small class="text-muted"><?=lang("Use this URL in your Google OAuth app configuration")?></small>
              </div>

              <div class="alert alert-warning">
                <strong><i class="fe fe-alert-triangle"></i> <?=lang("Important Notes")?></strong>
                <ul class="mt-2 mb-0">
                  <li><?=lang("Make sure to add the redirect URI to your Google OAuth app")?></li>
                  <li><?=lang("Users who sign in with Google will be automatically created in the system")?></li>
                  <li><?=lang("Google users will not have a password and can only login via Google")?></li>
                  <li><?=lang("Keep your Client Secret secure and never share it publicly")?></li>
                </ul>
              </div>

            </div>

          </div>

          <div class="form-group mt-4">
            <button class="btn btn-primary btn-min-width text-uppercase mr-1"><i class="fe fe-check-circle"></i> <?=lang("Save")?></button>
            <button class="btn btn-secondary btn-min-width text-uppercase" type="reset"><i class="fe fe-x-circle"></i> <?=lang("reset")?></button>
          </div>
        </form>
      </div>
    </div>
