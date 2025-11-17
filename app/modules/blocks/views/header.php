<style>
  .search-box input.form-control{
    margin: -1px;
  }
  .search-box select.form-control{
    border-radius: 0px;
    border: 1px solid #fff;
  }

.card-title.text-center {
  position: relative;
  z-index: 1;
  margin: 0;
  padding: 0;
}

.notifcation.m-r-10 {
  z-index: 100;
  position: relative;
}

/* ===== Sidenav Currency Switcher ===== */
.currency-switcher-sidenav {
  margin: 15px 15px 15px 15px;
  position: relative;
}

.currency-switcher-sidenav label {
  display: block;
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.currency-switcher-sidenav select {
  background: #ffffff !important;
  color: #000000 !important;
  border: 2px solid #000000 !important;
  border-radius: 6px;
  padding: 8px 28px 8px 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 100%;
  height: 40px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}

.currency-switcher-sidenav select:hover {
  background: #f5f5f5 !important;
  border-color: #333333 !important;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
}

.currency-switcher-sidenav select:focus {
  background: #ffffff !important;
  border-color: #000000 !important;
  box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
  outline: none;
}

.currency-switcher-sidenav select option {
  background: #ffffff;
  color: #000000;
  padding: 10px 12px;
  font-size: 14px;
  font-weight: 500;
}

.currency-switcher-sidenav select option:hover {
  background: #e0e0e0;
  color: #000000;
}

.currency-switcher-sidenav select option:checked {
  background: #000000;
  color: #ffffff;
  font-weight: 600;
}

/* Custom select wrapper for better styling */
.currency-select-wrapper {
  position: relative;
  width: 100%;
}

.currency-select-wrapper::after {
  content: "";
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-top: 5px solid #000000;
  pointer-events: none;
  transition: transform 0.2s ease;
}

.currency-switcher-sidenav select:focus ~ .currency-select-wrapper::after,
.currency-select-wrapper:has(select:focus)::after {
  transform: translateY(-50%) rotate(180deg);
}

/* Divider */
.currency-switcher-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 10px 0 5px 0;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .currency-switcher-sidenav {
    margin: 12px 12px 12px 12px;
  }

  .currency-switcher-sidenav select {
    padding: 7px 24px 7px 8px;
    font-size: 13px;
    height: 38px;
  }

  .currency-switcher-sidenav label {
    font-size: 11px;
    margin-bottom: 5px;
  }
}

@media (max-width: 480px) {
  .currency-switcher-sidenav {
    margin: 10px 10px 10px 10px;
  }

  .currency-switcher-sidenav select {
    padding: 6px 22px 6px 8px;
    font-size: 12px;
    height: 36px;
  }
}
</style>

<!--Not Sidenav-->
<div class="top-header">
  <div class="show-btn" onclick="openNav()" style="font-size: 45px; margin: 20px 0 0 20px; cursor: pointer; display: inline-block; position: absolute; left: 0; color: #fff; z-index: 100">
    <span class="header-toggler-icon"></span>
  </div>

  <?php
    if (session('uid_tmp')) {
  ?>
  <div class="notifcation m-r-10" style="font-size: 25px; margin: 18px 0 0 310px; cursor: pointer; display: inline-block; position: absolute; color: #fff; z-index: 100">
    <a href="<?=cn("blocks/back_to_admin")?>" data-toggle="tooltip" data-placement="bottom" title="<?=lang('Back_to_Admin')?>" class="text-white ajaxBackToAdmin">
      <i class="fe fe-log-out"></i>
    </a>
  </div>
  <?php } ?>

  <div class="card-title text-center" data-aos="fade-down" data-aos-easing="ease-in" data-aos-delay="1000">
    <div class="site-logo">
      <a href="<?=cn('order/add')?>">
        <img src="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>" alt="website-logo" style="max-height: 40px; margin-top:15px;">
      </a>
    </div>
  </div>
</div>

