<?=Modules::run(get_theme()."/header", false)?>

<div class="login-bg-image"></div>
<div class="page auth-login-form">
  <div class="container h-100">
    <div class="row h-100 align-items-center auth-form">
      <div class="col-md-6 col-login mx-auto ">
        <form class="card actionForm" action="<?=cn("auth/ajax_sign_in")?>" data-redirect="<?=cn('statistics')?>" method="POST">
          <div class="card-body ">
            <div class="card-title text-center">
              <div class="site-logo mb-2">
                <a href="<?=cn()?>">
                  <img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" alt="website-logo" class="website-logo">
                </a>
              </div>
              <h5><?=lang("login_to_your_account")?></h5>
            </div>
            <div class="form-group">
              <?php

                if (isset($_COOKIE["cookie_email"])) {
                  $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
                }

                if (isset($_COOKIE["cookie_pass"])) {
                  $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
                }

              ?>
              <div class="input-icon mb-5">
                <span class="input-icon-addon">
                  <i class="fe fe-mail"></i>
                </span>
                <input type="email" class="form-control" name="email" placeholder="<?=lang("Email")?>" value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : ""?>" required>
              </div>    
                    
              <div class="input-icon mb-5">
                <span class="input-icon-addon">
                  <i class="fa fa-key"></i>
                </span>
                <input type="password" class="form-control" name="password" placeholder="<?=lang("Password")?>" value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" required>
              </div>  
            </div>

            <div class="form-group">
              <label class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
                <span class="custom-control-label"><?=lang("remember_me")?></span>
                <a href="<?=cn("auth/forgot_password")?>" class="float-right small"><?=lang("forgot_password")?></a>
              </label>
            </div>

            <div class="form-footer">
              <button type="submit" class="btn btn-primary btn-block"><?=lang("Login")?></button>
            </div>

            <?php if(get_option('enable_google_login') && get_option('google_client_id') && get_option('google_client_secret')){ ?>
            <!-- Divider -->
            <div class="text-center text-muted mt-3 mb-3" style="position: relative;">
              <span style="background: #fff; padding: 0 10px; position: relative; z-index: 1;">OR</span>
              <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e0e0e0; z-index: 0;"></div>
            </div>

            <!-- Google Login Button -->
            <a href="<?=cn('auth/google')?>" class="btn btn-block" style="background: #ffffff; color: #333333; border: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: center; padding: 10px;">
              <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="margin-right: 10px;">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
              </svg>
              Sign in with Google
            </a>
            <?php }; ?>
          </div>
        </form>
        <?php if(!get_option('disable_signup_page')){ ?>
        <div class="text-center text-muted">
          <?=lang("dont_have_account_yet")?> <a href="<?=cn('auth/signup')?>"><?=lang("Sign_Up")?></a>
        </div>
        <?php }; ?>
      </div>
    </div>
  </div>
</div>

<?=Modules::run(get_theme()."/footer", false)?>