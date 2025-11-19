<div class="card content">
  <div class="card-header" style="border: 0.1px solid #05d0a0; border-radius: 3.5px 3.5px 0px 0px; background: #05d0a0;">
    <h3 class="card-title"><i class="fe fe-dollar-sign"></i> <?=lang("Multi-Currency Management")?></h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-info">
          <i class="fe fe-info"></i> Manage multiple currencies for your SMM panel. Users can switch between currencies in the sidebar. All amounts will be converted based on exchange rates.
        </div>
        
        <div class="mb-3">
          <button type="button" class="btn btn-success" id="fetchRatesBtn">
            <i class="fe fe-download"></i> Fetch Latest Exchange Rates
          </button>
          <button type="button" class="btn btn-info" id="showCronUrl">
            <i class="fe fe-link"></i> Show Cron URL
          </button>
        </div>
        
        <div class="alert alert-warning d-none" id="cronUrlBox">
          <strong>Cron URL:</strong><br>
          <code id="cronUrlText"></code>
          <button type="button" class="btn btn-sm btn-primary ml-2" id="copyCronUrl">
            <i class="fe fe-copy"></i> Copy
          </button>
          <br><small class="text-muted mt-2 d-block">Use this URL in your cron job to automatically update exchange rates daily.</small>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <thead>
              <tr>
                <th><?=lang("Code")?></th>
                <th><?=lang("Name")?></th>
                <th><?=lang("Symbol")?></th>
                <th><?=lang("Exchange Rate")?></th>
                <th><?=lang("Default")?></th>
                <th><?=lang("Status")?></th>
                <th><?=lang("Actions")?></th>
              </tr>
            </thead>
            <tbody>
              <?php
                // Get currencies from database
                $currencies = get_active_currencies();
                if (!empty($currencies)) {
                  foreach ($currencies as $currency) {
              ?>
              <tr>
                <td><strong><?=$currency->code?></strong></td>
                <td>
                  <input type="text" class="form-control form-control-sm currency-name" 
                         data-id="<?=$currency->id?>" value="<?=htmlspecialchars($currency->name)?>" 
                         style="width: 150px;">
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm currency-symbol" 
                         data-id="<?=$currency->id?>" value="<?=htmlspecialchars($currency->symbol)?>" 
                         style="width: 80px;">
                </td>
                <td>
                  <input type="number" step="0.00000001" class="form-control form-control-sm exchange-rate" 
                         data-id="<?=$currency->id?>" value="<?=$currency->exchange_rate?>" style="width: 150px;">
                </td>
                <td>
                  <?php if ($currency->is_default) { ?>
                    <span class="badge badge-success"><?=lang("Default")?></span>
                  <?php } else { ?>
                    <button class="btn btn-sm btn-primary set-default" data-id="<?=$currency->id?>">
                      <?=lang("Set as Default")?>
                    </button>
                  <?php } ?>
                </td>
                <td>
                  <label class="custom-switch">
                    <input type="checkbox" class="custom-switch-input toggle-status" 
                           data-id="<?=$currency->id?>" <?=$currency->status ? 'checked' : ''?>>
                    <span class="custom-switch-indicator"></span>
                  </label>
                </td>
                <td>
                  <button class="btn btn-sm btn-success update-currency" data-id="<?=$currency->id?>">
                    <i class="fe fe-check"></i> <?=lang("Update")?>
                  </button>
                </td>
              </tr>
              <?php 
                  }
                } else {
              ?>
              <tr>
                <td colspan="7" class="text-center"><?=lang("No currencies found. Please run the multi-currency.sql migration.")?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          <h5><?=lang("Add New Currency")?></h5>
          <form class="actionForm" action="<?=cn("currencies/add_currency")?>" method="POST">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label><?=lang("Code")?></label>
                  <input type="text" name="code" class="form-control" required maxlength="10" placeholder="USD">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label><?=lang("Name")?></label>
                  <input type="text" name="name" class="form-control" required maxlength="100" placeholder="US Dollar">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label><?=lang("Symbol")?></label>
                  <input type="text" name="symbol" class="form-control" required maxlength="10" placeholder="$">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label><?=lang("Exchange Rate")?></label>
                  <input type="number" step="0.00000001" name="exchange_rate" class="form-control" required value="1">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fe fe-plus"></i> <?=lang("Add")?>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Update currency (name, symbol, and rate)
  $('.update-currency').on('click', function() {
    var id = $(this).data('id');
    var name = $('.currency-name[data-id="' + id + '"]').val();
    var symbol = $('.currency-symbol[data-id="' + id + '"]').val();
    var rate = $('.exchange-rate[data-id="' + id + '"]').val();
    
    // Validate inputs
    if (!name || !symbol || !rate) {
      show_message('All fields are required', 'error');
      return;
    }
    
    // Update both currency details and exchange rate
    $.ajax({
      url: '<?=cn("currencies/update_currency")?>',
      type: 'POST',
      data: {
        id: id,
        name: name,
        symbol: symbol,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          // Now update the exchange rate
          $.ajax({
            url: '<?=cn("currencies/update_rate")?>',
            type: 'POST',
            data: {
              id: id,
              exchange_rate: rate,
              <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
            },
            dataType: 'json',
            success: function(rateResponse) {
              if (rateResponse.status == 'success') {
                show_message('Currency updated successfully', 'success');
              } else {
                show_message(rateResponse.message, 'error');
              }
            }
          });
        } else {
          show_message(response.message, 'error');
        }
      },
      error: function() {
        show_message('Failed to update currency', 'error');
      }
    });
  });
  
  // Set as default
  $('.set-default').on('click', function() {
    var id = $(this).data('id');
    
    $.ajax({
      url: '<?=cn("currencies/set_default")?>',
      type: 'POST',
      data: {
        id: id,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          location.reload();
        } else {
          show_message(response.message, 'error');
        }
      }
    });
  });
  
  // Toggle status
  $('.toggle-status').on('change', function() {
    var id = $(this).data('id');
    var status = $(this).is(':checked') ? 1 : 0;
    
    $.ajax({
      url: '<?=cn("currencies/toggle_status")?>',
      type: 'POST',
      data: {
        id: id,
        status: status,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status != 'success') {
          show_message(response.message, 'error');
        }
      }
    });
  });
  
  // Fetch latest exchange rates
  $('#fetchRatesBtn').on('click', function() {
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fe fe-loader"></i> Fetching...');
    
    $.ajax({
      url: '<?=cn("currencies/fetch_rates")?>',
      type: 'POST',
      data: {
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        btn.prop('disabled', false).html('<i class="fe fe-download"></i> Fetch Latest Exchange Rates');
        
        if (response.status == 'success') {
          show_message(response.message, 'success');
          // Reload page to show updated rates
          setTimeout(function() {
            location.reload();
          }, 1500);
        } else {
          show_message(response.message, 'error');
        }
      },
      error: function() {
        btn.prop('disabled', false).html('<i class="fe fe-download"></i> Fetch Latest Exchange Rates');
        show_message('Failed to fetch exchange rates', 'error');
      }
    });
  });
  
  // Show cron URL
  $('#showCronUrl').on('click', function() {
    var cronBox = $('#cronUrlBox');
    var baseUrl = '<?=base_url()?>';
    var cronUrl = baseUrl + 'currencies/cron_fetch_rates';
    
    // Add token if available
    var token = '<?=get_option("currency_cron_token", "")?>';
    if (token) {
      cronUrl += '?token=' + token;
    }
    
    $('#cronUrlText').text(cronUrl);
    cronBox.removeClass('d-none');
  });
  
  // Copy cron URL
  $('#copyCronUrl').on('click', function() {
    var cronUrl = $('#cronUrlText').text();
    
    // Use modern Clipboard API with fallback for older browsers
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(cronUrl).then(function() {
        show_message('Cron URL copied to clipboard!', 'success');
      }).catch(function() {
        // Fallback if clipboard API fails
        copyToClipboardFallback(cronUrl);
      });
    } else {
      // Fallback for older browsers
      copyToClipboardFallback(cronUrl);
    }
  });
  
  // Fallback function for older browsers
  function copyToClipboardFallback(text) {
    var tempInput = $('<input>');
    $('body').append(tempInput);
    tempInput.val(text).select();
    try {
      document.execCommand('copy');
      show_message('Cron URL copied to clipboard!', 'success');
    } catch (err) {
      show_message('Failed to copy. Please copy manually.', 'error');
    }
    tempInput.remove();
  }
});

function show_message(message, type) {
  $.toast({
    heading: type == 'success' ? 'Success' : 'Error',
    text: message,
    position: 'top-right',
    loaderBg: type == 'success' ? '#5ba035' : '#c9302c',
    icon: type,
    hideAfter: 3000
  });
}
</script>
