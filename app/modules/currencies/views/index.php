<style>
.currency-card {
  transition: all 0.3s ease;
}
.currency-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.currency-default-badge {
  position: absolute;
  top: 10px;
  right: 10px;
}
.currency-actions {
  opacity: 0.7;
  transition: opacity 0.2s ease;
}
.currency-card:hover .currency-actions {
  opacity: 1;
}
.rate-input {
  max-width: 150px;
}
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255,255,255,0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}
.currency-symbol-display {
  font-size: 1.5rem;
  font-weight: bold;
  color: #5a6770;
}
.stats-box {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 15px;
}
.stats-box .stat-value {
  font-size: 2rem;
  font-weight: bold;
}
.stats-box .stat-label {
  opacity: 0.8;
  font-size: 0.85rem;
}
.action-btn-group .btn {
  margin-right: 5px;
}
.filter-section {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}
/* Spin animation for loading icons */
.spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>

<div class="page-header">
  <h1 class="page-title">
    <i class="fe fe-dollar-sign"></i> <?=lang("Currency Manager")?>
  </h1>
  <div class="page-options d-flex">
    <button type="button" class="btn btn-success me-2" id="addCurrencyBtn">
      <i class="fe fe-plus"></i> <?=lang("Add Currency")?>
    </button>
    <button type="button" class="btn btn-info me-2" id="fetchRatesBtn">
      <i class="fe fe-refresh-cw"></i> <?=lang("Fetch Rates")?>
    </button>
    <div class="dropdown">
      <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fe fe-more-vertical"></i> <?=lang("More")?>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" id="showCronUrl">
          <i class="fe fe-link"></i> <?=lang("Cron URL")?>
        </a>
        <a class="dropdown-item" href="#" id="exportCurrencies">
          <i class="fe fe-download"></i> <?=lang("Export")?>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Stats Row -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="stats-box">
      <div class="stat-value" id="totalCurrencies"><?=count($currencies)?></div>
      <div class="stat-label"><?=lang("Total Currencies")?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stats-box" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
      <div class="stat-value" id="activeCurrencies"><?=count(array_filter($currencies, function($c) { return $c->status == 1; }))?></div>
      <div class="stat-label"><?=lang("Active Currencies")?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stats-box" style="background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);">
      <div class="stat-value" id="defaultCurrency"><?php foreach($currencies as $c) { if($c->is_default) echo $c->code; } ?></div>
      <div class="stat-label"><?=lang("Default Currency")?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stats-box" style="background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);">
      <div class="stat-value" id="lastUpdate"><?=date('M d')?></div>
      <div class="stat-label"><?=lang("Last Rate Update")?></div>
    </div>
  </div>
</div>

<!-- Cron URL Alert (hidden by default) -->
<div class="alert alert-warning d-none" id="cronUrlBox">
  <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
  <strong><i class="fe fe-terminal"></i> <?=lang("Cron URL")?>:</strong><br>
  <code id="cronUrlText" class="d-block mt-2 p-2 bg-light"></code>
  <button type="button" class="btn btn-sm btn-primary mt-2" id="copyCronUrl">
    <i class="fe fe-copy"></i> <?=lang("Copy")?>
  </button>
  <small class="text-muted mt-2 d-block"><?=lang("Use this URL in your cron job to automatically update exchange rates.")?></small>
</div>

