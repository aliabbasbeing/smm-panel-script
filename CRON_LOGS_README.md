# Cron Job Logging and Management System

## Overview
This system provides comprehensive logging and management capabilities for all cron jobs in the SMM Panel. It automatically captures execution details, provides admin interface for monitoring, and sends notifications on failures.

## Features

### 1. Automatic Cron Detection & Logging
- Automatically detects and logs all existing cron endpoints
- Captures new cron endpoints added in the future without code changes
- Logs execution time, status, response codes, and error messages

### 2. Database Tables
- `cron_logs`: Stores all cron execution logs
- `cron_settings`: Stores system configuration

### 3. Admin Interface
- View all cron logs with filtering and search
- Real-time statistics dashboard for each cron job
- Filter by cron name, status, date range
- Pagination for large datasets
- View detailed log information
- Manual cleanup of old logs

### 4. Notifications
- Optional email notifications for failed cron executions
- Configurable notification email address

### 5. Log Retention
- Configurable retention period (default: 30 days)
- Automatic cleanup of old logs

## Installation

### Step 1: Database Migration
Run the SQL migration to create required tables:

```bash
mysql -u your_user -p your_database < database/cron-logs.sql
```

Or manually execute the SQL in your database management tool (phpMyAdmin, etc.)

### Step 2: Verify Installation
The following files have been added:

**Library:**
- `app/libraries/Cron_logger.php` - Core logging library

**Module:**
- `app/modules/cron_logs/controllers/Cron_logs.php` - Admin controller
- `app/modules/cron_logs/models/Cron_logs_model.php` - Database model
- `app/modules/cron_logs/views/index.php` - Main logs listing page
- `app/modules/cron_logs/views/view.php` - Log details page
- `app/modules/cron_logs/views/settings.php` - Settings page

**Hook:**
- `app/hooks/Cron_auto_logger.php` - Auto-detection hook

**Configuration:**
- `app/config/hooks.php` - Hook configuration (updated)
- `app/config/config.php` - Enabled hooks (updated)

### Step 3: Access Admin Interface
Navigate to: `yoursite.com/cron_logs`

Note: Only admin users can access the cron logs interface.

## Usage

### Viewing Cron Logs
1. Login as admin
2. Navigate to `/cron_logs`
3. Use filters to find specific logs:
   - Cron Name: Filter by specific cron job
   - Status: Filter by success/failed/running
   - Date Range: Filter by execution date

### Viewing Statistics
The main dashboard shows:
- Total runs for each cron
- Success rate
- Last execution time
- Average execution time

### Configuring Settings
1. Navigate to `/cron_logs/settings`
2. Configure:
   - **Email Notifications**: Enable/disable failure notifications
   - **Notification Email**: Email address to receive alerts
   - **Log Retention**: Number of days to keep logs (default: 30)

### Manual Cleanup
To manually clean up old logs:
1. Navigate to `/cron_logs`
2. Click "Cleanup Old Logs" button

## Existing Cron Endpoints (Already Integrated)

The following cron endpoints are already integrated with logging:

1. **API Provider Crons:**
   - `/cron/order` - Place pending orders
   - `/cron/status` - Update order statuses
   - `/cron/sync_services` - Sync services from API providers
   - `/cron/status_subscriptions` - Update subscription statuses
   - `/cron/refill` - Process refill orders

2. **Currency Cron:**
   - `/currencies/cron_fetch_rates` - Update exchange rates

3. **Email Marketing Cron:**
   - `/cron/email_marketing` - Send email campaigns

4. **WhatsApp Marketing Cron:**
   - `/whatsapp_cron/run` - Send WhatsApp campaigns

5. **Child Panel Cron:**
   - `/cron/check_panel_status` - Check child panel status
   - `/cron/childpanel` - Process child panel tasks

## Adding New Cron Jobs

### Method 1: Automatic (Recommended)
New cron endpoints are automatically detected and logged if they follow naming conventions:
- Controller name contains "cron" (e.g., `my_cron.php`)
- Method name is "cron" or "run"
- Controller name ends with "_cron"

### Method 2: Manual Integration
For custom cron jobs, use the Cron_logger library:

