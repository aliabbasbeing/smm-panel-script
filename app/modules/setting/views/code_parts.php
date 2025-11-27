<style>
  .code-parts-tab {
    margin: 3px;
  }
  .variables-list {
    font-size: 12px;
  }
  .variables-list code {
    background: #e9ecef;
    padding: 2px 5px;
    border-radius: 3px;
    margin-right: 5px;
  }
</style>

<div class="row m-t-5">
  <div class="col-sm-12 col-sm-12">
    <div class="row">
      <div class="col-sm-12 col-lg-12 item">
        <div class="card">
          <div class="card-header d-flex align-items-center" style="border: 0.1px solid #1B78FC; border-radius: 3.5px 3.5px 0px 0px; background: #1B78FC;">
            <div class="tabs-list">
              <ul class="nav nav-tabs">
                <li class="code-parts-tab">
                  <a class="active show" data-toggle="tab" href="#code_dashboard"><i class="fa fa-home"></i> Dashboard</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_new_order"><i class="fa fa-cart-plus"></i> New Order</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_orders"><i class="fa fa-list"></i> Order Logs</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_services"><i class="fa fa-server"></i> Services</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_add_funds"><i class="fa fa-money"></i> Add Funds</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_api"><i class="fa fa-plug"></i> API</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_tickets"><i class="fa fa-ticket"></i> Tickets</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_child_panel"><i class="fa fa-child"></i> Child Panel</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_transactions"><i class="fa fa-exchange"></i> Transactions</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_signin"><i class="fa fa-sign-in"></i> Sign In</a>
                </li>
                <li class="code-parts-tab">
                  <a data-toggle="tab" href="#code_signup"><i class="fa fa-user-plus"></i> Sign Up</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="alert alert-info">
              <i class="fe fe-info"></i> 
              <strong><?=lang("Info")?>:</strong> Use the HTML editor below to create styled HTML blocks for different pages. 
              HTML is sanitized for security - scripts, iframes, and event handlers are removed. Use inline CSS (style attribute) for styling.
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
            
            <div class="tab-content mt-3">
              
              <!-- Dashboard Page Code Part -->
              <div id="code_dashboard" class="tab-pane fade in active show">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-bar-chart-2"></i> <?=lang("dashboard")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="dashboard_code_part" value="<?=get_option('dashboard_code_part')?>">
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
              <div id="code_new_order" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-shopping-cart"></i> <?=lang("New_Order")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="new_order_code_part" value="<?=get_option('new_order_code_part')?>">
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
              <div id="code_orders" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-shopping-cart"></i> <?=lang("Orders")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="orders_code_part" value="<?=get_option('orders_code_part')?>">
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
              <div id="code_services" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-list"></i> <?=lang("Services")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="services_code_part" value="<?=get_option('services_code_part')?>">
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
              <div id="code_add_funds" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-money"></i> <?=lang("Add_Funds")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="add_funds_code_part" value="<?=get_option('add_funds_code_part')?>">
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
              <div id="code_api" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-share-2"></i> <?=lang("API")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="api_code_part" value="<?=get_option('api_code_part')?>">
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
              <div id="code_tickets" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-comments-o"></i> <?=lang("Tickets")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="tickets_code_part" value="<?=get_option('tickets_code_part')?>">
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
              <div id="code_child_panel" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-child"></i> <?=lang("Child_Panel")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="child_panel_code_part" value="<?=get_option('child_panel_code_part')?>">
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
              <div id="code_transactions" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-calendar"></i> <?=lang("Transsactions")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="transactions_code_part" value="<?=get_option('transactions_code_part')?>">
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
              <div id="code_signin" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-sign-in"></i> Sign In Page</h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="signin_code_part" value="<?=get_option('signin_code_part')?>">
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
              <div id="code_signup" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-user-plus"></i> Sign Up Page</h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <input class="form-control plugin_editor" name="signup_code_part" value="<?=get_option('signup_code_part')?>">
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

<script>
   $(document).ready(function() {
       plugin_editor('.plugin_editor', {height: 200});
   });
</script>
