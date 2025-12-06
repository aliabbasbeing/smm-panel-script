<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.whatsapp-header {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    padding: 20px;
    border-radius: 8px 8px 0 0;
}
.nav-tabs-whatsapp .nav-link {
    color: #128C7E;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 12px 20px;
}
.nav-tabs-whatsapp .nav-link.active {
    color: #25D366;
    background: transparent;
    border-bottom-color: #25D366;
}
.nav-tabs-whatsapp .nav-link:hover {
    border-bottom-color: #25D366;
}
.stats-card {
    border-radius: 8px;
    padding: 15px;
    color: white;
}
.stats-card.total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.stats-card.sent {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.stats-card.failed {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}
.stats-card.queued {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.stats-card .stat-value {
    font-size: 2rem;
    font-weight: bold;
}
.stats-card .stat-label {
    opacity: 0.9;
    font-size: 0.85rem;
}
.filter-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.log-status-badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}
.log-status-badge.sent {
    background: #d4edda;
    color: #155724;
}
.log-status-badge.failed {
    background: #f8d7da;
    color: #721c24;
}
.log-status-badge.queued {
    background: #fff3cd;
    color: #856404;
}
.message-preview {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.message-modal-content {
    white-space: pre-wrap;
    word-wrap: break-word;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    font-family: monospace;
}
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fab fa-whatsapp text-success"></i> <?=lang("WhatsApp Manager")?>
    </h1>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs nav-tabs-whatsapp mb-4">
    <li class="nav-item">
        <a class="nav-link" href="<?=cn('whatsapp/device')?>">
            <i class="fe fe-smartphone"></i> <?=lang("Device")?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?=cn('whatsapp/notifications')?>">
            <i class="fe fe-bell"></i> <?=lang("Notifications")?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?=cn('whatsapp/logs')?>">
            <i class="fe fe-list"></i> <?=lang("Logs")?>
        </a>
    </li>
</ul>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card total">
            <div class="stat-value"><?=$stats['total']?></div>
            <div class="stat-label"><?=lang("Total Messages")?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card sent">
            <div class="stat-value"><?=$stats['sent']?></div>
            <div class="stat-label"><?=lang("Sent")?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card failed">
            <div class="stat-value"><?=$stats['failed']?></div>
            <div class="stat-label"><?=lang("Failed")?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card queued">
            <div class="stat-value"><?=$stats['queued']?></div>
            <div class="stat-label"><?=lang("Queued")?></div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="<?=cn('whatsapp/logs')?>">
        <div class="row align-items-center">
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" name="search" class="form-control" 
                           placeholder="<?=lang("Search...")?>"
                           value="<?=html_escape($filters['search'])?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <select name="status" class="form-control">
                        <option value=""><?=lang("All Status")?></option>
                        <option value="sent" <?=$filters['status'] == 'sent' ? 'selected' : ''?>><?=lang("Sent")?></option>
                        <option value="failed" <?=$filters['status'] == 'failed' ? 'selected' : ''?>><?=lang("Failed")?></option>
                        <option value="queued" <?=$filters['status'] == 'queued' ? 'selected' : ''?>><?=lang("Queued")?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" name="phone" class="form-control" 
                           placeholder="<?=lang("Phone number")?>"
                           value="<?=html_escape($filters['phone'])?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="date" name="date_from" class="form-control" 
                           placeholder="<?=lang("From")?>"
                           value="<?=html_escape($filters['date_from'])?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="date" name="date_to" class="form-control" 
                           placeholder="<?=lang("To")?>"
                           value="<?=html_escape($filters['date_to'])?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-search"></i> <?=lang("Filter")?>
                </button>
                <a href="<?=cn('whatsapp/logs')?>" class="btn btn-secondary">
                    <i class="fe fe-x"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="whatsapp-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fe fe-list"></i> <?=lang("Message Logs")?>
        </h4>
        <span class="badge bg-light text-dark"><?=$total?> <?=lang("records")?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th><?=lang("Phone")?></th>
                        <th><?=lang("Event")?></th>
                        <th><?=lang("Message")?></th>
                        <th style="width: 100px;" class="text-center"><?=lang("Status")?></th>
                        <th style="width: 160px;"><?=lang("Date")?></th>
                        <th style="width: 80px;" class="text-center"><?=lang("Actions")?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                    <?php 
                    $startIndex = ($page - 1) * $limit + 1;
                    foreach ($logs as $i => $log): 
                    ?>
                    <tr>
                        <td><?=$startIndex + $i?></td>
                        <td>
                            <strong><?=html_escape($log->phone_number)?></strong>
                        </td>
                        <td>
                            <span class="text-muted"><?=html_escape($log->event_type)?></span>
                        </td>
                        <td>
                            <div class="message-preview" title="<?=html_escape($log->message)?>">
                                <?=html_escape(substr($log->message, 0, 50))?><?=strlen($log->message) > 50 ? '...' : ''?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="log-status-badge <?=$log->status?>"><?=ucfirst($log->status)?></span>
                        </td>
                        <td>
                            <small><?=date('M d, Y H:i', strtotime($log->created_at))?></small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary view-log-btn" 
                                    data-phone="<?=html_escape($log->phone_number)?>"
                                    data-event="<?=html_escape($log->event_type)?>"
                                    data-message="<?=html_escape($log->message)?>"
                                    data-status="<?=html_escape($log->status)?>"
                                    data-response="<?=html_escape($log->response)?>"
                                    data-date="<?=date('M d, Y H:i:s', strtotime($log->created_at))?>">
                                <i class="fe fe-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fe fe-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2"><?=lang("No message logs found")?></p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total > $limit): ?>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted">
                <?=lang("Showing")?> <?=($page - 1) * $limit + 1?> - <?=min($page * $limit, $total)?> <?=lang("of")?> <?=$total?>
            </span>
            <div class="pagination mb-0">
                <?=$links?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Cleanup Logs -->
<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fe fe-trash-2 text-danger"></i> <?=lang("Cleanup Old Logs")?></h5>
    </div>
    <div class="card-body">
        <form id="cleanupForm" class="form-inline">
            <label class="me-2"><?=lang("Delete logs older than")?></label>
            <select name="days" class="form-control me-2" style="width: 150px;">
                <option value="7">7 <?=lang("days")?></option>
                <option value="14">14 <?=lang("days")?></option>
                <option value="30" selected>30 <?=lang("days")?></option>
                <option value="60">60 <?=lang("days")?></option>
                <option value="90">90 <?=lang("days")?></option>
            </select>
            <button type="button" class="btn btn-danger" id="cleanupBtn">
                <i class="fe fe-trash-2"></i> <?=lang("Delete Old Logs")?>
            </button>
        </form>
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
                <p><?=lang("Are you sure you want to delete old logs?")?></p>
                <p class="text-danger small"><?=lang("This action cannot be undone.")?></p>
                <p class="font-weight-bold" id="deleteConfirmDays"></p>
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

<!-- View Log Modal -->
<div class="modal fade" id="viewLogModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fe fe-file-text"></i> <?=lang("Message Details")?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted"><?=lang("Phone Number")?></label>
                        <p id="modalPhone" class="font-weight-bold">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted"><?=lang("Event Type")?></label>
                        <p id="modalEvent" class="font-weight-bold">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted"><?=lang("Status")?></label>
                        <p id="modalStatus">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted"><?=lang("Date")?></label>
                        <p id="modalDate" class="font-weight-bold">-</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted"><?=lang("Message")?></label>
                    <div class="message-modal-content" id="modalMessage">-</div>
                </div>
                <div id="responseSection" style="display: none;">
                    <label class="text-muted"><?=lang("API Response")?></label>
                    <div class="message-modal-content" id="modalResponse">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=lang("Close")?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var csrfName = '<?=$this->security->get_csrf_token_name()?>';
    var csrfHash = '<?=$this->security->get_csrf_hash()?>';

    // Show toast message
    function showMessage(message, type) {
        if (typeof $.toast === 'function') {
            $.toast({
                heading: type === 'success' ? 'Success' : 'Error',
                text: message,
                position: 'top-right',
                loaderBg: type === 'success' ? '#25D366' : '#dc3545',
                icon: type,
                hideAfter: 3000
            });
        } else {
            alert(message);
        }
    }

    // View log details
    $('.view-log-btn').on('click', function() {
        var $btn = $(this);
        
        $('#modalPhone').text($btn.data('phone'));
        $('#modalEvent').text($btn.data('event'));
        $('#modalMessage').text($btn.data('message'));
        $('#modalDate').text($btn.data('date'));
        
        var status = $btn.data('status');
        $('#modalStatus').html('<span class="log-status-badge ' + status + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>');
        
        var response = $btn.data('response');
        if (response && response !== 'null') {
            try {
                var parsed = JSON.parse(response);
                $('#modalResponse').text(JSON.stringify(parsed, null, 2));
            } catch (e) {
                $('#modalResponse').text(response);
            }
            $('#responseSection').show();
        } else {
            $('#responseSection').hide();
        }
        
        $('#viewLogModal').modal('show');
    });

    // Cleanup - show modal
    $('#cleanupBtn').on('click', function() {
        var days = $('select[name="days"]').val();
        $('#deleteConfirmDays').text('<?=lang("Logs older than")?> ' + days + ' <?=lang("days")?>');
        $('#deleteConfirmModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fe fe-loader spin"></i> <?=lang("Deleting...")?>');
        
        var days = $('select[name="days"]').val();
        var data = { days: days };
        data[csrfName] = csrfHash;
        
        $.ajax({
            url: '<?=cn("whatsapp/delete_old_logs")?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fe fe-trash-2"></i> <?=lang("Delete")?>');
                $('#deleteConfirmModal').modal('hide');
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
                $btn.prop('disabled', false).html('<i class="fe fe-trash-2"></i> <?=lang("Delete")?>');
                $('#deleteConfirmModal').modal('hide');
                showMessage('<?=lang("Failed to delete logs")?>', 'error');
            }
        });
    });
    
    // Spin animation
    $('<style>.spin { animation: spin 1s linear infinite; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>').appendTo('head');
});
</script>