```php
// In your controller constructor
$this->load->library('cron_logger');

// In your cron method
public function my_cron_job() {
    $start_time = microtime(true);
    $log_id = $this->cron_logger->start('my_cron_job');
    
    try {
        // Your cron logic here
        
        $execution_time = microtime(true) - $start_time;
        $this->cron_logger->end($log_id, 'success', 200, 'Job completed', $execution_time);
    } catch (Exception $e) {
        $execution_time = microtime(true) - $start_time;
        $this->cron_logger->end($log_id, 'failed', 500, $e->getMessage(), $execution_time);
    }
}
```

### Method 3: Using Execute Wrapper
```php
public function my_cron_job() {
    $this->load->library('cron_logger');
    
    $this->cron_logger->execute('my_cron_job', function() {
        // Your cron logic here
        // Return array with status if needed
        return ['status' => 'success', 'message' => 'Completed'];
    });
}
```

## Cron Logger Library API

### `start($cron_name)`
Start logging a cron execution
- **Parameters:** `$cron_name` - Name of the cron job
- **Returns:** Log ID

### `end($log_id, $status, $response_code, $response_message, $execution_time)`
End logging a cron execution
- **Parameters:**
  - `$log_id` - Log ID from start()
  - `$status` - 'success' or 'failed'
  - `$response_code` - HTTP status code or custom code
  - `$response_message` - Optional message
  - `$execution_time` - Time in seconds
- **Returns:** Boolean

### `log($cron_name, $status, $response_code, $response_message, $execution_time)`
Log a complete execution in one call
- **Parameters:** Same as start() + end() combined
- **Returns:** Log ID

### `execute($cron_name, $callback)`
Execute and log a cron function
- **Parameters:**
  - `$cron_name` - Name of the cron job
  - `$callback` - Function to execute
- **Returns:** Return value from callback

## Database Schema

### cron_logs Table
```sql
- id (auto-increment)
- cron_name (varchar 255)
- executed_at (datetime)
- status (enum: success, failed, running)
- response_code (int)
- response_message (text)
- execution_time (decimal 10,4)
- created (datetime)
```

### cron_settings Table
```sql
- id (auto-increment)
- setting_key (varchar 100)
- setting_value (text)
- changed (datetime)
- created (datetime)
```

## Troubleshooting

### Logs Not Appearing
1. Check if database tables are created: `SHOW TABLES LIKE 'cron_%'`
2. Verify hooks are enabled in `app/config/config.php`: `$config['enable_hooks'] = TRUE;`
3. Check if cron_logger library is loaded in the cron controller

### Email Notifications Not Working
1. Navigate to `/cron_logs/settings`
2. Ensure "Enable Email Notifications" is checked
3. Verify notification email address is correct
4. Check email configuration in panel settings

### Performance Issues
- Reduce log retention period
- Run cleanup regularly
- Add indexes to cron_logs table if needed

## Cron Job Setup Examples

### Using wget
```bash
# Every 5 minutes - Process orders
*/5 * * * * wget --spider -o - https://yoursite.com/cron/order >/dev/null 2>&1

# Every 10 minutes - Update order status
*/10 * * * * wget --spider -o - https://yoursite.com/cron/status >/dev/null 2>&1

# Every hour - Update currency rates
0 * * * * wget --spider -o - https://yoursite.com/currencies/cron_fetch_rates >/dev/null 2>&1
```

### Using curl
```bash
# Every 5 minutes - Process orders
*/5 * * * * curl -s https://yoursite.com/cron/order >/dev/null 2>&1

# Every 10 minutes - Update order status
*/10 * * * * curl -s https://yoursite.com/cron/status >/dev/null 2>&1
```

## Security Considerations

1. **Token Authentication**: Some cron endpoints use token authentication. Example:
   ```
   /currencies/cron_fetch_rates?token=YOUR_TOKEN
   ```

2. **Access Control**: The cron logs interface is protected and only accessible to admin users

3. **Hook Safety**: The auto-detection hook only monitors, it doesn't modify execution

## Future Enhancements

Potential improvements for future versions:
- Manual trigger cron jobs from admin interface
- Cron job scheduling from admin panel
- Email digest of all cron executions
- Slack/Discord notifications
- Export logs to CSV/Excel
- Cron job dependency management
- Retry failed cron jobs

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review the database logs for errors
3. Contact system administrator

---

**Version:** 1.0.0  
**Last Updated:** 2025-11-17
