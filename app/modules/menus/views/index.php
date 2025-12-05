<div class="row justify-content-center">
  <div class="col-md-12 col-lg-12">
    <div class="card p-0">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title" style="color:#fff !important;">
          <i class="fe fe-menu"></i> <?=lang("Header Menu Management")?>
        </h3>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#menuModal" onclick="openAddModal()">
          <i class="fe fe-plus"></i> <?=lang("Add Menu Item")?>
        </button>
      </div>
      <div class="card-body">
        <div class="alert alert-info">
          <i class="fe fe-info"></i> 
          <?=lang("Drag and drop menu items to reorder them. Changes are saved automatically.")?>
        </div>

        <div class="table-responsive">
          <table class="table table-hover" id="menuTable">
            <thead>
              <tr>
                <th width="40">#</th>
                <th width="50"><?=lang("Icon")?></th>
                <th><?=lang("Title")?></th>
                <th><?=lang("URL")?></th>
                <th><?=lang("Visibility")?></th>
                <th width="100"><?=lang("New Tab")?></th>
                <th width="100"><?=lang("Status")?></th>
                <th width="120"><?=lang("Actions")?></th>
              </tr>
            </thead>
            <tbody id="menuList">
              <?php if (!empty($menu_items)): ?>
                <?php foreach ($menu_items as $item): ?>
                  <tr data-id="<?=$item['id']?>">
                    <td class="drag-handle" style="cursor: move;">
                      <i class="fe fe-menu"></i>
                    </td>
                    <td>
                      <?php if (!empty($item['icon'])): ?>
                        <i class="<?=htmlspecialchars($item['icon'])?>"></i>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <strong><?=htmlspecialchars($item['title'])?></strong>
                    </td>
                    <td>
                      <code><?=htmlspecialchars($item['url'])?></code>
                    </td>
                    <td>
                      <?php 
                        $roles = isset($item['roles']) ? $item['roles'] : ['everyone'];
                        foreach ($roles as $role): 
                          $role_label = isset($available_roles[$role]) ? $available_roles[$role] : $role;
                      ?>
                        <span class="badge badge-secondary"><?=htmlspecialchars($role_label)?></span>
                      <?php endforeach; ?>
                    </td>
                    <td>
                      <?php if (!empty($item['new_tab'])): ?>
                        <span class="badge badge-info"><?=lang("Yes")?></span>
                      <?php else: ?>
                        <span class="badge badge-secondary"><?=lang("No")?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <label class="custom-switch">
                        <input type="checkbox" class="custom-switch-input toggle-status" 
                               data-id="<?=$item['id']?>" 
                               <?=(!empty($item['status']) && $item['status'] == 1) ? 'checked' : ''?>>
                        <span class="custom-switch-indicator"></span>
                      </label>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-info edit-btn" data-id="<?=$item['id']?>" title="<?=lang("Edit")?>">
                        <i class="fe fe-edit"></i>
                      </button>
                      <button class="btn btn-sm btn-danger delete-btn" data-id="<?=$item['id']?>" title="<?=lang("Delete")?>">
                        <i class="fe fe-trash-2"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr id="emptyRow">
                  <td colspan="8" class="text-center text-muted py-5">
                    <i class="fe fe-inbox" style="font-size: 48px;"></i>
                    <p class="mt-3"><?=lang("No menu items found. Click 'Add Menu Item' to create your first menu item.")?></p>
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

