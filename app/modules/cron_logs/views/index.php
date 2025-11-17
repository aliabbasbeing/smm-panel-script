<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?></h3>
                    <div class="card-options">
                        <a href="<?php echo cn('cron_logs/settings'); ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-cog"></i> Settings
                        </a>
                        <a href="<?php echo cn('cron_logs/cleanup'); ?>" class="btn btn-sm btn-warning actionItem">
                            <i class="fa fa-trash"></i> Cleanup Old Logs
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <?php if (!empty($statistics)): ?>
                            <?php foreach ($statistics as $stat): ?>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="mb-2 text-truncate" title="<?php echo esc($stat->cron_name); ?>">
                                                <?php echo esc($stat->cron_name); ?>
                                            </h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Total Runs</small>
                                                    <div class="h5 mb-0"><?php echo $stat->total_runs; ?></div>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Success Rate</small>
                                                    <div class="h5 mb-0">
                                                        <?php 
                                                        $success_rate = $stat->total_runs > 0 ? round(($stat->success_count / $stat->total_runs) * 100, 1) : 0;
                                                        echo $success_rate . '%';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                Last run: <?php echo $stat->last_run ? date('Y-m-d H:i', strtotime($stat->last_run)) : 'Never'; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info">No cron logs found. Cron jobs will appear here once they run.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Filters -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="mb-3">Filters</h5>
                            <form id="filter-form" class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cron Name</label>
                                        <select name="filter_cron_name" id="filter_cron_name" class="form-control">
                                            <option value="">All Crons</option>
                                            <?php foreach ($cron_names as $cron): ?>
                                                <option value="<?php echo esc($cron->cron_name); ?>"><?php echo esc($cron->cron_name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="filter_status" id="filter_status" class="form-control">
                                            <option value="">All Statuses</option>
                                            <option value="success">Success</option>
                                            <option value="failed">Failed</option>
                                            <option value="running">Running</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date From</label>
                                        <input type="date" name="filter_date_from" id="filter_date_from" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date To</label>
                                        <input type="date" name="filter_date_to" id="filter_date_to" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" id="apply-filters" class="btn btn-primary">
                                                <i class="fa fa-filter"></i> Apply Filters
                                            </button>
                                            <button type="button" id="reset-filters" class="btn btn-secondary">
                                                <i class="fa fa-refresh"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table id="cron-logs-table" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="25%">Cron Name</th>
                                    <th width="15%">Executed At</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Response Code</th>
                                    <th width="10%">Execution Time</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var cronLogsTable;

$(document).ready(function() {
    // Initialize DataTable
    cronLogsTable = $('#cron-logs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo cn('cron_logs/ajax_get_logs'); ?>',
            type: 'POST',
            data: function(d) {
                d.filter_cron_name = $('#filter_cron_name').val();
                d.filter_status = $('#filter_status').val();
                d.filter_date_from = $('#filter_date_from').val();
                d.filter_date_to = $('#filter_date_to').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'cron_name' },
            { data: 'executed_at' },
            { data: 'status', orderable: false },
            { data: 'response_code' },
            { data: 'execution_time' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
    
    // Apply filters
    $('#apply-filters').on('click', function() {
        cronLogsTable.ajax.reload();
    });
    
    // Reset filters
    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        cronLogsTable.ajax.reload();
    });
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        cronLogsTable.ajax.reload(null, false);
    }, 30000);
});
</script>
