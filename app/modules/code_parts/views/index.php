<style>
  .code-parts-manager {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  }
  
  .code-parts-header {
    background: linear-gradient(135deg, #1B78FC 0%, #0056b3 100%);
    color: #fff;
    padding: 20px 25px;
    border-radius: 8px 8px 0 0;
  }
  
  .code-parts-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .code-parts-header p {
    margin: 5px 0 0;
    opacity: 0.9;
    font-size: 0.9rem;
  }
  
  .code-parts-body {
    padding: 0;
  }
  
  .code-parts-sidebar {
    border-right: 1px solid #e9ecef;
    background: #f8f9fa;
    min-height: 600px;
  }
  
  .code-parts-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .code-parts-list-item {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }
  
  .code-parts-list-item:last-child {
    border-bottom: none;
  }
  
  .code-parts-list-item a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  
  .code-parts-list-item a:hover {
    background: #e9ecef;
    color: #1B78FC;
  }
  
  .code-parts-list-item.active a {
    background: #1B78FC;
    color: #fff;
  }
  
  .code-parts-list-item .item-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .code-parts-list-item .item-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(27, 120, 252, 0.1);
    border-radius: 8px;
    font-size: 16px;
    color: #1B78FC;
  }
  
  .code-parts-list-item.active .item-icon {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
  }
  
  .code-parts-list-item .item-text h6 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 500;
  }
  
  .code-parts-list-item .item-text small {
    color: #6c757d;
    font-size: 0.75rem;
  }
  
  .code-parts-list-item.active .item-text small {
    color: rgba(255, 255, 255, 0.8);
  }
  
  .code-parts-list-item .item-status {
    font-size: 0.75rem;
  }
  
  .code-parts-content {
    padding: 25px;
    min-height: 600px;
  }
  
  .code-parts-editor-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
  }
  
  .code-parts-editor-title h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #343a40;
  }
  
  .code-parts-editor-title p {
    margin: 5px 0 0;
    color: #6c757d;
    font-size: 0.875rem;
  }
  
  .status-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .status-toggle .custom-switch {
    padding-left: 2.75rem;
  }
  
  .variables-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
  }
  
  .variables-card h6 {
    margin: 0 0 10px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
  }
  
  .variables-list {
    font-size: 0.8rem;
  }
  
  .variables-list code {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    margin: 2px 4px 2px 0;
    display: inline-block;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .variables-list code:hover {
    background: #1B78FC;
    color: #fff;
  }
  
  .editor-container {
    margin-bottom: 20px;
  }
  
  .editor-container .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
  }
  
  .btn-save-code-part {
    padding: 12px 30px;
    font-weight: 600;
    text-transform: uppercase;
  }
  
  .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
  }
  
  .loading-overlay.hidden {
    display: none;
  }
  
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
  }
  
  .empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
  }
  
  .empty-state h5 {
    margin-bottom: 10px;
    color: #495057;
  }

  @media (max-width: 991px) {
    .code-parts-sidebar {
      min-height: auto;
      border-right: none;
      border-bottom: 1px solid #e9ecef;
    }
    
    .code-parts-list-item a {
      padding: 12px 15px;
    }
  }
</style>

