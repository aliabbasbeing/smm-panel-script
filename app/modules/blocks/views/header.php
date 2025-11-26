<header class="header-section">
  <nav class="main-nav">
    <!-- Top Section (Lighter Blue) -->
    <div class="header-top">
      <!-- Logo and Balance Section -->
      <div class="logo">
        <a href="<?=cn('order/add')?>">
          <img src="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>" alt="website-logo">
        </a>
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
            $currency_code = $current_currency ? $current_currency->code : 'USD';
        ?>
        
        <?php } ?>
      </div>

      <!-- Right Side: Currency Dropdown & Menu Toggle -->
      <div class="header-right">

        <div class="currency-dropdown">
          <div class="dropdown-toggle" id="currencyDropdownToggle">
            <div class="currency-balance-info">
              <span class="currency-code"><?=$currency_code?></span>
              <?php
$cleanBalance = preg_replace('/[^0-9.]/', '', $balance); // remove anything not number or dot
$cleanBalance = sprintf('%.2f', $cleanBalance);
?>
<span class="currency-balance">
  <?=$currency_symbol?><?=$cleanBalance?>
</span>

            </div>
            <i class="fa fa-angle-down"></i>
          </div>
          <ul class="currency-dropdown-menu" id="currencyDropdownMenu">
            <?php
              $currencies = get_active_currencies();
              if (!empty($currencies)) {
                foreach ($currencies as $currency) {
                  $isActive = ($current_currency && $current_currency->code == $currency->code) ? 'active' : '';
            ?>
            <li>
              <a href="#" class="dropdown-currency-item <?=$isActive?>" data-currency="<?=$currency->code?>">
                <?=$currency->symbol?> - <?=$currency->code?>
              </a>
            </li>
            <?php
                }
              }
            ?>
          </ul>
        </div>


        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" id="menuToggle">
          <i class="fa fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Menu Section (Darker Blue) -->
    <div class="menu-wrapper">
      <ul class="menu" id="menu">
        <?php
        if (!function_exists('get_role')) {
            function get_role($role) {
                if (!isset($_SESSION['roles'])) return false;
                $user_roles = $_SESSION['roles'];
                return in_array($role, $user_roles);
            }
        }
        ?>

        <!-- Dashboard (Admin only) -->
       
          <li class="menu-item <?=(segment(1) == 'statistics') ? 'active' : ''?>">
            <a href="<?=cn('statistics')?>" class="menu-link">
              <i class="fe fe-bar-chart-2"></i> <?=lang("Dashboard")?>
            </a>
          </li>

        <!-- New Order -->
        <li class="menu-item <?=(segment(1) == 'order' && segment(2) == 'add') ? 'active' : ''?>">
          <a href="<?=cn('order/add')?>" class="menu-link">
            <i class="fe fe-shopping-cart"></i> <?=lang("New_order")?>
          </a>
        </li>

        <!-- Orders -->
        <li class="menu-item <?=(segment(1) == 'order' && segment(2) == 'log') ? 'active' : ''?>">
          <a href="<?=cn('order/log')?>" class="menu-link">
            <i class="fa fa-shopping-cart"></i> <?=lang("Orders")?>
          </a>
        </li>

        <!-- Refill -->
        <li class="menu-item <?=(segment(1) == 'order' && segment(2) == 'refill') ? 'active' : ''?>">
          <a href="<?=cn('refill/log')?>" class="menu-link">
            <i class="fa fa-recycle"></i> <?=lang("Refill")?>
          </a>
        </li>

        <!-- Category (Admin/Supporter) -->
        <?php if (get_role("admin") || get_role("supporter")) { ?>
          <li class="menu-item <?=(segment(1) == 'category') ? 'active' : ''?>">
            <a href="<?=cn('category')?>" class="menu-link">
              <i class="fa fa-table"></i> <?=lang("Category")?>
            </a>
          </li>
        <?php } ?>

        <!-- Services -->
        <li class="menu-item <?=(segment(1) == 'services') ? 'active' : ''?>">
          <a href="<?=cn('services')?>" class="menu-link">
            <i class="fe fe-list"></i> <?=lang('Services')?>
          </a>
        </li>

        <!-- Add Funds -->
        <?php if (get_role("user") || get_role("admin")) { ?>
          <li class="menu-item <?=(segment(1) == 'add_funds') ? 'active' : ''?>">
            <a href="<?=cn('add_funds')?>" class="menu-link">
              <i class="fa fa-money"></i> <?=lang("Add_funds")?>
            </a>
          </li>
        <?php } ?>

        <!-- API (if enabled and not admin) -->
        <?php if (get_option('enable_api_tab') && !get_role("admin")) { ?>      
          <li class="menu-item <?=(segment(2) == 'docs') ? 'active' : ''?>">
            <a href="<?=cn('api/docs')?>" class="menu-link">
              <i class="fe fe-share-2"></i> <?=lang("API")?>
            </a>
          </li>
        <?php } ?>

        <!-- Tickets -->
        <li class="menu-item <?=(segment(1) == 'tickets') ? 'active' : ''?>">
          <a href="<?=cn('tickets')?>" class="menu-link">
            <i class="fa fa-comments-o"></i> <?=lang("Tickets")?>
            <?php if(isset($total_unread_tickets) && $total_unread_tickets > 0): ?>
              <span class="badge badge-info"><?=$total_unread_tickets?></span>
            <?php endif; ?>
          </a>
        </li>

        <!-- Affiliate (if enabled) -->
        <?php if(get_option("enable_affiliate") == "1"){ ?>
          <li class="menu-item <?=(segment(1) == 'affiliate') ? 'active' : ''?>">
            <a href="<?=cn('affiliate')?>" class="menu-link">
              <i class="fa fa-money"></i> <?=lang("Affiliate")?>
            </a>
          </li>
        <?php } ?>

        <!-- Child Panel (if enabled) -->
        <?php if(get_option("is_childpanel_status") == "1"){ ?>
          <li class="menu-item <?=(segment(1) == 'childpanel') ? 'active' : ''?>">
            <a href="<?=cn('childpanel/add')?>" class="menu-link">
              <i class="fa fa-child"></i> <?=lang("Child_Panel")?>
            </a>
          </li>
        <?php } ?>

        <!-- Transactions -->
        <li class="menu-item <?=(segment(1) == 'transactions') ? 'active' : ''?>">
          <a href="<?=cn('transactions')?>" class="menu-link">
            <i class="fe fe-calendar"></i> <?=lang("Transaction_logs")?>
          </a>
        </li>

        <!-- Balance Logs -->
        <li class="menu-item <?=(segment(1) == 'balance_logs') ? 'active' : ''?>">
          <a href="<?=cn('balance_logs')?>" class="menu-link">
            <i class="fe fe-activity"></i> <?=lang("Balance_Logs")?>
          </a>
        </li>

        <!-- Admin Section -->
        <?php if(get_role("admin") || get_role("supporter")){ ?>
          <li class="menu-item <?=(segment(1) == 'users') ? 'active' : ''?>">
            <a href="<?=cn('users')?>" class="menu-link">
              <i class="fe fe-users"></i> <?=lang("Users")?>
            </a>
          </li>

          <li class="menu-item <?=(segment(1) == 'subscribers') ? 'active' : ''?>">
            <a href="<?=cn('subscribers')?>" class="menu-link">
              <i class="fa fa-user-circle-o"></i> <?=lang("subscribers")?>
            </a>
          </li>

          <li class="menu-item <?=(segment(1) == 'setting') ? 'active' : ''?>">
            <a href="<?=cn('setting')?>" class="menu-link">
              <i class="fa fa-cog"></i> <?=lang("System_Settings")?>
            </a>
          </li>

          <li class="menu-item <?=(segment(1) == 'api_provider') ? 'active' : ''?>">
            <a href="<?=cn('api_provider')?>" class="menu-link">
              <i class="fa fa-share-alt"></i> <?=lang("Services_Providers")?>
            </a>
          </li>

          <li class="menu-item <?=(segment(1) == 'payments') ? 'active' : ''?>">
            <a href="<?=cn('payments')?>" class="menu-link">
              <i class="fa fa-credit-card"></i> <?=lang("Payments")?>
            </a>
          </li>
        <?php } ?>

        <!-- Admin Only -->
        <?php if(get_role("admin")){ ?>
          <li class="menu-item <?=(segment(1) == 'news') ? 'active' : ''?>">
            <a href="<?=cn('news')?>" class="menu-link">
              <i class="fa fa-bell"></i> <?=lang("Announcement")?>
            </a>
          </li>

          <li class="menu-item <?=(segment(1) == 'faqs') ? 'active' : ''?>">
            <a href="<?=cn('faqs')?>" class="menu-link">
              <i class="fa fa-book"></i> FAQs
            </a>
          </li>

          <li class="menu-item <?=(segment(1) == 'language') ? 'active' : ''?>">
            <a href="<?=cn('language')?>" class="menu-link">
              <i class="fa fa-language"></i> <?=lang("Language")?>
            </a>
          </li>
        <?php } ?>

        <!-- Account -->
        <li class="menu-item <?=(segment(1) == 'profile') ? 'active' : ''?>">
          <a href="<?=cn('profile')?>" class="menu-link">
            <i class="fa fa-user"></i> <?=lang("Account")?>
          </a>
        </li>

        <!-- Sign Out -->
        <li class="menu-item">
          <a href="<?=cn("auth/logout")?>" class="menu-link">
            <i class="fa fa-power-off"></i> <?=lang("Sign_Out")?>
          </a>
        </li>
      </ul>
    </div>
  </nav>
