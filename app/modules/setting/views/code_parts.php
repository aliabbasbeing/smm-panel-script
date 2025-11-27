<style>
 .mmm {
    margin: 3px;
 }
</style>

<div class="row m-t-5">
  <div class="col-sm-12 col-sm-12">
    <div class="row">
	  <div class="col-sm-10 col-lg-10 item">
       <div class="card">
          <div class="card-header d-flex align-items-center" style="border: 0.1px solid #1B78FC; border-radius: 3.5px 3.5px 0px 0px; background: #1B78FC;">
            <div class="tabs-list">
              <ul class="nav nav-tabs">
                <li class="mmm">
                  <a class="active show" data-toggle="tab" href="#dashboard"><i class="fa fa-book"></i> Dashboard Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#new_order"><i class="fa fa-book"></i> New Order Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#orders"><i class="fa fa-book"></i> Order Logs Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#services"><i class="fa fa-book"></i> Services Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#add_funds"><i class="fa fa-book"></i> Add Funds Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#api"><i class="fa fa-book"></i> API Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#tickets"><i class="fa fa-book"></i> Tickets Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#child_panel"><i class="fa fa-book"></i> Child Panel Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#transactions"><i class="fa fa-book"></i> Transactions Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#sign_in"><i class="fa fa-sign-in"></i> Sign In Page Text</a>
                </li>
                <li class="mmm">
                  <a data-toggle="tab" href="#sign_up"><i class="fa fa-user-plus"></i> Sign Up Page Text</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content">
              
              <div id="dashboard" class="tab-pane fade in active show">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-bar-chart-2"></i> <?=lang("dashboard")?></h5>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label"><?=lang("Content")?></label>
                            <textarea rows="5" name="dashboard_text" class="form-control plugin_editor"><?=get_option('dashboard_text')?></textarea>
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
              
              <div id="new_order" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe fe-shopping-cart"></i> <?=lang("New_Order")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="new_order_text" class="form-control plugin_editor"><?=get_option('new_order_text')?></textarea>
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
              
              <div id="orders" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-shopping-cart"></i> <?=lang("Orders")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="orders_text" class="form-control plugin_editor"><?=get_option('orders_text')?></textarea>
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
              
              <div id="services" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-list"></i> <?=lang("Services")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="services_text" class="form-control plugin_editor"><?=get_option('services_text')?></textarea>
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
              
              <div id="add_funds" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-money"></i> <?=lang("Add_Funds")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="add_funds_text" class="form-control plugin_editor"><?=get_option('add_funds_text')?></textarea>
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
              
              <div id="api" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-share-2"></i> <?=lang("API")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="api_text" class="form-control plugin_editor"><?=get_option('api_text')?></textarea>
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
              
              <div id="tickets" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-comments-o"></i> <?=lang("Tickets")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="tickets_text" class="form-control plugin_editor"><?=get_option('tickets_text')?></textarea>
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
              
              <div id="child_panel" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-child"></i> <?=lang("Child_Panel")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="child_panel_text" class="form-control plugin_editor"><?=get_option('child_panel_text')?></textarea>
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
              
              <div id="transactions" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-calendar"></i> <?=lang("Transsactions")?></h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="transactions_text" class="form-control plugin_editor"><?=get_option('transactions_text')?></textarea>
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

              <div id="sign_in" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-sign-in"></i> Sign In Page</h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="sign_in_text" class="form-control plugin_editor"><?=get_option('sign_in_text')?></textarea>
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

              <div id="sign_up" class="tab-pane fade">
                <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-user-plus"></i> Sign Up Page</h5>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label"><?=lang("Content")?></label>
                              <textarea rows="5" name="sign_up_text" class="form-control plugin_editor"><?=get_option('sign_up_text')?></textarea>
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
        plugin_editor('.plugin_editor', {height: 300, toolbar: 'code'});
      });
    </script>