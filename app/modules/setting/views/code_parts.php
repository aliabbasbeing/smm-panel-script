<style>
  .code-parts-tab {
    margin: 3px;
  }
  .code-parts-editor {
    min-height: 300px;
  }
</style>

<div class="row m-t-5">
  <div class="col-sm-12 col-sm-12">
    <div class="row">
      <div class="col-sm-12 col-lg-12 item">
        <div class="card">
          <div class="card-header d-flex align-items-center" style="border: 0.1px solid #1B78FC; border-radius: 3.5px 3.5px 0px 0px; background: #1B78FC;">
            <h3 class="card-title text-white"><i class="fe fe-code"></i> <?=lang("Code Parts")?></h3>
          </div>
          <div class="card-body">
            <div class="alert alert-info">
              <i class="fe fe-info"></i> 
              <strong><?=lang("Info")?>:</strong> Use the HTML editor below to create styled HTML blocks for different pages. 
              The HTML content will be rendered exactly as written on the respective pages.
            </div>
            
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
            
            <div class="tab-content mt-3">
              
              <!-- Dashboard Page Code Part -->
              <div id="code_dashboard" class="tab-pane fade in active show">
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-bar-chart-2"></i> Dashboard Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Dashboard/Statistics page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="dashboard_code_part" id="dashboard_code_part"><?=htmlspecialchars_decode(get_option('dashboard_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-shopping-cart"></i> New Order Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the New Order page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="new_order_code_part" id="new_order_code_part"><?=htmlspecialchars_decode(get_option('new_order_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-shopping-cart"></i> Order Logs Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Order Logs page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="orders_code_part" id="orders_code_part"><?=htmlspecialchars_decode(get_option('orders_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-list"></i> Services Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Services page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="services_code_part" id="services_code_part"><?=htmlspecialchars_decode(get_option('services_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-money"></i> Add Funds Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Add Funds page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="add_funds_code_part" id="add_funds_code_part"><?=htmlspecialchars_decode(get_option('add_funds_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-share-2"></i> API Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the API page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="api_code_part" id="api_code_part"><?=htmlspecialchars_decode(get_option('api_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-comments-o"></i> Tickets Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Tickets page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="tickets_code_part" id="tickets_code_part"><?=htmlspecialchars_decode(get_option('tickets_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-child"></i> Child Panel Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Child Panel page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="child_panel_code_part" id="child_panel_code_part"><?=htmlspecialchars_decode(get_option('child_panel_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fe fe-calendar"></i> Transactions Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Transactions page.</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="transactions_code_part" id="transactions_code_part"><?=htmlspecialchars_decode(get_option('transactions_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-sign-in"></i> Sign In Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Sign In (Login) page. (themes/signin)</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="signin_code_part" id="signin_code_part"><?=htmlspecialchars_decode(get_option('signin_code_part',''))?></textarea>
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
                <form class="actionForm" action="<?=cn("$module/ajax_code_parts")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
                  <div class="row">
                    <div class="col-md-12 col-lg-12">
                      <h5 class="text-info"><i class="fa fa-user-plus"></i> Sign Up Page HTML Block</h5>
                      <p class="text-muted small">This HTML block will be displayed on the Sign Up (Registration) page. (themes/signup)</p>
                      <div class="form-group">
                        <textarea class="form-control code-parts-editor" name="signup_code_part" id="signup_code_part"><?=htmlspecialchars_decode(get_option('signup_code_part',''))?></textarea>
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
    // Initialize TinyMCE for all code parts editors with HTML support
    // Note: Server-side sanitization removes dangerous elements (scripts, iframes, event handlers)
    plugin_editor('.code-parts-editor', {
      height: 350,
      plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern"
      ],
      toolbar1: "code | undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | fullscreen",
      // Allow common HTML elements for styling
      valid_elements: 'div[*],span[*],p[*],br,hr,h1[*],h2[*],h3[*],h4[*],h5[*],h6[*],a[*],img[*],ul[*],ol[*],li[*],table[*],thead[*],tbody[*],tr[*],td[*],th[*],strong,em,b,i,u,s,small,big,sup,sub,pre,code,blockquote[*],section[*],article[*],header[*],footer[*],nav[*],aside[*],figure[*],figcaption[*],main[*],address,dl[*],dt[*],dd[*],abbr[*],cite,q[*],time[*],mark,ins,del,style[*]',
      invalid_elements: 'script,iframe,object,embed,form,input,button,select,textarea,base',
      verify_html: true,
      force_br_newlines: false,
      force_p_newlines: false,
      forced_root_block: ''
    });
  });
</script>
