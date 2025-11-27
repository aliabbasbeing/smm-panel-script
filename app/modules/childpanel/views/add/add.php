<?php if (get_option('child_panel_text','') != '') { ?>
<div class="col-sm-12">
  <div class="row mb-4">
    <div class="card child-panel-info">
      <div class="card-body">
        <?=get_option('child_panel_text','')?>
      </div>
    </div>
  </div>
</div>
<?php }?>

<?php if (get_option('child_panel_code_part','') != '') { ?>
<div class="col-sm-12">
  <div class="row mb-4">
    <div class="col-sm-12">
      <?=get_option('child_panel_code_part','')?>
    </div>
  </div>
</div>
<?php }?>

<div class="row justify-content-center mt-5" id="result_ajaxSearch">
  <div class="col-md-10 col-lg-8">
    <div class="card child-panel-card">
      
      <!-- Card Header with Tabs -->
      <div class="card-header child-panel-header">
        <div class="tabs-list">
          <ul class="nav nav-tabs child-panel-tabs">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#new_order">
                <i class="fa fa-shopping-cart"></i> <?=lang('add_new')?>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?=cn('childpanel/log')?>">
                <i class="fe fe-inbox"></i> <?=lang("History")?>
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Card Body with Form -->
      <div class="card-body child-panel-body">
        <form class="form actionForm" action="<?=cn($module."/ajax_add_order")?>" data-redirect="<?=cn($module)?>" method="POST">
          
          <!-- Description Section -->
          <div class="description-section mb-4">
            <h4 class="description-title">
              <i class="fa fa-info-circle"></i> <?=lang('Description')?>
            </h4>
            <p class="description-text">
              <?=get_option('childpanel_desc')?>
            </p>
          </div>

          <!-- Form Fields -->
          <div class="form-section">
            
            <!-- Domain Name -->
            <div class="form-group">
              <label class="form-label"><?=lang("Domain_name")?></label>
              <input class="form-control form-control-lg" name="domain" type="text" placeholder="example.com">
            </div>

            <!-- Nameservers Alert -->
            <div class="alert alert-info nameserver-alert" role="alert">
              <div class="alert-title">
                <i class="fa fa-link"></i> Point your domain to these name servers
              </div>
              <div class="nameserver-list">
                <code><?=get_option('ns1')?></code>
                <code><?=get_option('ns2')?></code>
              </div>
            </div>

            <!-- Admin Email -->
            <div class="form-group">
              <label class="form-label"><?=lang("Admin_email")?></label>
              <input class="form-control form-control-lg" type="email" name="email" placeholder="admin@example.com">
            </div>

            <!-- Admin Password -->
            <div class="form-group">
              <label class="form-label"><?=lang("Admin_password")?></label>
              <input class="form-control form-control-lg" name="pass" type="password" placeholder="Enter password">
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
              <label class="form-label"><?=lang("Confirm_password")?></label>
              <input class="form-control form-control-lg" name="conf_pass" type="password" placeholder="Confirm password">
            </div>

          </div>

          <!-- Total Charge -->
          <div class="total-charge-section mb-3">
            <div class="total-charge-box">
              <span class="charge-label"><?=lang("total_charge")?>:</span>
              <span class="charge-amount">
                <?=get_option("currency_symbol", "")?>
                <span class="charge_number"><?=get_option("childpanel_price")?></span>
              </span>
            </div>
          </div>

          <!-- Error Alert -->
          <div class="alert alert-danger fund-alert d-none" role="alert">
            <i class="fa fa-exclamation-triangle"></i>
            <?=lang("order_amount_exceeds_available_funds")?>
          </div>

          <!-- Confirmation Checkbox -->
          <div class="form-group confirmation-section mb-4">
            <label class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="agree" required>
              <span class="custom-control-label">
                <?=lang("yes_i_have_confirmed_the_order")?>
              </span>
            </label>
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg btn-block">
              <i class="fa fa-paper-plane"></i> <?=lang("place_order")?>
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<style>
  /* Child Panel Card Styling */
  .child-panel-card {
    border: 2px solid #0066cc;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    background-color: #fff;
  }

  .child-panel-header {
    background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
    border-bottom: 2px solid #003d82;
    padding: 0;
  }

  .child-panel-tabs {
    margin: 0;
    border-bottom: none;
    display: flex;
    gap: 0;
  }

  .child-panel-tabs .nav-item {
    margin: 0;
  }

  .child-panel-tabs .nav-link {
    color: #fff;
    border: none;
    padding: 12px 24px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    background-color: rgba(255, 255, 255, 0.1);
  }

  .child-panel-tabs .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
  }

  .child-panel-tabs .nav-link.active {
    background-color: #00cc77;
    border-bottom: 3px solid #00aa55;
  }

  .child-panel-body {
    padding: 30px;
    background-color: #f8f9fa;
  }

  /* Description Section */
  .description-section {
    background-color: #e3f2fd;
    border-left: 4px solid #0066cc;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 30px;
  }

  .description-title {
    color: #0056b3;
    font-weight: 600;
    font-size: 18px;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  . description-title i {
    font-size: 20px;
  }

  .description-text {
    color: #0056b3;
    margin: 0;
    line-height: 1.6;
  }

  /* Form Styling */
  .form-section {
    margin-bottom: 25px;
  }

  . form-label {
    font-weight: 600;
    color: #0056b3;
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
  }

  .form-control {
    border: 1px solid #d0d8e0;
    border-radius: 6px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: #fff;
  }

  .form-control:focus {
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    outline: none;
  }

  .form-control-lg {
    padding: 12px 16px;
    font-size: 14px;
  }

  /* Nameserver Alert */
  .nameserver-alert {
    background-color: #c8e6c9;
    border: 1px solid #81c784;
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 25px;
    color: #1b5e20;
  }

  .alert-title {
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .nameserver-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .nameserver-list code {
    background-color: rgba(255, 255, 255, 0.5);
    padding: 8px 12px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: #0d3b00;
  }

  /* Total Charge Section */
  .total-charge-section {
    margin: 30px 0;
  }

  .total-charge-box {
    background: linear-gradient(135deg, #00bfff 0%, #0099cc 100%);
    color: white;
    padding: 16px 20px;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 16px;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(0, 153, 204, 0.3);
  }

  .charge-label {
    opacity: 0.9;
  }

  .charge-amount {
    font-size: 20px;
    font-weight: 700;
  }

  /* Fund Alert */
  .fund-alert {
    background-color: #ffebee;
    border: 1px solid #ef5350;
    color: #c62828;
    border-radius: 6px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Confirmation Section */
  .confirmation-section {
    background-color: #f5f5f5;
    padding: 16px;
    border-radius: 6px;
  }

  .custom-control {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
  }

  .custom-checkbox .custom-control-input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    margin: 0;
  }

  .custom-control-label {
    color: #0056b3;
    font-weight: 500;
    cursor: pointer;
    margin: 0;
    text-transform: uppercase;
    font-size: 13px;
  }

  /* Form Actions */
  .form-actions {
    margin-top: 30px;
  }

  .btn {
    border-radius: 6px;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #0066cc 0%, #0056b3 100%);
    color: white;
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004499 100%);
    box-shadow: 0 6px 12px rgba(0, 102, 204, 0.4);
    transform: translateY(-2px);
  }

  .btn-lg {
    padding: 14px 28px;
    font-size: 15px;
  }

  .btn-block {
    width: 100%;
    display: flex;
  }

  /* Child Panel Info */
  .child-panel-info {
    background-color: #e8f5e9;
    border-left: 4px solid #4caf50;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  }

  .child-panel-info . card-body {
    color: #2e7d32;
    padding: 20px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .child-panel-body {
      padding: 20px;
    }

    . total-charge-box {
      flex-direction: column;
      gap: 10px;
    }

    . btn-lg {
      padding: 12px 20px;
      font-size: 14px;
    }

    .child-panel-tabs . nav-link {
      padding: 10px 16px;
      font-size: 13px;
    }
  }

  /* Animation */
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .child-panel-card {
    animation: slideIn 0.3s ease;
  }
</style>