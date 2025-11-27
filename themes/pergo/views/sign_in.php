<?=Modules::run(get_theme()."/header")?>  

<?php if (get_option('sign_in_text','') != '') { ?>
<div class="container" style="margin-top: 20px;">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-12">
      <div class="card">
        <div class="card-body">
          <?=get_option('sign_in_text','')?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php }?>

<section class="banner">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-8 col-sm-10 col-xs-12" data-aos="fade-down" data-aos-easing="ease-in" data-aos-delay="300">
        <div class="form-login">
          <form class="actionForm" action="<?=cn("auth/ajax_sign_in")?>" data-redirect="<?=cn('order/add')?>" method="POST">
            <div>
              <div class="form-group">
                <?php
                  if (isset($_COOKIE["cookie_email"])) {
                    $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
                  }

                  if (isset($_COOKIE["cookie_pass"])) {
                    $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
                  }
                ?>
                <!-- Email Input with Label -->
                <div class="input-group mb-5">
                  <label style="display: block; font-size: 14px; font-weight: 600; color: #000000ff; margin-bottom: 5px;">
                    <?=lang("Email")?>
                  </label>
                  <input type="email" class="form-control1" name="email"
                         placeholder="<?=lang("Email")?>"
                         value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : ""?>" required>
                </div>

                <!-- Password Input with Label -->
                <div class="input-group mb-5">
                  <label style="display: block; font-size: 14px; font-weight: 600; color: #000000ff; margin-bottom: 5px;">
                    <?=lang("Password")?>
                  </label>
                  <input type="password" class="form-control" name="password"
                         placeholder="<?=lang("Password")?>"
                         value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" required>
                </div>
              </div>

              <!-- Remember / Forgot row -->
<div class="form-group remember-row">
  <div class="remember-left">
    <label class="custom-control custom-checkbox mb-0">
      <input type="checkbox" name="remember" class="custom-control-input" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
      <span class="custom-control-label"><?=lang("remember_me")?></span>
    </label>
  </div>

  <div class="remember-right">
    <a href="<?=cn("auth/forgot_password")?>" class="small btn-forgot-pass">
      <?=lang("forgot_password")?>
    </a>
  </div>
</div>

              <div class="form-footer">
                <button type="submit" class="btn-submit btn-gradient btn-hover color-6">
                  <?=lang("Login")?>
                </button>
              </div>
            </div>
          </form>

          <?php if(get_option('enable_google_login') && get_option('google_client_id') && get_option('google_client_secret')){ ?>
          <!-- Divider -->
          <div class="text-center text-muted m-t-20 m-b-20" style="position: relative;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 0 10px; position: relative; z-index: 1; color: #ffffff; font-weight: 500;">OR</span>
            <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(255,255,255,0.2); z-index: 0;"></div>
          </div>

          <!-- Google Login Button -->
          <div class="form-footer">
            <a href="<?=cn('auth/google')?>" class="btn-google-login" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 45px; background: #ffffff; color: #333333; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="margin-right: 10px;">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
              </svg>
              Sign in with Google
            </a>
          </div>
          <?php }; ?>

          <?php if(!get_option('disable_signup_page')){ ?>
          <div class="text-center text-muted m-t-20">
            <?=lang("dont_have_account_yet")?> 
            <a href="<?=cn('auth/signup')?>" class="btn-sign-up"><?=lang("Sign_Up")?></a>
          </div>
          <?php }; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?=Modules::run(get_theme()."/footer")?>