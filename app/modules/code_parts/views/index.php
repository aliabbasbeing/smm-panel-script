<style>
/* Code Parts Navigation Styles */
.code-parts-nav {
  display: flex !important;
  flex-wrap: wrap !important;
  border-bottom: 2px solid #e0e0e0 !important;
  margin-bottom: 20px !important;
}

.code-parts-tab {
  margin-right: 5px !important;
  margin-bottom: -2px !important;
}

.code-parts-tab .nav-link {
  padding: 10px 15px !important;
  display: flex !important;
  align-items: center !important;
  border: 1px solid #ddd !important;
  border-bottom: 2px solid transparent !important;
  background: #fff !important;
  color: #333 !important;
  text-decoration: none !important;
  border-radius: 4px 4px 0 0 !important;
  transition: all 0.2s ease !important;
  cursor: pointer !important;
}

.code-parts-tab .nav-link:hover {
  background: #f5f5f5 !important;
  color: #1B78FC !important;
  border-color: #ddd !important;
}

.code-parts-tab .nav-link.active {
  background: #f8f8f8 !important;
  color: #1B78FC !important;
  border-color: #1B78FC !important;
  border-bottom-color: #f8f8f8 !important;
  font-weight: 600 !important;
}

.code-parts-tab .nav-link i {
  margin-right: 6px !important;
}

/* Tab Content Styles */
.tab-content > .tab-pane {
  display: none !important;
}

.tab-content > .tab-pane.active,
.tab-content > .tab-pane.show {
  display: block !important;
}

/* Editor Loading Indicator */
.editor-loading-indicator {
  text-align: center !important;
  padding: 20px !important;
  background: #f8f9fa !important;
  border-radius: 4px !important;
  margin-bottom: 10px !important;
}

.editor-loading-indicator i {
  color: #1B78FC !important;
}

/* Variables List Styles */
.variables-list {
  font-size: 12px !important;
}

.variables-list code {
  background: #e9ecef !important;
  padding: 2px 5px !important;
  border-radius: 3px !important;
  margin-right: 5px !important;
}

/* Container Styles */
.code-parts-container {
  padding: 15px !important;
}

/* Performance: Loading State */
.tab-pane:not(.active) .plugin_editor {
  min-height: 200px !important;
  background: #f8f9fa !important;
}

/* Advanced Settings Panel */
.advanced-settings-panel {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  padding: 15px;
  margin-bottom: 20px;
}

.advanced-settings-panel h6 {
  color: #495057;
  font-weight: 600;
  margin-bottom: 15px;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.settings-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-bottom: 10px;
}

.settings-group {
  flex: 1;
  min-width: 200px;
}

.settings-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 8px;
  color: #495057;
  font-size: 13px;
}

.checkbox-group {
  display: flex;
  gap: 15px;
  align-items: center;
  margin-top: 5px;
}

.checkbox-group label {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 0;
  cursor: pointer;
  font-weight: normal;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.settings-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  background: #fff;
  color: #495057;
  font-size: 14px;
}

.settings-group select:focus {
  border-color: #1B78FC;
  outline: none;
  box-shadow: 0 0 0 0.2rem rgba(27, 120, 252, 0.25);
}

/* Settings Icon */
.settings-icon {
  color: #1B78FC;
  margin-right: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .code-parts-nav {
    flex-direction: column !important;
  }

  .code-parts-tab {
    margin-right: 0 !important;
    margin-bottom: 5px !important;
  }

  .code-parts-tab .nav-link {
    width: 100% !important;
    border-radius: 4px !important;
  }
  
  .settings-row {
    flex-direction: column;
    gap: 10px;
  }
  
  .settings-group {
    min-width: 100%;
  }
}

</style>

<?php
// Helper function to get settings for a page key
function get_page_settings($page_key, $code_parts_settings) {
  if (isset($code_parts_settings[$page_key])) {
    return $code_parts_settings[$page_key];
  }
  // Return defaults if not found
  return [
    'device_visibility' => 'both',
    'display_position' => 'top',
    'show_on_mobile' => 1,
    'show_on_desktop' => 1,
  ];
}
?>

