# Cron Logging and Management System

This feature provides comprehensive logging and management capabilities for all cron jobs in the SMM panel.

## Features

### 1. Automatic Cron Logging
- Logs every cron execution automatically
- Records execution time, status, response codes, and messages
- Works with both `wget` and `curl`
- No extra coding needed for new crons

### 2. Database Table
The `cron_logs` table stores all cron execution data:
- `id` - Auto-increment primary key
- `cron_name` - URL or identifier of the cron job
- `executed_at` - Timestamp when the cron was executed
- `status` - Execution status (success, failed, rate_limited)
- `response_code` - HTTP response code
- `response_message` - Output or error message
- `execution_time` - Total execution time in seconds

### 3. Admin Dashboard
Access the cron logs dashboard at: `/cron_logs/dashboard`

Features include:
- Overview of all cron jobs with last run status
- Success rate visualization
- Manual trigger buttons for each cron
- Quick statistics (total runs, success/failure counts)

### 4. Detailed Logs View
Access detailed logs at: `/cron_logs`

Features include:
- Search by cron name
- Filter by status (success, failed, rate_limited)
- Filter by date range
- Pagination for large datasets
- View detailed log entries
- Cleanup old logs functionality

### 5. Failure Notifications
Optional email notifications when a cron fails:
- Enable in settings: `cron_failure_notifications`
- Set admin email in settings: `admin_email`

## Installation

### 1. Database Setup
Run the SQL migration to create the `cron_logs` table:

```sql
-- Execute this SQL in your database
source database/cron-logs.sql;
```

Or manually execute:

```sql
CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(255) NOT NULL COMMENT 'URL or identifier of the cron job',
  `executed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the cron was executed',
  `status` enum('success','failed','rate_limited') NOT NULL DEFAULT 'success' COMMENT 'Execution status',
  `response_code` int(11) DEFAULT NULL COMMENT 'HTTP response code if applicable',
  `response_message` text DEFAULT NULL COMMENT 'Output or error message',
  `execution_time` decimal(10,3) DEFAULT NULL COMMENT 'Total execution time in seconds',
  PRIMARY KEY (`id`),
  KEY `idx_cron_name` (`cron_name`),
  KEY `idx_executed_at` (`executed_at`),
  KEY `idx_status` (`status`),
  KEY `idx_cron_status` (`cron_name`, `status`),
  KEY `idx_date_status` (`executed_at`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Verify Files
Ensure these files are present:

**Core Files:**
- `/app/libraries/Cron_logger.php` - Main logging library
- `/app/core/Base_cron.php` - Base class for cron controllers

**Module Files:**
- `/app/modules/cron_logs/controllers/Cron_logs.php`
- `/app/modules/cron_logs/models/Cron_logs_model.php`
- `/app/modules/cron_logs/views/dashboard.php`
- `/app/modules/cron_logs/views/index.php`
- `/app/modules/cron_logs/views/view.php`

### 3. Access Control
Make sure only admin users can access the cron logs module by adding authentication checks in the controller if not already present in your base controller.

## Usage

### Automatically Logged Crons
The following cron endpoints are automatically logged:

1. **Order Processing**: `/cron/order`
2. **Status Updates**: `/cron/status`
3. **Subscription Status**: `/cron/status_subscriptions`
4. **Service Sync**: `/cron/sync_services`
5. **Order Completion**: `/cron/completion_time`
6. **Email Marketing**: `/cron/email_marketing`
7. **WhatsApp Marketing**: `/whatsapp_cron/run`
8. **Currency Rates**: `/currencies/cron_fetch_rates`

### Adding Logging to New Crons

#### Method 1: Use the Cron_logger Library
```php
class My_cron extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('cron_logger');
    }
    
    public function my_cron_job() {
        // Start logging
        $this->cron_logger->start('cron/my_job');
        
        try {
            // Your cron logic here
            $result = $this->do_something();
            
            // Log success
            $this->cron_logger->end('Job completed successfully');
            
        } catch (Exception $e) {
            // Log failure
            $this->cron_logger->fail($e->getMessage());
        }
    }
}
```

#### Method 2: Use the execute_cron Wrapper
```php
class My_cron extends Base_cron {
    
    public function my_cron_job() {
        $this->execute_cron('cron/my_job', function() {
            // Your cron logic here
            $this->do_something();
            return ['status' => 'success', 'message' => 'Completed'];
        });
    }
}
```

### Manual Cron Triggering
You can manually trigger any cron from the dashboard:
1. Go to `/cron_logs/dashboard`
2. Click the "Trigger" button next to any cron
3. Confirm the action
4. The cron will execute and log the result

### API Integration
Trigger crons via AJAX:
```javascript
$.ajax({
    url: '/cron_logs/trigger',
    type: 'POST',
    data: { cron_url: 'https://yoursite.com/cron/order' },
    success: function(response) {
        console.log(response);
    }
});
```

## Maintenance

### Cleanup Old Logs
To keep the database clean, regularly remove old logs:

1. **Via Dashboard**: 
   - Go to `/cron_logs`
   - Click "Cleanup Old Logs"
   - Enter the number of days to retain

2. **Via Code**:
```php
$this->load->library('cron_logger');
$deleted = $this->cron_logger->cleanup(30); // Keep last 30 days
```

3. **Via Cron Job**:
Create a cleanup cron that runs monthly:
```bash
0 0 1 * * wget -O - https://yoursite.com/cron_logs/cleanup_scheduled >/dev/null 2>&1
```

## Monitoring

### View Statistics
Get statistics for any cron:
```php
$this->load->library('cron_logger');
$stats = $this->cron_logger->get_stats('cron/order', 7); // Last 7 days

// Returns:
// - total_runs
// - success_count
// - failed_count
// - rate_limited_count
// - avg_execution_time
// - last_run
```

### Dashboard Metrics
The dashboard shows:
- Total runs in the last 7 days
- Success/failure counts
- Success rate for each cron
- Average execution time
- Last run timestamp and status

## Security

### Token Protection
Some crons are protected with security tokens:
- Email cron: `get_option('email_cron_token')`
- WhatsApp cron: `get_option('whatsapp_cron_token')`
- Currency cron: `get_option('currency_cron_token')`

### Access Control
The cron logs module should be accessible only to administrators. Ensure your authentication middleware is in place.

## Troubleshooting

### Logs Not Appearing
1. Check database connection
2. Verify the `cron_logs` table exists
3. Check file permissions on `/app/libraries/Cron_logger.php`

### Performance Issues
If you have millions of log entries:
1. Run cleanup regularly
2. Add additional indexes if needed
3. Consider archiving old logs to a separate table

### Email Notifications Not Working
1. Check `get_option('cron_failure_notifications')` is set to 1
2. Verify `get_option('admin_email')` is configured
3. Check email configuration in the panel

## Future Enhancements
- Export logs to CSV/Excel
- Real-time cron monitoring dashboard
- Slack/Discord webhook notifications
- Scheduled cleanup cron
- Cron scheduling interface
- Performance analytics and trending

## Support
For issues or questions, please create an issue in the repository.