<!-- Add/Edit Menu Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"><?=lang("Add Menu Item")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="menuForm">
        <div class="modal-body">
          <input type="hidden" name="id" id="menuId" value="">
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label"><?=lang("Title")?> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="menuTitle" required placeholder="<?=lang("e.g., Dashboard")?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label"><?=lang("URL")?> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="url" id="menuUrl" required placeholder="<?=lang("e.g., statistics or https://example.com")?>">
                <small class="text-muted"><?=lang("Use relative path (e.g., 'order/add') or full URL")?></small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label"><?=lang("Icon Class")?></label>
                <input type="text" class="form-control" name="icon" id="menuIcon" placeholder="<?=lang("e.g., fe fe-home or fas fa-gauge")?>">
                <small class="text-muted"><?=lang("Use Font Awesome or Feather icons")?></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label"><?=lang("Preview")?></label>
                <div class="p-3 border rounded bg-light" id="iconPreview">
                  <i class="fe fe-link"></i> <span id="titlePreview"><?=lang("Menu Item")?></span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label"><?=lang("Visibility (Roles)")?></label>
            <div class="row">
              <?php foreach ($available_roles as $role_key => $role_label): ?>
                <div class="col-md-4">
                  <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input role-checkbox" name="roles[]" value="<?=$role_key?>">
                    <span class="custom-control-label"><?=$role_label?></span>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" name="new_tab" id="menuNewTab" value="1">
                  <span class="custom-control-label"><?=lang("Open in New Tab")?></span>
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" name="status" id="menuStatus" value="1" checked>
                  <span class="custom-control-label"><?=lang("Enabled")?></span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang("Cancel")?></button>
          <button type="submit" class="btn btn-primary" id="submitBtn"><?=lang("Save")?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?=lang("Confirm Delete")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?=lang("Are you sure you want to delete this menu item?")?></p>
        <input type="hidden" id="deleteItemId" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang("Cancel")?></button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><?=lang("Delete")?></button>
      </div>
    </div>
  </div>
</div>

<!-- Include Sortable.js for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
.drag-handle {
  cursor: move !important;
}
.drag-handle:hover {
  background-color: #f8f9fa;
}
.sortable-ghost {
  background-color: #e9ecef !important;
  opacity: 0.8;
}
.custom-switch {
  padding-left: 0;
}
#iconPreview {
  min-height: 50px;
  display: flex;
  align-items: center;
  gap: 10px;
}
#iconPreview i {
  font-size: 20px;
}
.table td {
  vertical-align: middle;
}
</style>

<script>
var isEditMode = false;

// Initialize Sortable for drag and drop reordering
document.addEventListener('DOMContentLoaded', function() {
  var menuList = document.getElementById('menuList');
  if (menuList && menuList.children.length > 0 && !document.getElementById('emptyRow')) {
    new Sortable(menuList, {
      handle: '.drag-handle',
      animation: 150,
      ghostClass: 'sortable-ghost',
      onEnd: function() {
        saveOrder();
      }
    });
  }

  // Live preview for icon and title
  document.getElementById('menuIcon').addEventListener('input', updatePreview);
  document.getElementById('menuTitle').addEventListener('input', updatePreview);
});

function updatePreview() {
  var icon = document.getElementById('menuIcon').value || 'fe fe-link';
  var title = document.getElementById('menuTitle').value || '<?=lang("Menu Item")?>';
  document.getElementById('iconPreview').innerHTML = '<i class="' + escapeHtml(icon) + '"></i> <span id="titlePreview">' + escapeHtml(title) + '</span>';
}

function escapeHtml(text) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(text));
  return div.innerHTML;
}

function openAddModal() {
  isEditMode = false;
  document.getElementById('modalTitle').textContent = '<?=lang("Add Menu Item")?>';
  document.getElementById('menuForm').reset();
  document.getElementById('menuId').value = '';
  document.getElementById('menuStatus').checked = true;
  
  // Check "everyone" by default
  document.querySelectorAll('.role-checkbox').forEach(function(cb) {
    cb.checked = cb.value === 'everyone';
  });
  
  updatePreview();
}