<div class="row m-t-5">
  <div class="col-12">
    <div class="code-parts-manager">
      <!-- Header -->
      <div class="code-parts-header">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h3><i class="fe fe-code"></i> <?=lang("Code Parts & Blocks Manager")?></h3>
            <p><?=lang("Manage custom HTML content blocks for different pages")?></p>
          </div>
          <div>
            <span class="badge badge-light">
              <i class="fe fe-layers"></i> <?=count($code_parts)?> <?=lang("Code Parts")?>
            </span>
          </div>
        </div>
      </div>
      
      <!-- Body -->
      <div class="code-parts-body">
        <div class="row no-gutters">
          <!-- Sidebar with code parts list -->
          <div class="col-lg-3 code-parts-sidebar">
            <ul class="code-parts-list" id="codePartsList">
              <?php 
              $icons = [
                'dashboard' => 'fe fe-home',
                'new_order' => 'fe fe-shopping-cart',
                'orders' => 'fe fe-list',
                'services' => 'fe fe-server',
                'add_funds' => 'fe fe-dollar-sign',
                'api' => 'fe fe-share-2',
                'tickets' => 'fe fe-message-square',
                'child_panel' => 'fa fa-child',
                'transactions' => 'fe fe-credit-card',
                'signin' => 'fe fe-log-in',
                'signup' => 'fe fe-user-plus'
              ];
              
              foreach ($code_parts as $part): 
                $icon = isset($icons[$part->page_key]) ? $icons[$part->page_key] : 'fe fe-code';
                $is_active = ($active_tab == $part->page_key);
                $has_content = !empty($part->content);
              ?>
              <li class="code-parts-list-item <?=$is_active ? 'active' : ''?>" data-key="<?=$part->page_key?>">
                <a href="javascript:void(0)" onclick="loadCodePart('<?=$part->page_key?>')">
                  <div class="item-info">
                    <div class="item-icon">
                      <i class="<?=$icon?>"></i>
                    </div>
                    <div class="item-text">
                      <h6><?=htmlspecialchars($part->page_name)?></h6>
                      <small><?=$part->page_key?></small>
                    </div>
                  </div>
                  <div class="item-status">
                    <?php if ($part->status == 1): ?>
                      <?php if ($has_content): ?>
                        <span class="badge badge-success">Active</span>
                      <?php else: ?>
                        <span class="badge badge-secondary">Empty</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="badge badge-warning">Disabled</span>
                    <?php endif; ?>
                  </div>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          
          <!-- Editor Area -->
          <div class="col-lg-9">
            <div class="code-parts-content position-relative">
              <!-- Loading Overlay -->
              <div class="loading-overlay hidden" id="editorLoading">
                <div class="text-center">
                  <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <p class="mt-2 text-muted">Loading content...</p>
                </div>
              </div>
              
              <!-- Empty State (shown initially if no tab selected) -->
              <div class="empty-state" id="emptyState" style="<?=$active_tab ? 'display:none;' : ''?>">
                <i class="fe fe-code"></i>
                <h5><?=lang("Select a Code Part")?></h5>
                <p><?=lang("Choose a code part from the list to start editing")?></p>
              </div>
              
              <!-- Editor Form -->
              <div id="editorForm" style="<?=!$active_tab ? 'display:none;' : ''?>">
                <div class="code-parts-editor-header">
                  <div class="code-parts-editor-title">
                    <h4 id="editorTitle"><?=lang("Select a Code Part")?></h4>
                    <p id="editorSubtitle"><?=lang("Choose a code part from the list to start editing")?></p>
                  </div>
                  <div class="status-toggle">
                    <label class="text-muted mr-2"><?=lang("Status")?>:</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="statusToggle" checked>
                      <label class="custom-control-label" for="statusToggle"><?=lang("Enabled")?></label>
                    </div>
                  </div>
                </div>
                
                <!-- Variables Reference -->
                <div class="variables-card">
                  <h6><i class="fe fe-info"></i> <?=lang("Available Variables")?> <small class="text-muted">(click to copy)</small></h6>
                  <div class="variables-list">
                    <div class="mb-2">
                      <strong class="text-muted">User:</strong>
                      <code onclick="copyVariable(this)">{{user.balance}}</code>
                      <code onclick="copyVariable(this)">{{user.name}}</code>
                      <code onclick="copyVariable(this)">{{user.email}}</code>
                      <code onclick="copyVariable(this)">{{user.orders}}</code>
                      <code onclick="copyVariable(this)">{{user.spent}}</code>
                      <code onclick="copyVariable(this)">{{user.pending_orders}}</code>
                      <code onclick="copyVariable(this)">{{user.completed_orders}}</code>
                      <code onclick="copyVariable(this)">{{user.tickets}}</code>
                    </div>
                    <div class="mb-2">
                      <strong class="text-muted">Site:</strong>
                      <code onclick="copyVariable(this)">{{site.name}}</code>
                      <code onclick="copyVariable(this)">{{site.url}}</code>
                      <code onclick="copyVariable(this)">{{site.currency}}</code>
                      <code onclick="copyVariable(this)">{{site.currency_code}}</code>
                    </div>
                    <div>
                      <strong class="text-muted">Date:</strong>
                      <code onclick="copyVariable(this)">{{date.today}}</code>
                      <code onclick="copyVariable(this)">{{date.now}}</code>
                      <code onclick="copyVariable(this)">{{date.year}}</code>
                    </div>
                  </div>
                </div>
                
                <!-- Editor -->
                <form id="codePartForm">
                  <input type="hidden" name="page_key" id="currentPageKey" value="">
                  
                  <div class="editor-container">
                    <label class="form-label"><?=lang("HTML Content")?></label>
                    <textarea class="form-control" id="codePartEditor" name="content" rows="15"></textarea>
                  </div>
                  
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <span class="text-muted" id="lastUpdated"></span>
                    </div>
                    <div>
                      <button type="button" class="btn btn-outline-secondary mr-2" onclick="resetEditor()">
                        <i class="fe fe-refresh-cw"></i> <?=lang("Reset")?>
                      </button>
                      <button type="submit" class="btn btn-primary btn-save-code-part">
                        <i class="fe fe-save"></i> <?=lang("Save Changes")?>
                      </button>
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