<div class="row m-t-5">
  <div class="col-sm-12 col-lg-12">
    <div class="card p-0">
      <div class="card-header d-flex align-items-center justify-content-between" style="border: 0.1px solid #1B78FC; border-radius: 3.5px 3.5px 0px 0px; background: #1B78FC;">
        <h4 class="card-title text-white m-0">
          <i class="fe fe-code"></i> <?=lang("Code Parts")?>
        </h4>
        <a href="<?=cn('setting/website_setting')?>" class="btn btn-outline-light btn-sm">
          <i class="fe fe-arrow-left"></i> <?=lang("Back to Settings")?>
        </a>
      </div>
      <div class="card-body code-parts-container">
        <div class="row">
          <div class="col-md-12 col-lg-12">
            <div class="tabs-list mb-4">
  <ul class="nav nav-tabs code-parts-nav" role="tablist">
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_dashboard" role="tab" aria-controls="code_dashboard">
        <i class="fas fa-home"></i> Dashboard
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_new_order" role="tab" aria-controls="code_new_order">
        <i class="fas fa-cart-plus"></i> New Order
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_orders" role="tab" aria-controls="code_orders">
        <i class="fas fa-list"></i> Order Logs
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_services" role="tab" aria-controls="code_services">
        <i class="fas fa-server"></i> Services
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_add_funds" role="tab" aria-controls="code_add_funds">
        <i class="fas fa-money-bill"></i> Add Funds
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_api" role="tab" aria-controls="code_api">
        <i class="fas fa-plug"></i> API
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_tickets" role="tab" aria-controls="code_tickets">
        <i class="fas fa-ticket"></i> Tickets
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_child_panel" role="tab" aria-controls="code_child_panel">
        <i class="fas fa-child"></i> Child Panel
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_transactions" role="tab" aria-controls="code_transactions">
        <i class="fas fa-arrow-right-arrow-left"></i> Transactions
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_signin" role="tab" aria-controls="code_signin">
        <i class="fas fa-right-to-bracket"></i> Sign In
      </a>
    </li>
    <li class="nav-item code-parts-tab">
      <a class="nav-link" href="#code_signup" role="tab" aria-controls="code_signup">
        <i class="fas fa-user-plus"></i> Sign Up
      </a>
    </li>
  </ul>