<!-- Filter Section -->
<div class="filter-section">
  <div class="row align-items-center">
    <div class="col-md-4">
      <div class="input-group">
        <div class="">
          <span class="input-group-text"><i class="fe fe-search"></i></span>
        </div>
        <input type="text" class="form-control" id="searchCurrency" placeholder="<?=lang("Search currencies...")?>">
      </div>
    </div>
    <div class="col-md-3">
      <select class="form-control" id="filterStatus">
        <option value=""><?=lang("All Status")?></option>
        <option value="1"><?=lang("Active Only")?></option>
        <option value="0"><?=lang("Inactive Only")?></option>
      </select>
    </div>
    <div class="col-md-3">
      <select class="form-control" id="sortBy">
        <option value="default"><?=lang("Default First")?></option>
        <option value="code"><?=lang("Sort by Code")?></option>
        <option value="name"><?=lang("Sort by Name")?></option>
        <option value="rate"><?=lang("Sort by Rate")?></option>
      </select>
    </div>
    <div class="col-md-2 text-end">
      <button type="button" class="btn btn-outline-secondary" id="resetFilters">
        <i class="fe fe-x"></i> <?=lang("Reset")?>
      </button>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="row" id="currencyContainer">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">
          <i class="fe fe-list"></i> <?=lang("Currency List")?>
        </h3>
        <div class="card-options">
          <span class="text-white me-3" id="currencyCount"><?=count($currencies)?> <?=lang("currencies")?></span>
        </div>
      </div>
      <div class="card-body p-0 position-relative">
        <div class="loading-overlay d-none" id="loadingOverlay">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only"><?=lang("Loading...")?></span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table mb-0" id="currencyTable">
            <thead>
              <tr>
                <th style="width: 80px;" class="text-center"><?=lang("Code")?></th>
                <th><?=lang("Currency Name")?></th>
                <th style="width: 80px;" class="text-center"><?=lang("Symbol")?></th>
                <th style="width: 180px;"><?=lang("Exchange Rate")?></th>
                <th style="width: 120px;" class="text-center"><?=lang("Default")?></th>
                <th style="width: 100px;" class="text-center"><?=lang("Status")?></th>
                <th style="width: 150px;" class="text-center"><?=lang("Actions")?></th>
              </tr>
            </thead>
            <tbody id="currencyList">
              <?php if (!empty($currencies)): foreach ($currencies as $currency): ?>
              <tr class="currency-row" 
                  data-id="<?=$currency->id?>" 
                  data-code="<?=strtolower($currency->code)?>" 
                  data-name="<?=strtolower($currency->name)?>"
                  data-status="<?=$currency->status?>"
                  data-default="<?=$currency->is_default?>">
                <td class="text-center">
                  <strong class="text-primary"><?=htmlspecialchars($currency->code)?></strong>
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm currency-name-input" 
                         data-id="<?=$currency->id?>" 
                         data-original="<?=htmlspecialchars($currency->name)?>"
                         value="<?=htmlspecialchars($currency->name)?>">
                </td>
                <td class="text-center">
                  <input type="text" class="form-control form-control-sm currency-symbol-input text-center" 
                         data-id="<?=$currency->id?>" 
                         data-original="<?=htmlspecialchars($currency->symbol)?>"
                         value="<?=htmlspecialchars($currency->symbol)?>" style="width: 60px; margin: 0 auto;">
                </td>
                <td>
                  <div class="input-group input-group-sm">
                    <input type="number" step="0.00000001" 
                           class="form-control exchange-rate-input" 
                           data-id="<?=$currency->id?>"
                           data-original="<?=$currency->exchange_rate?>"
                           value="<?=$currency->exchange_rate?>">
                    <?php if($currency->is_default): ?>
                    <div class="">
                      <span class="input-group-text bg-info text-white">base</span>
                    </div>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="text-center">
                  <?php if ($currency->is_default): ?>
                    <span class="badge bg-success"><i class="fe fe-check"></i> <?=lang("Default")?></span>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-primary set-default-btn" data-id="<?=$currency->id?>">
                      <?=lang("Set Default")?>
                    </button>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <label class="custom-switch mb-0">
                    <input type="checkbox" class="custom-switch-input toggle-status-btn" 
                           data-id="<?=$currency->id?>" 
                           <?=$currency->status ? 'checked' : ''?>
                           <?=$currency->is_default ? 'disabled' : ''?>>
                    <span class="custom-switch-indicator"></span>
                  </label>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-success save-currency-btn" data-id="<?=$currency->id?>" title="<?=lang("Save")?>">
                      <i class="fe fe-check"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary reset-currency-btn" data-id="<?=$currency->id?>" title="<?=lang("Reset")?>">
                      <i class="fe fe-rotate-ccw"></i>
                    </button>
                    <?php if (!$currency->is_default): ?>
                    <button class="btn btn-sm btn-danger delete-currency-btn" data-id="<?=$currency->id?>" data-code="<?=$currency->code?>" title="<?=lang("Delete")?>">
                      <i class="fe fe-trash-2"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; else: ?>
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="fe fe-inbox text-muted" style="font-size: 3rem;"></i>
                  <p class="text-muted mt-2"><?=lang("No currencies found. Add your first currency to get started.")?></p>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Currency Modal -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fe fe-plus-circle"></i> <?=lang("Add New Currency")?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form id="addCurrencyForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><?=lang("Currency Code")?> <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" required maxlength="10" 
                       placeholder="USD" style="text-transform: uppercase;">
                <small class="text-muted"><?=lang("3-letter ISO code (e.g., USD, EUR, GBP)")?></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><?=lang("Symbol")?> <span class="text-danger">*</span></label>
                <input type="text" name="symbol" class="form-control" required maxlength="10" placeholder="$">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label><?=lang("Currency Name")?> <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required maxlength="100" 
                   placeholder="United States Dollar">
          </div>
          <div class="form-group">
            <label><?=lang("Exchange Rate")?> <span class="text-danger">*</span></label>
            <input type="number" step="0.00000001" name="exchange_rate" class="form-control" required value="1">
            <small class="text-muted"><?=lang("Rate relative to default currency")?></small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=lang("Cancel")?></button>
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-plus"></i> <?=lang("Add Currency")?>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fe fe-alert-triangle"></i> <?=lang("Confirm Delete")?></h5>
        <button type="button" class="close text-white" data-bs-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <p><?=lang("Are you sure you want to delete")?> <strong id="deleteCurrencyCode"></strong>?</p>
        <p class="text-muted small"><?=lang("This action cannot be undone.")?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=lang("Cancel")?></button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
          <i class="fe fe-trash-2"></i> <?=lang("Delete")?>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  var csrfName = '<?=$this->security->get_csrf_token_name()?>';
  var csrfHash = '<?=$this->security->get_csrf_hash()?>';
  var deleteId = null;
  
  // Show loading overlay
  function showLoading() {
    $('#loadingOverlay').removeClass('d-none');
  }
  
  // Hide loading overlay
  function hideLoading() {
    $('#loadingOverlay').addClass('d-none');
  }
  
  // Show toast message
  function showMessage(message, type) {
    if (typeof $.toast === 'function') {
      $.toast({
        heading: type === 'success' ? 'Success' : 'Error',
        text: message,
        position: 'top-right',
        loaderBg: type === 'success' ? '#5ba035' : '#c9302c',
        icon: type,
        hideAfter: 3000
      });
    } else {
      alert(message);
    }
  }
  
  // Search/Filter functionality
  function filterCurrencies() {
    var search = $('#searchCurrency').val().toLowerCase();
    var status = $('#filterStatus').val();
    var visibleCount = 0;
    
    $('.currency-row').each(function() {
      var $row = $(this);
      var code = $row.data('code');
      var name = $row.data('name');
      var rowStatus = String($row.data('status'));
      
      var matchSearch = code.indexOf(search) > -1 || name.indexOf(search) > -1;
      var matchStatus = status === '' || rowStatus === status;
      
      if (matchSearch && matchStatus) {
        $row.show();
        visibleCount++;
      } else {
        $row.hide();
      }
    });
    
    $('#currencyCount').text(visibleCount + ' <?=lang("currencies")?>');
  }
  
  $('#searchCurrency, #filterStatus').on('input change', filterCurrencies);
  
  $('#resetFilters').on('click', function() {
    $('#searchCurrency').val('');
    $('#filterStatus').val('');
    $('#sortBy').val('default');
    filterCurrencies();
  });
  
  // Sort functionality
  $('#sortBy').on('change', function() {
    var sortBy = $(this).val();
    var $tbody = $('#currencyList');
    var $rows = $tbody.find('.currency-row').get();
    
    $rows.sort(function(a, b) {
      var $a = $(a);
      var $b = $(b);
      
      if (sortBy === 'default') {
        return $b.data('default') - $a.data('default');
      } else if (sortBy === 'code') {
        return $a.data('code').localeCompare($b.data('code'));
      } else if (sortBy === 'name') {
        return $a.data('name').localeCompare($b.data('name'));
      } else if (sortBy === 'rate') {
        var rateA = parseFloat($a.find('.exchange-rate-input').val());
        var rateB = parseFloat($b.find('.exchange-rate-input').val());
        return rateA - rateB;
      }
      return 0;
    });
    
    $.each($rows, function(index, row) {
      $tbody.append(row);
    });
  });
  
  // Add Currency Modal
  $('#addCurrencyBtn').on('click', function() {
    $('#addCurrencyForm')[0].reset();
    $('#addCurrencyModal').modal('show');
  });
  
  // Add Currency Submit
  $('#addCurrencyForm').on('submit', function(e) {
    e.preventDefault();
    showLoading();
    
    var formData = {
      code: $('input[name="code"]').val(),
      name: $('input[name="name"]').val(),
      symbol: $('input[name="symbol"]').val(),
      exchange_rate: $('input[name="exchange_rate"]').val()
    };
    formData[csrfName] = csrfHash;
    
    $.ajax({
      url: '<?=cn("currencies/add_currency")?>',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        hideLoading();
        if (response.status === 'success') {
          showMessage(response.message, 'success');
          $('#addCurrencyModal').modal('hide');
          setTimeout(function() {
            location.reload();
          }, 1000);
        } else {
          showMessage(response.message, 'error');
        }
      },
      error: function() {
        hideLoading();
        showMessage('<?=lang("Failed to add currency")?>', 'error');
      }
    });
  });
  
  // Save Currency (name, symbol, rate)
  $('.save-currency-btn').on('click', function() {
    var id = $(this).data('id');
    var name = $('.currency-name-input[data-id="' + id + '"]').val();
    var symbol = $('.currency-symbol-input[data-id="' + id + '"]').val();
    var rate = $('.exchange-rate-input[data-id="' + id + '"]').val();
    
    if (!name || !symbol || !rate) {
      showMessage('<?=lang("All fields are required")?>', 'error');
      return;
    }
    
    showLoading();
    
    // Update currency details first
    var detailsData = { id: id, name: name, symbol: symbol };
    detailsData[csrfName] = csrfHash;
    
    $.ajax({
      url: '<?=cn("currencies/update_currency")?>',
      type: 'POST',
      data: detailsData,
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          // Now update rate
          var rateData = { id: id, exchange_rate: rate };
          rateData[csrfName] = csrfHash;
          
          $.ajax({
            url: '<?=cn("currencies/update_rate")?>',
            type: 'POST',
            data: rateData,
            dataType: 'json',
            success: function(rateResponse) {
              hideLoading();
              if (rateResponse.status === 'success') {
                showMessage('<?=lang("Currency updated successfully")?>', 'success');
                // Update original values
                $('.currency-name-input[data-id="' + id + '"]').data('original', name);
                $('.currency-symbol-input[data-id="' + id + '"]').data('original', symbol);
                $('.exchange-rate-input[data-id="' + id + '"]').data('original', rate);
              } else {
                showMessage(rateResponse.message, 'error');
              }
            },
            error: function() {
              hideLoading();
              showMessage('<?=lang("Failed to update exchange rate")?>', 'error');
            }
          });
        } else {
          hideLoading();
          showMessage(response.message, 'error');
        }
      },
      error: function() {
        hideLoading();
        showMessage('<?=lang("Failed to update currency")?>', 'error');
      }
    });
  });
  
  // Reset Currency inputs to original values
  $('.reset-currency-btn').on('click', function() {
    var id = $(this).data('id');
    var $name = $('.currency-name-input[data-id="' + id + '"]');
    var $symbol = $('.currency-symbol-input[data-id="' + id + '"]');
    var $rate = $('.exchange-rate-input[data-id="' + id + '"]');
    
    $name.val($name.data('original'));
    $symbol.val($symbol.data('original'));
    $rate.val($rate.data('original'));
  });
  
  // Set Default Currency
  $('.set-default-btn').on('click', function() {
    var id = $(this).data('id');
    showLoading();
    
    var data = { id: id };
    data[csrfName] = csrfHash;
    
    $.ajax({
      url: '<?=cn("currencies/set_default")?>',
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(response) {
        hideLoading();
        if (response.status === 'success') {
          showMessage('<?=lang("Default currency updated")?>', 'success');
          setTimeout(function() {
            location.reload();
          }, 1000);
        } else {
          showMessage(response.message, 'error');
        }
      },
      error: function() {
        hideLoading();
        showMessage('<?=lang("Failed to set default currency")?>', 'error');
      }
    });
  });
  
  // Toggle Status
  $('.toggle-status-btn').on('change', function() {
    var id = $(this).data('id');
    var status = $(this).is(':checked') ? 1 : 0;
    
    var data = { id: id, status: status };
    data[csrfName] = csrfHash;
    
    $.ajax({
      url: '<?=cn("currencies/toggle_status")?>',
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          // Update row data attribute
          $('.currency-row[data-id="' + id + '"]').data('status', status);
          showMessage('<?=lang("Status updated")?>', 'success');
        } else {
          showMessage(response.message, 'error');
        }
      },
      error: function() {
        showMessage('<?=lang("Failed to update status")?>', 'error');
      }
    });
  });
  
  // Delete Currency - show confirmation
  $('.delete-currency-btn').on('click', function() {
    deleteId = $(this).data('id');
    var code = $(this).data('code');
    $('#deleteCurrencyCode').text(code);
    $('#deleteConfirmModal').modal('show');
  });
  
  // Confirm Delete
  $('#confirmDeleteBtn').on('click', function() {
    if (!deleteId) return;
    
    showLoading();
    $('#deleteConfirmModal').modal('hide');
    
    var data = { id: deleteId };
    data[csrfName] = csrfHash;
    
    $.ajax({
      url: '<?=cn("currencies/delete_currency")?>',
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(response) {
        hideLoading();
        if (response.status === 'success') {
          showMessage('<?=lang("Currency deleted")?>', 'success');
          $('.currency-row[data-id="' + deleteId + '"]').fadeOut(300, function() {
            $(this).remove();
            // Update count
            var count = $('.currency-row:visible').length;
            $('#currencyCount').text(count + ' <?=lang("currencies")?>');
            $('#totalCurrencies').text(count);
          });
        } else {
          showMessage(response.message, 'error');
        }
        deleteId = null;
      },
      error: function() {
        hideLoading();
        showMessage('<?=lang("Failed to delete currency")?>', 'error');
        deleteId = null;
      }
    });
  });
  
  // Fetch Latest Rates
  $('#fetchRatesBtn').on('click', function() {
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fe fe-loader spin"></i> <?=lang("Fetching...")?>');
    showLoading();
    
    var data = {};
    data[csrfName] = csrfHash;
    
    $.ajax({
      url: '<?=cn("currencies/fetch_rates")?>',
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(response) {
        hideLoading();
        $btn.prop('disabled', false).html('<i class="fe fe-refresh-cw"></i> <?=lang("Fetch Rates")?>');
        
        if (response.status === 'success') {
          showMessage(response.message, 'success');
          setTimeout(function() {
            location.reload();
          }, 1500);
        } else {
          showMessage(response.message, 'error');
        }
      },
      error: function() {
        hideLoading();
        $btn.prop('disabled', false).html('<i class="fe fe-refresh-cw"></i> <?=lang("Fetch Rates")?>');
        showMessage('<?=lang("Failed to fetch exchange rates")?>', 'error');
      }
    });
  });
  
  // Show Cron URL
  $('#showCronUrl').on('click', function(e) {
    e.preventDefault();
    var cronBox = $('#cronUrlBox');
    var baseUrl = '<?=base_url()?>';
    var cronUrl = baseUrl + 'currencies/cron_fetch_rates';
    var token = '<?=get_option("currency_cron_token", "")?>';
    if (token) {
      cronUrl += '?token=' + token;
    }
    $('#cronUrlText').text(cronUrl);
    cronBox.removeClass('d-none');
  });
  
  // Copy Cron URL
  $('#copyCronUrl').on('click', function() {
    var cronUrl = $('#cronUrlText').text();
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(cronUrl).then(function() {
        showMessage('<?=lang("Cron URL copied!")?>', 'success');
      }).catch(function() {
        copyFallback(cronUrl);
      });
    } else {
      copyFallback(cronUrl);
    }
  });
  
  function copyFallback(text) {
    var $temp = $('<input>');
    $('body').append($temp);
    $temp.val(text).select();
    try {
      document.execCommand('copy');
      showMessage('<?=lang("Cron URL copied!")?>', 'success');
    } catch (err) {
      showMessage('<?=lang("Failed to copy. Please copy manually.")?>', 'error');
    }
    $temp.remove();
  }
  
  // Export currencies (simple CSV)
  $('#exportCurrencies').on('click', function(e) {
    e.preventDefault();
    var csv = 'Code,Name,Symbol,Exchange Rate,Status,Default\n';
    $('.currency-row').each(function() {
      var $row = $(this);
      var code = $row.find('.currency-name-input').closest('tr').find('td:first strong').text();
      var name = $row.find('.currency-name-input').val();
      var symbol = $row.find('.currency-symbol-input').val();
      var rate = $row.find('.exchange-rate-input').val();
      var status = $row.data('status') == 1 ? 'Active' : 'Inactive';
      var isDefault = $row.data('default') == 1 ? 'Yes' : 'No';
      csv += '"' + code + '","' + name + '","' + symbol + '",' + rate + ',' + status + ',' + isDefault + '\n';
    });
    
    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'currencies_export_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
    showMessage('<?=lang("Export completed")?>', 'success');
  });
});
</script>