function openEditModal(id) {
  isEditMode = true;
  document.getElementById('modalTitle').textContent = '<?=lang("Edit Menu Item")?>';
  
  // Fetch item data
  $.ajax({
    url: '<?=cn("menus/ajax_get_item")?>',
    type: 'POST',
    data: {
      id: id,
      <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        var item = response.data;
        document.getElementById('menuId').value = item.id;
        document.getElementById('menuTitle').value = item.title;
        document.getElementById('menuUrl').value = item.url;
        document.getElementById('menuIcon').value = item.icon || '';
        document.getElementById('menuNewTab').checked = item.new_tab == 1;
        document.getElementById('menuStatus').checked = item.status == 1;
        
        // Set roles
        document.querySelectorAll('.role-checkbox').forEach(function(cb) {
          cb.checked = item.roles && item.roles.includes(cb.value);
        });
        
        updatePreview();
        $('#menuModal').modal('show');
      } else {
        showToast('error', response.message);
      }
    },
    error: function() {
      showToast('error', '<?=lang("An error occurred")?>');
    }
  });
}

// Edit button click handler
$(document).on('click', '.edit-btn', function() {
  var id = $(this).data('id');
  openEditModal(id);
});

// Delete button click handler
$(document).on('click', '.delete-btn', function() {
  var id = $(this).data('id');
  document.getElementById('deleteItemId').value = id;
  $('#deleteModal').modal('show');
});

// Confirm delete
$('#confirmDeleteBtn').on('click', function() {
  var id = document.getElementById('deleteItemId').value;
  
  $.ajax({
    url: '<?=cn("menus/ajax_delete")?>',
    type: 'POST',
    data: {
      id: id,
      <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        showToast('success', response.message);
        $('tr[data-id="' + id + '"]').fadeOut(300, function() {
          $(this).remove();
          if ($('#menuList tr').length === 0) {
            location.reload();
          }
        });
        $('#deleteModal').modal('hide');
      } else {
        showToast('error', response.message);
      }
    },
    error: function() {
      showToast('error', '<?=lang("An error occurred")?>');
    }
  });
});

// Toggle status
$(document).on('change', '.toggle-status', function() {
  var id = $(this).data('id');
  var checkbox = $(this);
  
  $.ajax({
    url: '<?=cn("menus/ajax_toggle_status")?>',
    type: 'POST',
    data: {
      id: id,
      <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        showToast('success', response.message);
      } else {
        checkbox.prop('checked', !checkbox.prop('checked'));
        showToast('error', response.message);
      }
    },
    error: function() {
      checkbox.prop('checked', !checkbox.prop('checked'));
      showToast('error', '<?=lang("An error occurred")?>');
    }
  });
});

// Form submission
$('#menuForm').on('submit', function(e) {
  e.preventDefault();
  
  var formData = $(this).serialize();
  var url = isEditMode ? '<?=cn("menus/ajax_update")?>' : '<?=cn("menus/ajax_add")?>';
  
  // Add CSRF token
  formData += '&<?=$this->security->get_csrf_token_name()?>=<?=$this->security->get_csrf_hash()?>';
  
  $.ajax({
    url: url,
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        showToast('success', response.message);
        $('#menuModal').modal('hide');
        setTimeout(function() {
          location.reload();
        }, 500);
      } else {
        showToast('error', response.message);
      }
    },
    error: function() {
      showToast('error', '<?=lang("An error occurred")?>');
    }
  });
});

// Save order after drag and drop
function saveOrder() {
  var order = [];
  $('#menuList tr').each(function() {
    var id = $(this).data('id');
    if (id) {
      order.push(id);
    }
  });
  
  if (order.length === 0) return;
  
  $.ajax({
    url: '<?=cn("menus/ajax_reorder")?>',
    type: 'POST',
    data: {
      order: order,
      <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        showToast('success', response.message);
      } else {
        showToast('error', response.message);
      }
    },
    error: function() {
      showToast('error', '<?=lang("An error occurred")?>');
    }
  });
}

// Helper function to show toast notifications
function showToast(type, message) {
  if (typeof $.toast === 'function') {
    $.toast({
      heading: type === 'success' ? '<?=lang("Success")?>' : '<?=lang("Error")?>',
      text: message,
      showHideTransition: 'slide',
      icon: type,
      position: 'top-right',
      hideAfter: 3000
    });
  } else {
    alert(message);
  }
}
</script>
