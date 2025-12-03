<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.whatsapp-header {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    padding: 20px;
    border-radius: 8px 8px 0 0;
}
.device-status-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.3s ease;
}
.device-status-card.connected {
    border-color: #25D366;
}
.device-status-card.disconnected {
    border-color: #dc3545;
}
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    animation: pulse 2s infinite;
}
.status-indicator.connected {
    background: #25D366;
}
.status-indicator.disconnected {
    background: #dc3545;
}
.status-indicator.checking {
    background: #ffc107;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
.qr-container {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
}
.qr-container img {
    max-width: 280px;
    max-height: 280px;
}
.qr-placeholder {
    text-align: center;
    color: #6c757d;
}
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
}
.stats-card.green {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.stats-card.orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.health-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.health-item:last-child {
    border-bottom: none;
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
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fa fa-whatsapp text-success"></i> <?=lang("WhatsApp Manager")?>
    </h1>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs nav-tabs-whatsapp mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="<?=cn('whatsapp/device')?>">
            <i class="fe fe-smartphone"></i> <?=lang("Device")?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?=cn('whatsapp/notifications')?>">
            <i class="fe fe-bell"></i> <?=lang("Notifications")?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?=cn('whatsapp/logs')?>">
            <i class="fe fe-list"></i> <?=lang("Logs")?>
        </a>
    </li>
</ul>

<div class="row">
    <!-- Connection Status Card -->
    <div class="col-md-8">
        <div class="card device-status-card" id="statusCard">
            <div class="whatsapp-header">
                <h4 class="mb-0">
                    <i class="fa fa-whatsapp"></i> <?=lang("Device Connection")?>
                </h4>
            </div>
            <div class="card-body">
                <!-- Connection Status -->
                <div class="d-flex align-items-center mb-4">
                    <div class="mr-3">
                        <span class="status-indicator checking" id="statusIndicator"></span>
                        <strong id="statusText"><?=lang("Checking...")?></strong>
                    </div>
                    <div class="ml-auto">
                        <button class="btn btn-sm btn-outline-primary" id="refreshStatusBtn">
                            <i class="fe fe-refresh-cw"></i> <?=lang("Refresh")?>
                        </button>
                    </div>
                </div>

                <!-- Phone Number Display -->
                <div class="mb-4" id="phoneNumberSection" style="display: none;">
                    <label class="text-muted"><?=lang("Connected Phone Number")?></label>
                    <h3 id="phoneNumberDisplay" class="text-success">-</h3>
                </div>

                <!-- QR Code Section -->
                <div id="qrSection">
                    <h5 class="mb-3"><i class="fe fe-camera"></i> <?=lang("QR Code")?></h5>
                    <div class="qr-container" id="qrContainer">
                        <div class="qr-placeholder">
                            <i class="fe fe-loader" style="font-size: 2rem;"></i>
                            <p class="mt-2"><?=lang("Loading QR code...")?></p>
                        </div>
                    </div>
                    <p class="text-muted mt-2 small">
                        <i class="fe fe-info"></i> <?=lang("Scan this QR code with your WhatsApp app to connect.")?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 pt-3 border-top">
                    <button class="btn btn-danger" id="logoutBtn" disabled>
                        <i class="fe fe-log-out"></i> <?=lang("Logout / Disconnect")?>
                    </button>
                    <button class="btn btn-info ml-2" id="pingBtn">
                        <i class="fe fe-zap"></i> <?=lang("Ping Server")?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-md-4">
        <!-- Health Status Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fe fe-activity text-success"></i> <?=lang("Server Health")?></h5>
            </div>
            <div class="card-body" id="healthData">
                <div class="text-center text-muted py-3">
                    <i class="fe fe-loader"></i> <?=lang("Loading...")?>
                </div>
            </div>
        </div>

        <!-- API Configuration Card -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fe fe-settings text-primary"></i> <?=lang("API Configuration")?></h5>
            </div>
            <div class="card-body">
                <form id="configForm">
                    <div class="form-group">
                        <label><?=lang("API URL")?> <span class="text-danger">*</span></label>
                        <input type="text" name="url" class="form-control" 
                               value="<?=isset($config->url) ? html_escape($config->url) : ''?>"
                               placeholder="http://localhost:3000" required>
                    </div>
                    <div class="form-group">
                        <label><?=lang("API Key")?> <span class="text-danger">*</span></label>
                        <input type="text" name="api_key" class="form-control"
                               value="<?=isset($config->api_key) ? html_escape($config->api_key) : ''?>"
                               placeholder="Your API key" required>
                    </div>
                    <div class="form-group">
                        <label><?=lang("Admin Phone")?> <span class="text-danger">*</span></label>
                        <input type="text" name="admin_phone" class="form-control"
                               value="<?=isset($config->admin_phone) ? html_escape($config->admin_phone) : ''?>"
                               placeholder="+923001234567" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fe fe-save"></i> <?=lang("Save Configuration")?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var csrfName = '<?=$this->security->get_csrf_token_name()?>';
    var csrfHash = '<?=$this->security->get_csrf_hash()?>';
    var isConfigured = <?=$is_configured ? 'true' : 'false'?>;
    var refreshInterval = null;
    var isConnected = false;

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

    // Update status display
    function updateStatus(connected, phoneNumber) {
        isConnected = connected;
        var $indicator = $('#statusIndicator');
        var $text = $('#statusText');
        var $card = $('#statusCard');
        var $qrSection = $('#qrSection');
        var $phoneSection = $('#phoneNumberSection');
        var $logoutBtn = $('#logoutBtn');

        $indicator.removeClass('connected disconnected checking');
        $card.removeClass('connected disconnected');

        if (connected) {
            $indicator.addClass('connected');
            $card.addClass('connected');
            $text.text('<?=lang("Connected")?>');
            $qrSection.hide();
            $phoneSection.show();
            $('#phoneNumberDisplay').text(phoneNumber || '-');
            $logoutBtn.prop('disabled', false);
        } else {
            $indicator.addClass('disconnected');
            $card.addClass('disconnected');
            $text.text('<?=lang("Disconnected")?>');
            $qrSection.show();
            $phoneSection.hide();
            $logoutBtn.prop('disabled', true);
            fetchQR();
        }
    }

    // Fetch status
    function fetchStatus() {
        if (!isConfigured) {
            updateStatus(false, null);
            return;
        }

        $.ajax({
            url: '<?=cn("whatsapp/refresh_status")?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateStatus(response.connected, response.phoneNumber);
            },
            error: function() {
                updateStatus(false, null);
            }
        });
    }

    // Fetch QR code
    function fetchQR() {
        if (!isConfigured || isConnected) return;

        $.ajax({
            url: '<?=cn("whatsapp/fetch_qr")?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.qr) {
                    var qrData = response.qr;
                    if (qrData.startsWith('data:')) {
                        $('#qrContainer').html('<img src="' + qrData + '" alt="QR Code">');
                    } else if (qrData === 'QR not available') {
                        $('#qrContainer').html('<div class="qr-placeholder"><i class="fe fe-check-circle text-success" style="font-size: 3rem;"></i><p class="mt-2"><?=lang("Already connected or QR not needed")?></p></div>');
                    } else {
                        $('#qrContainer').html('<div class="qr-placeholder"><p>' + qrData + '</p></div>');
                    }
                } else {
                    $('#qrContainer').html('<div class="qr-placeholder"><i class="fe fe-alert-circle text-warning" style="font-size: 2rem;"></i><p class="mt-2"><?=lang("QR code not available")?></p></div>');
                }
            },
            error: function() {
                $('#qrContainer').html('<div class="qr-placeholder"><i class="fe fe-wifi-off text-danger" style="font-size: 2rem;"></i><p class="mt-2"><?=lang("Failed to load QR code")?></p></div>');
            }
        });
    }

    // Fetch health
    function fetchHealth() {
        if (!isConfigured) {
            $('#healthData').html('<div class="alert alert-warning"><i class="fe fe-alert-circle"></i> <?=lang("Configure API first")?></div>');
            return;
        }

        $.ajax({
            url: '<?=cn("whatsapp/get_health")?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    var html = '';
                    var data = response.data;
                    
                    html += '<div class="health-item"><span><?=lang("Status")?></span><strong class="text-success">' + (data.status || '-') + '</strong></div>';
                    html += '<div class="health-item"><span><?=lang("Connected")?></span><strong class="' + (data.connected ? 'text-success' : 'text-danger') + '">' + (data.connected ? 'Yes' : 'No') + '</strong></div>';
                    html += '<div class="health-item"><span><?=lang("Uptime")?></span><strong>' + formatUptime(data.uptime) + '</strong></div>';
                    html += '<div class="health-item"><span><?=lang("Version")?></span><strong>' + (data.version || '-') + '</strong></div>';
                    html += '<div class="health-item"><span><?=lang("PID")?></span><strong>' + (data.pid || '-') + '</strong></div>';
                    
                    $('#healthData').html(html);
                } else {
                    $('#healthData').html('<div class="alert alert-danger"><i class="fe fe-alert-triangle"></i> ' + (response.message || '<?=lang("Failed to fetch health data")?>') + '</div>');
                }
            },
            error: function() {
                $('#healthData').html('<div class="alert alert-danger"><i class="fe fe-wifi-off"></i> <?=lang("Connection error")?></div>');
            }
        });
    }

    // Format uptime
    function formatUptime(seconds) {
        if (!seconds) return '-';
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = Math.floor(seconds % 60);
        return hours + 'h ' + minutes + 'm ' + secs + 's';
    }

    // Auto-refresh every 2 seconds
    function startAutoRefresh() {
        if (refreshInterval) clearInterval(refreshInterval);
        refreshInterval = setInterval(function() {
            fetchStatus();
            if (!isConnected) {
                fetchQR();
            }
        }, 2000);
    }

    // Initial load
    fetchStatus();
    fetchHealth();
    startAutoRefresh();

    // Manual refresh
    $('#refreshStatusBtn').on('click', function() {
        fetchStatus();
        fetchHealth();
        if (!isConnected) fetchQR();
    });

    // Ping
    $('#pingBtn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fe fe-loader spin"></i> Pinging...');
        
        $.ajax({
            url: '<?=cn("whatsapp/ping")?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fe fe-zap"></i> <?=lang("Ping Server")?>');
                if (response.status === 'success') {
                    showMessage('<?=lang("Ping successful!")?>', 'success');
                } else {
                    showMessage(response.message || '<?=lang("Ping failed")?>', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fe fe-zap"></i> <?=lang("Ping Server")?>');
                showMessage('<?=lang("Connection error")?>', 'error');
            }
        });
    });

    // Logout
    $('#logoutBtn').on('click', function() {
        if (!confirm('<?=lang("Are you sure you want to logout from WhatsApp?")?>')) return;
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fe fe-loader spin"></i> <?=lang("Logging out...")?>');
        
        var data = {};
        data[csrfName] = csrfHash;
        
        $.ajax({
            url: '<?=cn("whatsapp/logout")?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="fe fe-log-out"></i> <?=lang("Logout / Disconnect")?>');
                if (response.status === 'success') {
                    showMessage(response.message, 'success');
                    fetchStatus();
                } else {
                    showMessage(response.message || '<?=lang("Logout failed")?>', 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fe fe-log-out"></i> <?=lang("Logout / Disconnect")?>');
                showMessage('<?=lang("Connection error")?>', 'error');
            }
        });
    });

    // Save config
    $('#configForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&' + csrfName + '=' + csrfHash;
        
        $.ajax({
            url: '<?=cn("whatsapp/save_config")?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showMessage(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showMessage(response.message, 'error');
                }
            },
            error: function() {
                showMessage('<?=lang("Failed to save configuration")?>', 'error');
            }
        });
    });

    // Spin animation
    $('<style>.spin { animation: spin 1s linear infinite; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>').appendTo('head');
});
</script>
