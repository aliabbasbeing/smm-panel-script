<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-clock-o"></i> <?php echo lang("Cron Job Logs"); ?>
                    </h3>
                </div>
                <div class="panel-body">
                    
                    <!-- Statistics Cards -->
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-3">
                            <div class="info-box bg-aqua">
                                <span class="info-box-icon"><i class="fa fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo lang("Total Cron Jobs"); ?></span>
                                    <span class="info-box-number"><?php echo $statistics['total_crons']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="fa fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo lang("Successful (24h)"); ?></span>
                                    <span class="info-box-number"><?php echo $statistics['successful_24h']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-red">
                                <span class="info-box-icon"><i class="fa fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo lang("Failed (24h)"); ?></span>
                                    <span class="info-box-number"><?php echo $statistics['failed_24h']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo lang("Avg Time (24h)"); ?></span>
                                    <span class="info-box-number"><?php echo round($statistics['avg_execution_time_24h'], 2); ?>s</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cron Jobs Summary -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo lang("Cron Jobs Summary"); ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo lang("Cron Name"); ?></th>
                                            <th><?php echo lang("Last Run"); ?></th>
                                            <th><?php echo lang("Status"); ?></th>
                                            <th><?php echo lang("Total Runs"); ?></th>
                                            <th><?php echo lang("Success"); ?></th>
                                            <th><?php echo lang("Failed"); ?></th>
                                            <th><?php echo lang("Avg Time"); ?></th>
                                            <th><?php echo lang("Actions"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($summary)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center"><?php echo lang("No cron jobs found"); ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($summary as $cron): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($cron->cron_name); ?></strong></td>
                                                    <td><?php echo $cron->last_run ? date('Y-m-d H:i:s', strtotime($cron->last_run)) : 'Never'; ?></td>
                                                    <td>
                                                        <?php if ($cron->last_status == 'success'): ?>
                                                            <span class="label label-success"><?php echo lang("Success"); ?></span>
                                                        <?php elseif ($cron->last_status == 'failed'): ?>
                                                            <span class="label label-danger"><?php echo lang("Failed"); ?></span>
                                                        <?php elseif ($cron->last_status == 'rate_limited'): ?>
                                                            <span class="label label-warning"><?php echo lang("Rate Limited"); ?></span>
                                                        <?php else: ?>
                                                            <span class="label label-info"><?php echo lang("Info"); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $cron->total_executions; ?></td>
                                                    <td><span class="text-success"><?php echo $cron->success_count; ?></span></td>
                                                    <td><span class="text-danger"><?php echo $cron->failed_count; ?></span></td>
                                                    <td><?php echo round($cron->avg_execution_time, 2); ?>s</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info btn-trigger-cron" data-url="<?php echo htmlspecialchars($cron->cron_name); ?>">
                                                            <i class="fa fa-play"></i> <?php echo lang("Trigger"); ?>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo lang("Filter Logs"); ?></h4>
                        </div>
                        <div class="panel-body">
                            <form id="filter-form" class="form-inline">
                                <div class="form-group">
                                    <label><?php echo lang("Cron Name"); ?>:</label>
                                    <input type="text" name="cron_name" id="filter-cron-name" class="form-control" placeholder="<?php echo lang("Enter cron name"); ?>">
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang("Status"); ?>:</label>
                                    <select name="status" id="filter-status" class="form-control">
                                        <option value=""><?php echo lang("All"); ?></option>
                                        <option value="success"><?php echo lang("Success"); ?></option>
                                        <option value="failed"><?php echo lang("Failed"); ?></option>
                                        <option value="rate_limited"><?php echo lang("Rate Limited"); ?></option>
                                        <option value="info"><?php echo lang("Info"); ?></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang("Date From"); ?>:</label>
                                    <input type="date" name="date_from" id="filter-date-from" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label><?php echo lang("Date To"); ?>:</label>
                                    <input type="date" name="date_to" id="filter-date-to" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo lang("Filter"); ?></button>
                                <button type="button" id="btn-reset-filter" class="btn btn-default"><i class="fa fa-refresh"></i> <?php echo lang("Reset"); ?></button>
                                <a href="<?php echo cn('cron_logs/export'); ?>" id="btn-export" class="btn btn-success"><i class="fa fa-download"></i> <?php echo lang("Export CSV"); ?></a>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Logs Table -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo lang("Execution Logs"); ?></h4>
                        </div>
                        <div class="panel-body">
                            <div id="logs-container">
                                <div class="text-center">
                                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                                    <p><?php echo lang("Loading logs..."); ?></p>
                                </div>
                            </div>
                            
                            <!-- Pagination -->
                            <div id="pagination-container" class="text-center" style="margin-top: 20px;"></div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo lang("Manage Logs"); ?></h4>
                        </div>
                        <div class="panel-body">
                            <button class="btn btn-warning btn-delete-old" data-days="30">
                                <i class="fa fa-trash"></i> <?php echo lang("Delete logs older than 30 days"); ?>
                            </button>
                            <button class="btn btn-danger btn-clear-all">
                                <i class="fa fa-trash-o"></i> <?php echo lang("Clear all logs"); ?>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var currentPage = 0;
var logsPerPage = 20;
var currentFilters = {};

$(document).ready(function() {
    // Load initial logs
    loadLogs();
    
    // Filter form submission
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 0;
        currentFilters = {
            cron_name: $('#filter-cron-name').val(),
            status: $('#filter-status').val(),
            date_from: $('#filter-date-from').val(),
            date_to: $('#filter-date-to').val()
        };
        loadLogs();
    });
    
    // Reset filter
    $('#btn-reset-filter').on('click', function() {
        $('#filter-form')[0].reset();
        currentPage = 0;
        currentFilters = {};
        loadLogs();
    });
    
    // Trigger cron
    $(document).on('click', '.btn-trigger-cron', function() {
        var cronUrl = $(this).data('url');
        if (!confirm('<?php echo lang("Are you sure you want to trigger this cron?"); ?>')) {
            return;
        }
        
        $.ajax({
            url: '<?php echo cn("cron_logs/trigger"); ?>',
            type: 'POST',
            data: { cron_url: cronUrl },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    alert('<?php echo lang("Cron triggered successfully"); ?>');
                    loadLogs();
                } else {
                    alert(response.message);
                }
            }
        });
    });
    
    // Delete old logs
    $('.btn-delete-old').on('click', function() {
        var days = $(this).data('days');
        if (!confirm('<?php echo lang("Are you sure you want to delete logs older than"); ?> ' + days + ' <?php echo lang("days"); ?>?')) {
            return;
        }
        
        $.ajax({
            url: '<?php echo cn("cron_logs/delete_old"); ?>',
            type: 'POST',
            data: { days: days },
            dataType: 'json',
            success: function(response) {
                alert(response.message);
                if (response.status == 'success') {
                    loadLogs();
                }
            }
        });
    });
    
    // Clear all logs
    $('.btn-clear-all').on('click', function() {
        if (!confirm('<?php echo lang("Are you sure you want to clear all logs? This action cannot be undone."); ?>')) {
            return;
        }
        
        $.ajax({
            url: '<?php echo cn("cron_logs/clear_all"); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                alert(response.message);
                if (response.status == 'success') {
                    location.reload();
                }
            }
        });
    });
    
    // Update export link with filters
    $('#btn-export').on('click', function(e) {
        var url = '<?php echo cn("cron_logs/export"); ?>';
        var params = [];
        if (currentFilters.cron_name) params.push('cron_name=' + encodeURIComponent(currentFilters.cron_name));
        if (currentFilters.status) params.push('status=' + encodeURIComponent(currentFilters.status));
        if (currentFilters.date_from) params.push('date_from=' + encodeURIComponent(currentFilters.date_from));
        if (currentFilters.date_to) params.push('date_to=' + encodeURIComponent(currentFilters.date_to));
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        $(this).attr('href', url);
    });
});