<div id="overlay" onclick="closeNav()"></div>
<div id="closeBtn" onclick="closeNav()">&times;</div>
<div class="sidenav" id="mySidenav">
  <div class="sidenavHeader" style="color: #fff;">
    <h4><?=lang("Hi")?>, <span class="text-uppercase"><?php _echo(get_field(USERS, ["id" => session('uid')], 'first_name'))?></span></h4>
    <h6>
      <?php
        if (!get_role("admin")) {
          $balance = get_field(USERS, ["id" => session('uid')], 'balance');

          switch (get_option('currency_decimal_separator', 'dot')) {
            case 'dot':
              $decimalpoint = '.';
              break;
            case 'comma':
              $decimalpoint = ',';
              break;
            default:
              $decimalpoint = '';
              break;
          } 

          switch (get_option('currency_thousand_separator', 'comma')) {
            case 'dot':
              $separator = '.';
              break;
            case 'comma':
              $separator = ',';
              break;
            case 'space':
              $separator = ' ';
              break;
            default:
              $separator = '';
              break;
          }
          if (empty($balance) || $balance == 0) {
            $balance = 0.0000;
          }else{
            $balance = convert_currency($balance);
            $balance = currency_format($balance, get_option('currency_decimal', 2), $decimalpoint, $separator);
          }
          
          $current_currency = get_current_currency();
          $currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol',"$");
      ?>
      <?=lang("Balance")?>: <span id="balanceDisplay"><?=$currency_symbol?><?=$balance?></span>
      <?php }else{?> 
        <?=lang("Admin_account")?>
      <?php }?> 
    </h6>
  </div>

  <!-- Currency Switcher Inside Sidenav - Above Dashboard Link -->
  <div class="currency-switcher-sidenav">
    <label for="currencySelectorSidenav"><?=lang("Currency")?></label>
    <div class="currency-select-wrapper">
      <select id="currencySelectorSidenav" class="form-control" aria-label="<?=lang('Select_currency')?>">
        <?php
          $current_currency = get_current_currency();
          $currencies = get_active_currencies();
          if (!empty($currencies)) {
            foreach ($currencies as $currency) {
        ?>
        <option value="<?=$currency->code?>" <?=($current_currency && $current_currency->code == $currency->code) ? 'selected' : ''?>>
          <?=$currency->symbol?> - <?=$currency->code?>
        </option>
        <?php
            }
          }
        ?>
      </select>
    </div>
  </div>

  <div class="currency-switcher-divider"></div>

  <!--Below SideNavHeader-->
  <div id="main-container">
    <ul class="nav-tabs">
      <?php
      session_start();
      $user_id = $_SESSION['uid'];

      if (!function_exists('get_role')) {
          function get_role($role) {
              $user_roles = $_SESSION['roles'];
              return in_array($role, $user_roles);
          }
      }
      ?>

      <!-- Sidebar link for Dashboard -->
      <?php if (get_role('admin')): ?>
        <a style="margin-top: 2px;" href="<?= cn('statistics') ?>" class="nav-link <?=(segment(1) == 'statistics') ? "active" : ""?>">
          <div class="nav-item title" class="sidenavContent">
            <i class="fe fe-bar-chart-2"></i><?= lang("Dashboard") ?>
          </div>
        </a>
      <?php endif; ?>

      <a href="<?=cn('order/add')?>" class="nav-link <?=(segment(1) == 'order' && segment(2) == 'add') ? "active" : ""?>" style="margin-top: 5px;">
        <div class="nav-item" class="sidenavContent">
          <i class="fe fe-shopping-cart"></i><?= lang("New_order") ?>
        </div>
      </a>

      <a href="<?=cn('order/log')?>" class="nav-link <?=(segment(1) == 'order' && segment(2) == 'log')?"active":""?>">
        <div class="nav-item" class="sidenavContent"><i class="fa fa-shopping-cart"></i><?=lang("Orders")?></div>
      </a>
      
      <a href="<?=cn('refill/log')?>" class="nav-link <?=(segment(1) == 'order' && segment(2) == 'refill')?"active":""?>">
        <div class="nav-item" class="sidenavContent"><i class="fa fa-recycle"></i><?=lang("Refill")?></div>
      </a>
      
      <?php if (get_role("admin") || get_role("supporter")) { ?>
        <a href="<?=cn('category')?>" class="nav-link <?=(segment(1) == 'category')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-table"></i><?=lang("Category")?></div>
        </a>
      <?php } ?>

      <a href="<?=cn('services')?>" class="nav-link <?=(segment(1) == 'services')?"active":""?>">
        <div class="nav-item" class="sidenavContent"><i class="fe fe-list"></i><?=lang('Services')?></div>
      </a>
      
      <?php if (get_role("user")) { ?>
        <a href="<?=cn('add_funds')?>" class="nav-link <?=(segment(1) == 'add_funds')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-money"></i><?=lang("Add_funds")?></div>
        </a>
      <?php } ?>
      
      <?php if (get_role("admin")) { ?>
        <a href="<?=cn('add_funds')?>" class="nav-link <?=(segment(1) == 'add_funds')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-money"></i><?=lang("Add_funds")?></div>
        </a>
      <?php } ?>
      
      <?php if (get_option('enable_api_tab') && !get_role("admin")) { ?>      
        <a href="<?=cn('api/docs')?>" class="nav-link <?=(segment(2) == 'docs')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fe fe-share-2"></i><?=lang("API")?></div>
        </a>
      <?php } ?>
      
      <?php if (get_role("user")) { ?>   
        <a href="<?=cn('tickets')?>" class="nav-link <?=(segment(1) == 'tickets')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-comments-o"></i><?=lang("Tickets")?>&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-info"><?=$total_unread_tickets?></span></div>
        </a>
      <?php } else { ?>
        <a href="<?=cn('tickets')?>" class="nav-link <?=(segment(1) == 'tickets') ? "active": ""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-comments-o"></i><?=lang("tickets")?>&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-info"><?=$total_unread_tickets?></span></div>
        </a>
      <?php } ?>
      
      <?php if(get_option("enable_affiliate") == "1"){ ?>
        <a href="<?=cn('affiliate')?>" class="nav-link <?=(segment(1) == 'affiliate')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-money"></i><?=lang("Affiliate")?></div>
        </a>
      <?php } ?>
      
      <?php if(get_option("is_childpanel_status") == "1"){ ?>
        <a href="<?=cn('childpanel/add')?>" class="nav-link <?=(segment(1) == 'childpanel')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-child"></i><?=lang("Child_Panel")?></div>
        </a>
      <?php } ?>
      
      <a href="<?=cn('transactions')?>" class="nav-link <?=(segment(1) == 'transactions')?"active":""?>">
        <div class="nav-item" class="sidenavContent"><i class="fe fe-calendar"></i><?=lang("Transaction_logs")?></div>
      </a>

      <a href="<?=cn('balance_logs')?>" class="nav-link <?=(segment(1) == 'balance_logs')?"active":""?>">
            <div class="nav-item" class="sidenavContent"><i class="fe fe-activity"></i><?=lang("Balance_Logs")?></div>
          </a>
          
      
      <?php if(get_role("admin") || get_role("supporter")){ ?>
        <div class="sidenavContentHeader">Admin Role</div>
        <a href="<?=cn('users')?>" class="nav-link <?=(segment(1) == 'users')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fe fe-users"></i><?=lang("Users")?></div>
        </a>

        <a style="margin-top: 2px;" href="<?= cn('whatsapp_listed_updated.php') ?>" class="nav-link <?=(segment(1) == 'whatsapp_listed_updated') ? "active" : ""?>">
          <div class="nav-item title" class="sidenavContent">
            <i class="fe fe-users"></i>WA Number Updates
          </div>
        </a>
        
        <a href="<?=cn('subscribers')?>" class="nav-link <?=(segment(1) == 'subscribers')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-user-circle-o"></i><?php echo lang("subscribers"); ?></div>
        </a>
        
        <a href="<?=cn('user_mail_logs')?>" class="nav-link <?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-envelope"></i><?=lang("User_Mail_Logs")?></div>
        </a>
        
        <a href="<?=cn('user_logs')?>" class="nav-link <?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-sort"></i><?=lang("user_activity_logs")?></div>
        </a>
        
        <a href="<?=cn('user_block_ip')?>" class="nav-link <?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-ban"></i><?=lang("banned_ip_address")?></div>
        </a>
      <?php } ?>
      
      <?php if(get_role("admin") || get_role("supporter")){ ?>
        <div class="sidenavContentHeader">Settings</div>
        <a href="<?=cn('setting')?>" class="nav-link <?=(segment(1) == 'setting')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-cog"></i><?=lang("System_Settings")?></div>
        </a>
        
        <a href="<?=cn('api_provider')?>" class="nav-link <?=(segment(1) == 'api_provider')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-share-alt"></i><?=lang("Services_Providers")?></div>
        </a>
        
        <a href="<?=cn('payments')?>" class="nav-link <?=(segment(1) == 'payments')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-credit-card"></i><?=lang("Payments")?></div>
        </a>
        
        <a href="<?=cn('payments_bonuses')?>" class="nav-link <?=(segment(1) == 'payments_bonuses')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-money"></i><?=lang("Payments_Bonuses")?></div>
        </a>
        
        <a href="<?=cn('cron_logs')?>" class="nav-link <?=(segment(1) == 'cron_logs')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-clock-o"></i>Cron Logs</div>
        </a>
      <?php } ?>
      
      <?php if(get_role("admin")){ ?>
        <div class="sidenavContentHeader">Others</div>
        <a href="<?=cn('news')?>" class="nav-link <?=(segment(1) == 'news')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-bell"></i><?=lang("Announcement")?></div>
        </a>
        
        <a href="<?=cn('faqs')?>" class="nav-link <?=(segment(1) == 'faqs')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-book"></i>FAQs</div>
        </a>
        
        <a href="<?=cn('language')?>" class="nav-link <?=(segment(1) == 'language')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-language"></i><?=lang("Language")?></div>
        </a>
        
        <a href="https://codewithali.online" target="_blank" class="nav-link <?=(segment(1) == 'hqsmmscripts')?"active":""?>">
          <div class="nav-item" class="sidenavContent"><i class="fa fa-diamond"></i><?=lang("Modules_&_Scripts")?></div>
        </a>
      <?php } ?>
      
      <a href="<?=cn('profile')?>" class="nav-link <?=(segment(1) == 'profile')?"active":""?>">
        <div class="nav-item" class="sidenavContent"><i class="fa fa-user"></i><?=lang("Account")?></div>
      </a>
      
      <a href="<?=cn("auth/logout")?>" class="nav-link <?=(segment(1) == 'auth/logout')?"active":""?>">
        <div class="nav-item" class="sidenavContent"><i class="fa fa-power-off"></i><?=lang("Sign_Out")?></div>
      </a>
      <br><br><br><br><br><br>
    </ul>
  </div>