</div>


            <!-- Info Alerts -->
            <div class="alert alert-info">
              <i class="fe fe-info"></i> 
              <strong><?=lang("Info")?>:</strong> Use the HTML editor below to create styled HTML blocks for different pages. 
              HTML is sanitized for security - scripts, iframes, and event handlers are removed. Use inline CSS (style attribute) for styling.
            </div>
            
            <div class="alert alert-primary">
              <i class="fas fa-cog"></i> 
              <strong>Advanced Settings:</strong> Each code part now supports device visibility (mobile/desktop) and display position (top/bottom). 
              Configure these settings in the Advanced Settings panel above each editor.
            </div>
            
            <div class="alert alert-success">
              <i class="fe fe-zap"></i> 
              <strong>Performance Optimized:</strong> Editors are loaded on-demand when you switch tabs, ensuring fast page loading even with many code parts.
            </div>
            
            <div class="alert alert-success">
              <i class="fe fe-code"></i> 
              <strong>Supported Variables:</strong>
              <div class="variables-list mt-2">
                <strong>User:</strong> 
                <code>{{user.balance}}</code>
                <code>{{user.name}}</code>
                <code>{{user.email}}</code>
                <code>{{user.orders}}</code>
                <code>{{user.spent}}</code>
                <code>{{user.pending_orders}}</code>
                <code>{{user.completed_orders}}</code>
                <code>{{user.tickets}}</code>
                <br><br>
                <strong>Site:</strong>
                <code>{{site.name}}</code>
                <code>{{site.url}}</code>
                <code>{{site.currency}}</code>
                <code>{{site.currency_code}}</code>
                <br><br>
                <strong>Date:</strong>
                <code>{{date.today}}</code>
                <code>{{date.now}}</code>
                <code>{{date.year}}</code>
              </div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content mt-3">
              
              <!-- Dashboard Page Code Part -->
              <div id="code_dashboard" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="dashboard">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-bar-chart-2"></i> <?=lang("dashboard")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('dashboard', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('dashboard'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- New Order Page Code Part -->
              <div id="code_new_order" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="new_order">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-shopping-cart"></i> <?=lang("New_Order")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('new_order', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('new_order'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Order Logs Page Code Part -->
              <div id="code_orders" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="orders">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fas fa-shopping-cart"></i> <?=lang("Orders")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('orders', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('orders'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Services Page Code Part -->
              <div id="code_services" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="services">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-list"></i> <?=lang("Services")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('services', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('services'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Add Funds Page Code Part -->
              <div id="code_add_funds" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="add_funds">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fas fa-money-bill"></i> <?=lang("Add_Funds")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('add_funds', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('add_funds'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- API Page Code Part -->
              <div id="code_api" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="api">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-share-2"></i> <?=lang("API")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('api', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('api'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Tickets Page Code Part -->
              <div id="code_tickets" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="tickets">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="far fa-comments"></i> <?=lang("Tickets")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('tickets', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('tickets'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Child Panel Page Code Part -->
              <div id="code_child_panel" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="child_panel">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fas fa-child"></i> <?=lang("Child_Panel")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('child_panel', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('child_panel'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Transactions Page Code Part -->
              <div id="code_transactions" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="transactions">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-calendar"></i> <?=lang("Transactions")?></h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('transactions', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('transactions'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Sign In Page Code Part -->
              <div id="code_signin" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="signin">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fas fa-right-to-bracket"></i> Sign In Page</h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('signin', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('signin'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Sign Up Page Code Part -->
              <div id="code_signup" class="tab-pane fade" role="tabpanel">
                <form class="actionForm" action="<?=cn("$module/ajax_save")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <input type="hidden" name="page_key" value="signup">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fas fa-user-plus"></i> Sign Up Page</h5>
                      
                      <!-- Advanced Settings Panel -->
                      <?php $settings = get_page_settings('signup', $code_parts_settings); ?>
                      <div class="advanced-settings-panel">
                        <h6><i class="fas fa-cog settings-icon"></i> Advanced Settings</h6>
                        
                        <div class="settings-row">
                          <div class="settings-group">
                            <label>Display Position</label>
                            <select name="display_position" class="form-control">
                              <option value="top" <?= $settings['display_position'] == 'top' ? 'selected' : '' ?>>Top of Page</option>
                              <option value="bottom" <?= $settings['display_position'] == 'bottom' ? 'selected' : '' ?>>Bottom of Page</option>
                            </select>
                          </div>
                          
                          <div class="settings-group">
                            <label>Device Visibility</label>
                            <div class="checkbox-group">
                              <label>
                                <input type="checkbox" name="show_on_mobile" value="1" <?= $settings['show_on_mobile'] ? 'checked' : '' ?>>
                                <i class="fas fa-mobile-alt"></i> Mobile
                              </label>
                              <label>
                                <input type="checkbox" name="show_on_desktop" value="1" <?= $settings['show_on_desktop'] ? 'checked' : '' ?>>
                                <i class="fas fa-desktop"></i> Desktop
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea class="form-control plugin_editor" name="content"><?=htmlspecialchars(get_code_part_raw('signup'), ENT_QUOTES, 'UTF-8')?></textarea>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-md-12">
                      <div class="form-footer">
                        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Code Parts Tab Navigation Script -->
<script src="<?=BASE?>assets/js/code_parts_tabs.js"></script>
<script>
  // Legacy initialization - now handled by code_parts_tabs.js with lazy loading
  // Editors will be initialized only when their tabs are activated
  $(document).ready(function() {
    console.log('Code Parts page loaded - tab navigation and lazy editor loading enabled');
  });
</script>