</header>

<script>
// Mobile Menu Toggle and Currency Dropdown
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const menu = document.getElementById('menu');

  if (menuToggle && menu) {
    menuToggle.addEventListener('click', function() {
      menu.classList.toggle('show');
      menuToggle.classList.toggle('up');
    });
  }

  // Currency Dropdown
  const currencyToggle = document.getElementById('currencyDropdownToggle');
  const currencyDropdown = document.querySelector('.currency-dropdown');
  
  if (currencyToggle && currencyDropdown) {
    currencyToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      currencyDropdown.classList.toggle('dropdown-show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!currencyDropdown.contains(e.target)) {
        currencyDropdown.classList.remove('dropdown-show');
      }
    });
  }

  // Currency Selection
  const currencyItems = document.querySelectorAll('.dropdown-currency-item');
  currencyItems.forEach(function(item) {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      var selectedCurrency = this.getAttribute('data-currency');

      // Show loading overlay
      if (document.getElementById('page-overlay')) {
        document.getElementById('page-overlay').classList.add('visible', 'incoming');
      }

      // AJAX call to set currency
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
            if (document.getElementById('page-overlay')) {
              document.getElementById('page-overlay').classList.remove('visible', 'incoming');
            }
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert('An error occurred while changing currency. Please try again.');
          if (document.getElementById('page-overlay')) {
            document.getElementById('page-overlay').classList.remove('visible', 'incoming');
          }
        }
      });
    });
  });
});
</script>

