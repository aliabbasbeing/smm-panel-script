<!-- New UL List Based Header Navigation -->
<header class="header-section">
  <nav class="main-nav">
    <div class="header-top">
      <!-- Logo with Balance -->
      <div class="logo">
        <a href="<?=cn('order/add')?>">
          <img src="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>" alt="website-logo">
        </a>
        <!-- User Balance Display -->
        <div class="user-balance">
          <?php
            if (!get_role("admin")) {
              $balance = get_field(USERS, ["id" => session('uid')], 'balance');
              switch (get_option('currency_decimal_separator', 'dot')) {
                case 'dot': $decimalpoint = '.'; break;
                case 'comma': $decimalpoint = ','; break;
                default: $decimalpoint = ''; break;
              }
              switch (get_option('currency_thousand_separator', 'comma')) {
                case 'dot': $separator = '.'; break;
                case 'comma': $separator = ','; break;
                case 'space': $separator = ' '; break;
                default: $separator = ''; break;
              }
              if (empty($balance) || $balance == 0) {
                $balance = 0.0000;
              } else {
                $balance = convert_currency($balance);
                $balance = currency_format($balance, get_option('currency_decimal', 2), $decimalpoint, $separator);
              }
              $current_currency = get_current_currency();
              $currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol',"$");
          ?>
            <?=lang("Balance")?>: <span id="balanceDisplay" class="currency-text"><?=$currency_symbol?><?=$balance?></span>
          <?php } else { ?>
            <?=lang("Admin_account")?>
          <?php } ?>
        </div>
      </div>

      <!-- Mobile Menu Toggle -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
        <i class="fe fe-menu"></i>
      </button>
    </div>

    <!-- Main Navigation Menu as UL List -->
    <ul class="menu" id="mainMenu">
      <?php
      if (!isset($_SESSION)) {
        session_start();
      }
      $user_id = $_SESSION['uid'] ?? null;

      if (!function_exists('get_role')) {
        function get_role($role) {
          $user_roles = $_SESSION['roles'] ?? [];
          return in_array($role, $user_roles);
        }
      }
      ?>

      <!-- Dashboard (Admin Only) -->
      <?php if (get_role('admin')): ?>
        <li class="menu-item <?=(segment(1) == 'statistics') ? 'active' : ''?>">
          <a href="<?=cn('statistics')?>" class="menu-link">
            <i class="fe fe-bar-chart-2"></i> <?=lang("Dashboard")?>
          </a>
        </li>
      <?php endif; ?>

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

      <!-- Tickets -->
      <li class="menu-item <?=(segment(1) == 'tickets') ? 'active' : ''?>">
        <a href="<?=cn('tickets')?>" class="menu-link">
          <i class="fa fa-comments-o"></i> <?=lang("Tickets")?>
          <?php if(isset($total_unread_tickets) && $total_unread_tickets > 0): ?>
            <span class="badge badge-info"><?=$total_unread_tickets?></span>
          <?php endif; ?>
        </a>
      </li>

      <!-- Transactions -->
      <li class="menu-item <?=(segment(1) == 'transactions') ? 'active' : ''?>">
        <a href="<?=cn('transactions')?>" class="menu-link">
          <i class="fe fe-calendar"></i> <?=lang("Transaction_logs")?>
        </a>
      </li>

      <!-- Admin Section -->
      <?php if (get_role("admin") || get_role("supporter")) { ?>
        <!-- Users -->
        <li class="menu-item <?=(segment(1) == 'users') ? 'active' : ''?>">
          <a href="<?=cn('users')?>" class="menu-link">
            <i class="fe fe-users"></i> <?=lang("Users")?>
          </a>
        </li>

        <!-- Settings -->
        <li class="menu-item <?=(segment(1) == 'setting') ? 'active' : ''?>">
          <a href="<?=cn('setting')?>" class="menu-link">
            <i class="fa fa-cog"></i> <?=lang("System_Settings")?>
          </a>
        </li>
      <?php } ?>

      <!-- Account -->
      <li class="menu-item <?=(segment(1) == 'profile') ? 'active' : ''?>">
        <a href="<?=cn('profile')?>" class="menu-link">
          <i class="fa fa-user"></i> <?=lang("Account")?>
        </a>
      </li>

      <!-- Currency Dropdown -->
      <li class="menu-item currency-dropdown">
        <a href="javascript:void(0)" class="menu-link dropdown-toggle" id="currencyDropdownToggle">
          <i class="fe fe-dollar-sign"></i> 
          <span class="currency-text">
            <?php
              $current_currency = get_current_currency();
              echo $current_currency ? $current_currency->code : 'USD';
            ?>
          </span>
        </a>
        <ul class="currency-dropdown-menu" id="currencyDropdownMenu">
          <?php
            $currencies = get_active_currencies();
            if (!empty($currencies)) {
              foreach ($currencies as $currency) {
          ?>
          <li>
            <a href="javascript:void(0)" 
               class="dropdown-currency-item <?=($current_currency && $current_currency->code == $currency->code) ? 'active' : ''?>" 
               data-currency="<?=$currency->code?>">
              <?=$currency->symbol?> - <?=$currency->code?>
            </a>
          </li>
          <?php
              }
            }
          ?>
        </ul>
      </li>

      <!-- Logout -->
      <li class="menu-item">
        <a href="<?=cn("auth/logout")?>" class="menu-link">
          <i class="fa fa-power-off"></i> <?=lang("Sign_Out")?>
        </a>
      </li>
    </ul>
  </nav>
</header>

<script>
// Menu toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const mainMenu = document.getElementById('mainMenu');
  
  if (menuToggle && mainMenu) {
    menuToggle.addEventListener('click', function(e) {
      e.preventDefault();
      mainMenu.classList.toggle('show');
      this.classList.toggle('up');
    });
  }

  // Currency dropdown toggle
  const currencyToggle = document.getElementById('currencyDropdownToggle');
  const currencyDropdown = document.querySelector('.currency-dropdown');
  
  if (currencyToggle && currencyDropdown) {
    currencyToggle.addEventListener('click', function(e) {
      e.preventDefault();
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

  // Currency selection handler
  const currencyItems = document.querySelectorAll('.dropdown-currency-item');
  currencyItems.forEach(function(item) {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const selectedCurrency = this.getAttribute('data-currency');
      
      // Show loading overlay if available
      const pageOverlay = document.getElementById('page-overlay');
      if (pageOverlay) {
        pageOverlay.classList.add('visible', 'incoming');
      }

      // Make AJAX request to change currency
      fetch('<?=cn("currencies/set_currency")?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          'currency_code': selectedCurrency,
          '<?=$this->security->get_csrf_token_name()?>': '<?=$this->security->get_csrf_hash()?>'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success' || data.success === true) {
          setTimeout(function() {
            location.reload();
          }, 500);
        } else {
          alert(data.message || 'Failed to change currency');
          if (pageOverlay) {
            pageOverlay.classList.remove('visible', 'incoming');
          }
        }
      })
      .catch(error => {
        console.error('AJAX Error:', error);
        alert('An error occurred while changing currency. Please try again.');
        if (pageOverlay) {
          pageOverlay.classList.remove('visible', 'incoming');
        }
      });
    });
  });
});
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
<?php } ?>

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
<?php } ?>