</div>

<script>
// Sidenav currency switcher handler
$(document).ready(function() {
  $('#currencySelectorSidenav').on('change', function() {
    var selectedCurrency = $(this).val();

    // Show loading overlay
    if ($('#page-overlay').length) {
      $('#page-overlay').addClass('visible incoming');
    }

    $.ajax({
      url: '<?=cn("currencies/set_currency")?>',
      type: 'POST',
      data: {
        currency_code: selectedCurrency,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success' || response.success === true) {
          setTimeout(function(){ 
            location.reload(); 
          }, 500);
        } else {
          alert(response.message || 'Failed to change currency');
          if ($('#page-overlay').length) {
            $('#page-overlay').removeClass('visible incoming');
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('An error occurred while changing currency. Please try again.');
        if ($('#page-overlay').length) {
          $('#page-overlay').removeClass('visible incoming');
        }
      }
    });
  });
});

// Existing navigation functions
function openNav() {
  document.getElementById("mySidenav").style.width = "280px";
  document.getElementById("overlay").style.display = "block";
  document.getElementById("closeBtn").style.display = "block";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("overlay").style.display = "none";
  document.getElementById("closeBtn").style.display = "none";
}
</script>

<?php
if (get_option("enable_news_announcement") == 1) {
?>
  <a href="<?=cn("news/ajax_notification")?>" style="position: fixed; bottom: 8px; right: 8px; font-size: 20px; padding-top: 3px; text-align: center; z-index: 10000000;" data-toggle="tooltip" data-placement="bottom" title="News & Announcement" class="ajaxModal text-white">
    <div class="bell-fix">
      <i class="fa fa-bell"></i>
      <div class="test">
        <span class="nav-unread <?=(isset($_COOKIE["news_annoucement"]) && $_COOKIE["news_annoucement"] == "clicked") ? "" : "change_color"?>"></span>
      </div>
    </div>
  </a>
<?php }?>

<?php
if (get_option("enable_news_announcement") == 1) {
?>
<a href="https://wa.me/<?=get_option('whatsapp_number')?>/?text=Hello, I have a question" 
   style="position: fixed; bottom: 8px; left: 8px; font-size: 24px; padding-top: 3px; text-align: center; z-index: 10000000;" 
   title="Chat" class="text-white">
  <div class="custom-whatsapp-button">
    <img src="<?php echo BASE; ?>assets/images/whatsapp.png" alt="WhatsApp" class="custom-whatsapp-icon">
  </div>
</a>

<style>
  .custom-whatsapp-button {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 40px;
    margin-top: 10px;
  }

  .custom-whatsapp-icon {
    width: 100%;
    height: 100%;
  }
</style>
<?php }?>