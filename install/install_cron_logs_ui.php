<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cron Logging System - Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        pre {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        code {
            font-family: 'Courier New', monospace;
            color: #d63384;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Cron Logging System Installation</h1>
        
        <div class="info">
            <strong>Installation Status:</strong> Ready to install
        </div>

        <div class="step">
            <h3>Step 1: Database Setup</h3>
            <p>Click the button below to create the <code>cron_logs</code> table in your database.</p>
            <button id="installBtn" onclick="installDatabase()">Install Database Table</button>
            <div id="dbStatus"></div>
        </div>

        <div class="step">
            <h3>Step 2: Verify Installation</h3>
            <p>After installation, verify that the table was created successfully.</p>
            <button id="verifyBtn" onclick="verifyInstallation()" disabled>Verify Installation</button>
            <div id="verifyStatus"></div>
        </div>

        <div class="step">
            <h3>Step 3: Access Dashboard</h3>
            <p>Once installation is complete, you can access the cron logs dashboard:</p>
            <ul>
                <li><a href="<?php echo base_url('cron_logs/dashboard'); ?>" target="_blank">Cron Logs Dashboard</a></li>
                <li><a href="<?php echo base_url('cron_logs'); ?>" target="_blank">View All Logs</a></li>
            </ul>
        </div>

        <div class="step">
            <h3>📚 Documentation</h3>
            <p>For complete documentation, please read the <a href="../database/CRON-LOGGING-README.md" target="_blank">README file</a>.</p>
        </div>
    </div>

    <script>
        function installDatabase() {
            const btn = document.getElementById('installBtn');
            const status = document.getElementById('dbStatus');
            
            btn.disabled = true;
            btn.textContent = 'Installing...';
            
            fetch('install_cron_logs.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'install' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    status.innerHTML = '<div class="success"><strong>✓ Success!</strong> ' + data.message + '</div>';
                    document.getElementById('verifyBtn').disabled = false;
                } else {
                    status.innerHTML = '<div class="error"><strong>✗ Error:</strong> ' + data.message + '</div>';
                    btn.disabled = false;
                    btn.textContent = 'Retry Installation';
                }
            })
            .catch(error => {
                status.innerHTML = '<div class="error"><strong>✗ Error:</strong> ' + error.message + '</div>';
                btn.disabled = false;
                btn.textContent = 'Retry Installation';
            });
        }

        function verifyInstallation() {
            const status = document.getElementById('verifyStatus');
            
            fetch('install_cron_logs.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'verify' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    status.innerHTML = '<div class="success"><strong>✓ Verified!</strong> ' + data.message + '</div>';
                } else {
                    status.innerHTML = '<div class="warning"><strong>⚠ Warning:</strong> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                status.innerHTML = '<div class="error"><strong>✗ Error:</strong> ' + error.message + '</div>';
            });
        }
    </script>
</body>
</html>