<!-- Info Card -->
<div class="row mt-4">
  <div class="col-12">
    <div class="alert alert-info">
      <div class="d-flex align-items-start">
        <i class="fe fe-info mr-3" style="font-size: 24px; margin-top: 2px;"></i>
        <div>
          <h6 class="alert-heading mb-1"><?=lang("About Code Parts")?></h6>
          <p class="mb-0"><?=lang("Code Parts allow you to add custom HTML content blocks to different pages of your panel. The content supports template variables for dynamic user and site information. HTML is sanitized for security - scripts, iframes, and event handlers are removed. Use inline CSS (style attribute) for styling.")?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var currentPageKey = '<?=$active_tab?>';
  var editorInitialized = false;
  var originalContent = '';
  
  $(document).ready(function() {
    // Initialize editor if we have an active tab
    if (currentPageKey) {
      loadCodePart(currentPageKey);
    }
    
    // Form submission
    $('#codePartForm').on('submit', function(e) {
      e.preventDefault();
      saveCodePart();
    });
    
    // Status toggle
    $('#statusToggle').on('change', function() {
      if (currentPageKey) {
        toggleStatus(currentPageKey, $(this).is(':checked') ? 1 : 0);
      }
    });
  });
  
  function initEditor() {
    if (!editorInitialized) {
      if (typeof plugin_editor === 'function') {
        plugin_editor('#codePartEditor', {height: 350});
      }
      editorInitialized = true;
    }
  }
  
  function loadCodePart(pageKey) {
    if (!pageKey) return;
    
    // Update UI
    currentPageKey = pageKey;
    $('#emptyState').hide();
    $('#editorForm').show();
    $('#editorLoading').removeClass('hidden');
    
    // Update active state in list
    $('.code-parts-list-item').removeClass('active');
    $('.code-parts-list-item[data-key="' + pageKey + '"]').addClass('active');
    
    // Fetch content via AJAX
    $.ajax({
      url: '<?=cn("code_parts/ajax_get_content")?>',
      method: 'POST',
      data: {
        page_key: pageKey,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          // Update editor
          $('#currentPageKey').val(pageKey);
          
          // Initialize editor if not done
          initEditor();
          
          // Get the nice name
          var item = $('.code-parts-list-item[data-key="' + pageKey + '"]');
          var pageName = item.find('.item-text h6').text();
          
          $('#editorTitle').text(pageName);
          $('#editorSubtitle').text('Edit HTML content for the ' + pageName.toLowerCase());
          
          // Set content
          if (typeof tinymce !== 'undefined' && tinymce.get('codePartEditor')) {
            tinymce.get('codePartEditor').setContent(response.data.content || '');
          } else {
            $('#codePartEditor').val(response.data.content || '');
          }
          
          originalContent = response.data.content || '';
          
          // Set status toggle
          $('#statusToggle').prop('checked', response.data.status == 1);
          
        } else {
          toastr.error(response.message || 'Failed to load content');
        }
      },
      error: function() {
        toastr.error('Failed to load content. Please try again.');
      },
      complete: function() {
        $('#editorLoading').addClass('hidden');
      }
    });
  }
  
  function saveCodePart() {
    if (!currentPageKey) {
      toastr.error('Please select a code part first');
      return;
    }
    
    var content = '';
    if (typeof tinymce !== 'undefined' && tinymce.get('codePartEditor')) {
      content = tinymce.get('codePartEditor').getContent();
    } else {
      content = $('#codePartEditor').val();
    }
    
    $('#editorLoading').removeClass('hidden');
    
    $.ajax({
      url: '<?=cn("code_parts/ajax_save")?>',
      method: 'POST',
      data: {
        page_key: currentPageKey,
        content: content,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          toastr.success(response.message || 'Saved successfully');
          originalContent = content;
          
          // Update badge in list
          var item = $('.code-parts-list-item[data-key="' + currentPageKey + '"]');
          var badge = item.find('.item-status .badge');
          if (content.trim()) {
            badge.removeClass('badge-secondary').addClass('badge-success').text('Active');
          } else {
            badge.removeClass('badge-success').addClass('badge-secondary').text('Empty');
          }
        } else {
          toastr.error(response.message || 'Failed to save');
        }
      },
      error: function() {
        toastr.error('Failed to save. Please try again.');
      },
      complete: function() {
        $('#editorLoading').addClass('hidden');
      }
    });
  }
  
  function toggleStatus(pageKey, status) {
    $.ajax({
      url: '<?=cn("code_parts/ajax_toggle_status")?>',
      method: 'POST',
      data: {
        page_key: pageKey,
        status: status,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          toastr.success(response.message || 'Status updated');
          
          // Update badge in list
          var item = $('.code-parts-list-item[data-key="' + pageKey + '"]');
          var badge = item.find('.item-status .badge');
          if (status) {
            var hasContent = originalContent.trim() !== '';
            if (hasContent) {
              badge.removeClass('badge-warning badge-secondary').addClass('badge-success').text('Active');
            } else {
              badge.removeClass('badge-warning badge-success').addClass('badge-secondary').text('Empty');
            }
          } else {
            badge.removeClass('badge-success badge-secondary').addClass('badge-warning').text('Disabled');
          }
        } else {
          toastr.error(response.message || 'Failed to update status');
          // Revert toggle
          $('#statusToggle').prop('checked', !status);
        }
      },
      error: function() {
        toastr.error('Failed to update status. Please try again.');
        // Revert toggle
        $('#statusToggle').prop('checked', !status);
      }
    });
  }
  
  function resetEditor() {
    if (confirm('Are you sure you want to reset? Any unsaved changes will be lost.')) {
      if (typeof tinymce !== 'undefined' && tinymce.get('codePartEditor')) {
        tinymce.get('codePartEditor').setContent(originalContent);
      } else {
        $('#codePartEditor').val(originalContent);
      }
    }
  }
  
  function copyVariable(element) {
    var text = $(element).text();
    
    // Use modern Clipboard API with fallback
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function() {
        toastr.info('Copied: ' + text);
      }).catch(function() {
        // Fallback for clipboard API failure
        fallbackCopy(text);
      });
    } else {
      // Fallback for older browsers
      fallbackCopy(text);
    }
  }
  
  function fallbackCopy(text) {
    var $temp = $('<textarea>');
    $temp.css({position: 'absolute', left: '-9999px'});
    $('body').append($temp);
    $temp.val(text).select();
    try {
      document.execCommand('copy');
      toastr.info('Copied: ' + text);
    } catch (err) {
      toastr.error('Failed to copy');
    }
    $temp.remove();
  }
</script>