<?php if (get_option("enable_news_announcement") == 1) { ?>
  <a href="<?=cn("news/ajax_notification")?>" style="position: fixed; bottom: 8px; right: 8px; font-size: 20px; padding-top: 3px; text-align: center; z-index: 10000000;" data-toggle="tooltip" data-placement="bottom" title="News & Announcement" class="ajaxModal text-white">
    <div class="bell-fix">
      <i class="fa fa-bell"></i>
      <div class="test">
        <span class="nav-unread <?=(isset($_COOKIE["news_annoucement"]) && $_COOKIE["news_annoucement"] == "clicked") ? "" : "change_color"?>"></span>
      </div>
    </div>
  </a>
<?php } ?>

<?php if (get_option("enable_whatsapp_contact") == 1 && get_option('whatsapp_number')) { ?>
<a href="https://wa.me/<?=get_option('whatsapp_number')?>/?text=Hello, I have a question" 
   style="position: fixed; bottom: 8px; left: 8px; font-size: 24px; padding-top: 3px; text-align: center; z-index: 10000000;" 
   target="_blank"
   title="Chat on WhatsApp" class="text-white">
  <div class="custom-whatsapp-button">
    <img src="<?php echo BASE; ?>assets/images/whatsapp.png" alt="WhatsApp" class="custom-whatsapp-icon">
  </div>
</a>
<?php } ?>