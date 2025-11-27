
<div class="sidebar o-auto">

  <!-- GENERAL SETTINGS SECTION -->
  <div class="sidebar-section">
    <div class="sidebar-header">
      <h5>
        <i class="fe fe-disc"></i>
        <span><?=lang("general_settings")?></span>
      </h5>
    </div>
    
    <nav class="sidebar-nav">
      <a href="<?php echo cn($module."/website_setting")?>" class="sidebar-link <?php echo (segment(2) == 'website_setting') ? 'active' : ''?>">
        <i class="fe fe-globe"></i>
        <span><?=lang("website_setting")?></span>
      </a>

      <a href="<?php echo cn($module."/website_logo")?>" class="sidebar-link <?php echo (segment(2) == 'website_logo') ?  'active' : ''?>">
        <i class="fe fe-image"></i>
        <span><?=lang("Logo")?></span>
      </a>

      <a href="<?php echo cn($module."/cookie_policy")?>" class="sidebar-link <?php echo (segment(2) == 'cookie_policy') ? 'active' : ''?>">
        <i class="fe fe-bookmark"></i>
        <span><?php echo lang("cookie_policy");?></span>
      </a>

      <a href="<?php echo cn($module."/terms_policy")?>" class="sidebar-link <?php echo (segment(2) == 'terms_policy') ?  'active' : ''?>">
        <i class="fe fe-award"></i>
        <span><?=lang("terms__policy_page")?></span>
      </a>

      <a href="<?php echo cn($module."/default")?>" class="sidebar-link <?php echo (segment(2) == 'default') ? 'active' : ''?>">
        <i class="fe fe-box"></i>
        <span><?=lang("default_setting")?></span>
      </a>

      <a href="<?php echo cn($module. "/currency")?>" class="sidebar-link <?php echo (segment(2) == 'currency') ? 'active' : ''?>">
        <i class="fe fe-dollar-sign"></i>
        <span><?=lang("currency_setting")?></span>
      </a>

      <a href="<?php echo cn($module. "/currencies")?>" class="sidebar-link <?php echo (segment(2) == 'all_currencies') ? 'active' : ''?>">
        <i class="fe fe-dollar-sign"></i>
        <span><?=lang("all_currencies")?></span>
      </a>

      <a href="<?php echo cn($module."/child-panel")?>" class="sidebar-link <?php echo (segment(2) == 'child-panel') ? 'active' : ''?>">
        <i class="fa fa-child"></i>
        <span><?=lang("child_panel")?></span>
      </a>

      <a href="<?php echo cn($module."/affiliate")?>" class="sidebar-link <?php echo (segment(2) == 'affiliate') ? 'active' : ''?>">
        <i class="fa fa-users"></i>
        <span><?=lang("Affiliate")?></span>
      </a>

      <a href="<?php echo cn($module. "/modules")?>" class="sidebar-link <?php echo (segment(2) == 'modules') ? 'active' : ''?>">
        <i class="fa fa-question-circle"></i>
        <span><?=lang("Modules")?></span>
      </a>

      <a href="<?php echo cn($module. "/code_parts")?>" class="sidebar-link <?php echo (segment(2) == 'code_parts') ? 'active' : ''?>">
        <i class="fe fe-code"></i>
        <span>Code Parts</span>
      </a>

      <a href="<?php echo cn($module."/rules_updates")?>" class="sidebar-link <?php echo (segment(2) == 'rules_updates') ?  'active' : ''?>">
        <i class="fa fa-info-circle"></i>
        <span>Rules & Updates</span>
      </a>

      <a href="<?php echo cn($module. "/other")?>" class="sidebar-link <?php echo (segment(2) == 'other') ? 'active' : ''?>">
        <i class="fe fe-command"></i>
        <span><?=lang("Other")?></span>
      </a>
    </nav>
  </div>

  <!-- EMAIL SECTION -->
  <div class="sidebar-section">
    <div class="sidebar-header">
      <h5>
        <i class="fe fe-disc"></i>
        <span><?=lang("Email")?></span>
      </h5>
    </div>
    
    <nav class="sidebar-nav">
      <a href="<?php echo cn($module."/smtp")?>" class="sidebar-link <?php echo (segment(2) == 'smtp') ? 'active' : ''?>">
        <i class="fe fe-mail"></i>
        <span><?=lang("email_setting")?></span>
      </a>

      <a href="<?php echo cn($module."/template")?>" class="sidebar-link <?php echo (segment(2) == 'template') ? 'active' : ''?>">
        <i class="fe fe-file-text"></i>
        <span><?=lang("email_template")?></span>
      </a>
    </nav>
  </div>

  <!-- INTEGRATIONS SECTION -->
  <div class="sidebar-section">
    <div class="sidebar-header">
      <h5>
        <i class="fe fe-disc"></i>
        <span><?=lang("integrations")?></span>
      </h5>
    </div>
    
    <nav class="sidebar-nav">
      <a href="<?php echo cn($module."/payment")?>" class="sidebar-link <?php echo (segment(2) == 'payment') ? 'active' : ''?>">
        <i class="fe fe-alert-triangle"></i>
        <span><?=lang("Payment")?></span>
      </a>
      
      <a href="<?php echo cn($module."/google_oauth")?>" class="sidebar-link <?php echo (segment(2) == 'google_oauth') ?  'active' : ''?>">
        <i class="fe fe-lock"></i>
        <span><?=lang("Google OAuth")?></span>
      </a>
      
      <a href="<?php echo cn($module."/whatsapp_notifications")?>" class="sidebar-link <?php echo (segment(2) == 'whatsapp_notifications') ? 'active' : ''?>">
        <i class="fa fa-whatsapp"></i>
        <span>WhatsApp Notifications</span>
      </a>
    </nav>
  </div>

</div>