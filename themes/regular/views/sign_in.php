<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=lang("Login")?> - <?=get_option('website_name', 'SMM Panel')?></title>
    <link rel="stylesheet" href="<?=BASE?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=BASE?>assets/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=BASE?>assets/plugins/feather/feather.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-container img {
            max-width: 180px;
            height: auto;
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: #ffffff;
        }
        
        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 6px;
        }
        
        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: #ffffff;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 14px;
        }
        
        .btn-google {
            width: 100%;
            padding: 12px;
            background: #ffffff;
            color: #333;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-google:hover {
            border-color: #667eea;
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-google svg {
            margin-right: 10px;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <a href="<?=cn()?>">
                <img src="<?=get_option('website_logo', BASE.'assets/images/logo.png')?>" alt="Logo">
            </a>
        </div>
        
        <h1 class="login-title"><?=lang("Login")?></h1>
        <p class="login-subtitle"><?=lang("login_to_your_account")?></p>
        
        <form class="actionForm" action="<?=cn('auth/ajax_sign_in')?>" data-redirect="<?=cn('statistics')?>" method="POST">
            <?php
                $cookie_email = '';
                $cookie_pass = '';
                if (isset($_COOKIE["cookie_email"])) {
                    $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
                }
                if (isset($_COOKIE["cookie_pass"])) {
                    $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
                }
            ?>
            
            <div class="form-group">
                <label class="form-label"><?=lang("Email")?></label>
                <input type="email" class="form-control" name="email" placeholder="<?=lang("Email")?>" value="<?=$cookie_email?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label"><?=lang("Password")?></label>
                <input type="password" class="form-control" name="password" placeholder="<?=lang("Password")?>" value="<?=$cookie_pass?>" required>
            </div>
            
            <div class="form-row">
                <div class="remember-me">
                    <input type="checkbox" name="remember" <?=($cookie_email != "") ? "checked" : ""?>>
                    <label><?=lang("remember_me")?></label>
                </div>
                <a href="<?=cn('auth/forgot_password')?>" class="forgot-link"><?=lang("forgot_password")?></a>
            </div>
            
            <button type="submit" class="btn-login"><?=lang("Login")?></button>
        </form>
        
        <?php if(get_option('enable_google_login') && get_option('google_client_id') && get_option('google_client_secret')){ ?>
        <div class="divider"><span>OR</span></div>
        
        <a href="<?=cn('auth/google')?>" class="btn-google">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Sign in with Google
        </a>
        <?php }; ?>
        
        <?php if(!get_option('disable_signup_page')){ ?>
        <div class="signup-link">
            <?=lang("dont_have_account_yet")?> <a href="<?=cn('auth/signup')?>"><?=lang("Sign_Up")?></a>
        </div>
        <?php }; ?>
    </div>
    
    <script src="<?=BASE?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=BASE?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?=BASE?>assets/js/core.js"></script>
</body>
</html>