function loadLogs(page) {
    page = page || 0;
    
    var params = $.extend({}, currentFilters, {
        limit: logsPerPage,
        offset: page * logsPerPage
    });
    
    $.ajax({
        url: '<?php echo cn("cron_logs/ajax_get_logs"); ?>',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                renderLogs(response.data.logs);
                renderPagination(response.data.total, page);
            }
        }
    });
}

function renderLogs(logs) {
    var html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
    html += '<thead><tr>';
    html += '<th><?php echo lang("ID"); ?></th>';
    html += '<th><?php echo lang("Cron Name"); ?></th>';
    html += '<th><?php echo lang("Executed At"); ?></th>';
    html += '<th><?php echo lang("Status"); ?></th>';
    html += '<th><?php echo lang("Response Code"); ?></th>';
    html += '<th><?php echo lang("Execution Time"); ?></th>';
    html += '<th><?php echo lang("Message"); ?></th>';
    html += '</tr></thead><tbody>';
    
    if (logs.length === 0) {
        html += '<tr><td colspan="7" class="text-center"><?php echo lang("No logs found"); ?></td></tr>';
    } else {
        logs.forEach(function(log) {
            html += '<tr>';
            html += '<td>' + log.id + '</td>';
            html += '<td>' + escapeHtml(log.cron_name) + '</td>';
            html += '<td>' + log.executed_at + '</td>';
            html += '<td>';
            if (log.status == 'success') {
                html += '<span class="label label-success"><?php echo lang("Success"); ?></span>';
            } else if (log.status == 'failed') {
                html += '<span class="label label-danger"><?php echo lang("Failed"); ?></span>';
            } else if (log.status == 'rate_limited') {
                html += '<span class="label label-warning"><?php echo lang("Rate Limited"); ?></span>';
            } else {
                html += '<span class="label label-info"><?php echo lang("Info"); ?></span>';
            }
            html += '</td>';
            html += '<td>' + (log.response_code || 'N/A') + '</td>';
            html += '<td>' + (log.execution_time ? log.execution_time + 's' : 'N/A') + '</td>';
            html += '<td>' + (log.response_message ? '<small>' + escapeHtml(truncate(log.response_message, 100)) + '</small>' : '') + '</td>';
            html += '</tr>';
        });
    }
    
    html += '</tbody></table></div>';
    $('#logs-container').html(html);
}

function renderPagination(total, currentPage) {
    var totalPages = Math.ceil(total / logsPerPage);
    
    if (totalPages <= 1) {
        $('#pagination-container').html('');
        return;
    }
    
    var html = '<ul class="pagination">';
    
    // Previous button
    if (currentPage > 0) {
        html += '<li><a href="#" onclick="loadLogs(' + (currentPage - 1) + '); return false;">&laquo;</a></li>';
    }
    
    // Page numbers
    for (var i = 0; i < totalPages; i++) {
        if (i == currentPage) {
            html += '<li class="active"><a href="#">' + (i + 1) + '</a></li>';
        } else {
            html += '<li><a href="#" onclick="loadLogs(' + i + '); return false;">' + (i + 1) + '</a></li>';
        }
    }
    
    // Next button
    if (currentPage < totalPages - 1) {
        html += '<li><a href="#" onclick="loadLogs(' + (currentPage + 1) + '); return false;">&raquo;</a></li>';
    }
    
    html += '</ul>';
    $('#pagination-container').html(html);
}

function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function truncate(str, n) {
    return (str.length > n) ? str.substr(0, n-1) + '...' : str;
}
</script>
