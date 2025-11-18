# Cron Logging System

This document describes the cron logging and management system for the SMM Panel.

## Overview

The cron logging system automatically tracks all cron job executions, providing administrators with detailed insights into cron performance, failures, and execution history.

## Features

- **Automatic Logging**: All cron jobs are automatically logged without requiring extra coding
- **Detailed Metrics**: Tracks execution time, status, response codes, and error messages
- **Web Interface**: Admin panel to view, search, and filter cron logs
- **Performance Monitoring**: Last run summary for all cron jobs
- **Filtering & Search**: Filter by cron name, status, date range
- **Maintenance**: Cleanup old logs to save database space
- **Minimal Impact**: Designed to not slow down cron execution

## Installation

1. **Create Database Table**
   ```bash
   mysql -u [username] -p [database_name] < database/cron-logs.sql
   ```

2. **Verify Installation**
   - Login to admin panel
   - Navigate to sidebar menu → "Cron Logs"
   - The page should display with empty logs (until crons run)

## Logged Cron Jobs

The following cron jobs are automatically logged:

### Order & Status Crons
- `/cron/order` - Place pending orders via API providers
- `/cron/status` - Check order status updates
- `/cron/status_subscriptions` - Check subscription status

### Service Management
- `/cron/sync_services` - Sync services from API providers

### Completion Tracking
- `/cron/completion_time` - Calculate average completion times

### Marketing Crons
- `/cron/email_marketing` - Email marketing campaigns
- `/whatsapp_cron/run` - WhatsApp marketing campaigns

### Payment Verification
- `/imap-auto-verify` - IMAP email verification for payments

## Admin Interface

### Viewing Logs

Navigate to: **Admin Panel → Cron Logs**

The interface provides:
- **Last Run Summary**: Quick overview of all cron jobs with their last execution status
- **Detailed Logs Table**: Full execution history with:
  - Cron name
  - Execution timestamp
  - Status (Success/Failed)
  - Response code
  - Execution time (in seconds)
  - Response message

### Filtering

Filter logs by:
- **Cron Name**: Select specific cron job
- **Status**: Success or Failed
- **Date Range**: From date and To date

### Actions

Available actions:
- **Delete**: Remove selected log entries
- **Cleanup Old Logs**: Delete logs older than 30 days
- **Clear All**: Remove all logs (use with caution)

## Usage for Developers

### Adding Logging to New Cron Jobs

When creating a new cron job, add logging support:

```php
class My_cron extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->library('cron_logger');
    }
    
    public function run(){
        // Start logging
        $log_id = $this->cron_logger->start('cron/my_custom_cron');
        
        try {
            // Your cron logic here
            $result = $this->do_something();
            
            // Log success
            $this->cron_logger->end($log_id, 'Success', 200, 'Processed successfully');
            
        } catch (Exception $e) {
            // Log failure
            $this->cron_logger->end($log_id, 'Failed', 500, $e->getMessage());
        }
    }
}
```

### Using the Cron Logger Library

The `Cron_logger` library provides these methods:

#### 1. Start Logging
```php
$log_id = $this->cron_logger->start('cron/my_cron');
```

#### 2. End Logging
```php
$this->cron_logger->end($log_id, $status, $response_code, $message);
```
- `$status`: 'Success' or 'Failed'
- `$response_code`: HTTP code (200, 500, etc.)
- `$message`: Optional description or error message

#### 3. One-Call Logging
```php
$this->cron_logger->log('cron/my_cron', 'Success', 200, 'Message', 1.234);
```

#### 4. Get Last Log
```php
$last_log = $this->cron_logger->get_last_log('cron/my_cron');
```

#### 5. Cleanup Old Logs
```php
$deleted = $this->cron_logger->cleanup(30); // Delete logs older than 30 days
```

## Database Schema

Table: `cron_logs`

| Column | Type | Description |
|--------|------|-------------|
| id | int(10) | Primary key |
| cron_name | varchar(255) | Cron identifier/URL |
| executed_at | datetime | Execution timestamp |
| status | enum | 'Success' or 'Failed' |
| response_code | int(11) | HTTP response code |
| response_message | text | Output or error message |
| execution_time | decimal(10,3) | Execution time in seconds |
| created | datetime | Record creation timestamp |

Indexes:
- Primary key on `id`
- Index on `cron_name` (for filtering)
- Index on `executed_at` (for date queries)
- Index on `status` (for status filtering)

## Crontab Configuration

The cron logging system works with existing crontab configurations. Example:

```bash
# Order processing (every 5 minutes)
*/5 * * * * curl -s "https://yourdomain.com/cron/order" >/dev/null 2>&1

# Status updates (every 10 minutes)
*/10 * * * * curl -s "https://yourdomain.com/cron/status" >/dev/null 2>&1

# Service sync (daily at 2 AM)
0 2 * * * curl -s "https://yourdomain.com/cron/sync_services" >/dev/null 2>&1

# Email marketing (every minute)
* * * * * curl -s "https://yourdomain.com/cron/email_marketing?token=YOUR_TOKEN" >/dev/null 2>&1
```

Both `wget` and `curl` are supported.

## Performance Considerations

- Logging is lightweight and adds minimal overhead (~10-50ms per cron)
- Logs are written asynchronously to the database
- Failed database writes won't break cron execution
- Execution time is calculated using PHP's `microtime(true)` for precision
- Recommended: Clean up logs older than 30-90 days to maintain performance

## Troubleshooting

### Logs not appearing

1. Check database table exists:
   ```sql
   SHOW TABLES LIKE 'cron_logs';
   ```

2. Verify cron is actually running:
   ```bash
   tail -f /var/log/cron
   ```

3. Check PHP error log for any issues

### Permission errors

Ensure the web server user has write access to:
- Database tables
- Log files (if using file logging)

## Maintenance

### Regular Cleanup

Set up a monthly cleanup job:
```bash
# Clean logs older than 30 days (runs 1st of every month)
0 0 1 * * curl -s "https://yourdomain.com/cron_logs/ajax_actions_option" -d "type=cleanup_old" >/dev/null 2>&1
```

Or use the admin panel: **Cron Logs → Action → Cleanup Old Logs**

## Security

- Cron logs page requires admin authentication
- Sensitive data in response messages is truncated (100 chars max in list view)
- Token-protected crons (email, whatsapp) verify tokens before logging

## Future Enhancements

Planned features:
- Email notifications on cron failures
- Dashboard widget showing cron health
- API endpoint for external monitoring tools
- Export logs to CSV/JSON
- Cron scheduling interface

## Support

For issues or questions:
- Check the logs: `app/logs/`
- Enable debug mode in CodeIgniter for detailed errors
- Review cron output directly via browser

---

**Version**: 1.0  
**Last Updated**: November 2025
