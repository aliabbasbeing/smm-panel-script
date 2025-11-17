# Cron Job Logging and Management System

## Overview

This system provides comprehensive logging and management capabilities for all cron jobs in the SMM panel. It automatically detects and logs all current and future cron executions without requiring manual configuration.

## Features

### 1. Automatic Detection
- Automatically detects cron endpoints by URL pattern (`/cron/`, `_cron_`, `cron_`)
- Works with all existing cron jobs
- Future-proof: automatically logs any new cron endpoints added later
- No manual configuration required for new cron jobs

### 2. Comprehensive Logging
- Logs all cron executions with detailed information:
  - Cron name/URL
  - Execution timestamp
  - Status (success, failed, rate_limited, info)
  - HTTP response code
  - Response message
  - Execution time (in seconds)
- Minimal performance impact
- Non-blocking logging operations

### 3. Admin Interface
- **Dashboard**: View statistics and health overview
  - Total number of cron jobs
  - Success/failure counts (last 24 hours)
  - Average execution time
- **Cron Summary**: Overview of all cron jobs
  - Last run timestamp
  - Current status
  - Total executions
  - Success/failure counts
  - Average execution time
- **Detailed Logs**: 
  - Search and filter by cron name, status, date range
  - Pagination for large datasets
  - Export to CSV
- **Manual Trigger**: Run any cron job on demand from the interface
- **Log Management**:
  - Delete logs older than X days
  - Clear all logs

### 4. Notifications (Optional)
- Email notifications for failed cron executions
- Configurable notification settings

### 5. Automatic Cleanup
- Configurable log retention period
- Automatic cleanup of old logs (default: 30 days)

## Installation

### 1. Database Setup

Run the SQL migration file to create the necessary tables:

```bash
mysql -u your_username -p your_database < database/cron-logging-system.sql
```

Or manually execute the SQL from `database/cron-logging-system.sql` in your database.

### 2. Configuration

The system is pre-configured and ready to use. Optional settings can be configured in the admin panel:

- **Notification Email**: Email address to receive failure notifications
- **Enable Notifications**: Turn on/off email notifications (default: off)
- **Log Retention Days**: How long to keep logs (default: 30 days)

### 3. Verify Installation

1. Access the admin panel
2. Navigate to **Settings > Cron Logs**
3. You should see the cron logs dashboard

## Usage

### Accessing Cron Logs

1. Log in to the admin panel
2. Navigate to **Settings > Cron Logs**
3. View the dashboard with statistics and cron job summaries

### Filtering Logs

Use the filter form to search for specific logs:
- **Cron Name**: Search by cron endpoint name
- **Status**: Filter by success, failed, rate_limited, or info
- **Date From/To**: Filter by date range

### Manual Trigger

To manually run a cron job:
1. Find the cron in the summary table
2. Click the **Trigger** button
3. The cron will execute immediately
4. View the results in the logs

### Exporting Logs

1. Apply filters (optional)
2. Click **Export CSV**
3. Download the CSV file with filtered logs

### Managing Logs

- **Delete Old Logs**: Click "Delete logs older than 30 days" to remove old entries
- **Clear All Logs**: Click "Clear all logs" to remove all log entries (cannot be undone)

## Logged Cron Endpoints

The following cron endpoints are automatically logged:

1. `/cron/order` - Process pending orders
2. `/cron/status` - Check order statuses
3. `/cron/status_subscriptions` - Check subscription order statuses
4. `/cron/refill` - Process refill requests
5. `/cron/sync_services` - Sync services from providers
6. `/cron/check_panel_status` - Check child panel statuses
7. `/cron/childpanel` - Renew child panel subscriptions
8. `/cron/email_marketing` - Process email marketing campaigns
9. `/whatsapp_cron/run` - Process WhatsApp marketing campaigns
10. `/currencies/cron_fetch_rates` - Update currency exchange rates
11. `/coinpayments/cron` - Check CoinPayments transactions
12. `/coinbase/cron` - Check Coinbase transactions
13. `/payop/cron` - Check PayOp transactions
14. `/midtrans/cron` - Check Midtrans transactions
15. `/mercadopago/cron` - Check MercadoPago transactions
16. `/imap-auto-verify` - IMAP auto-verification for payments

## Technical Details

### Components

1. **Cron_logger Library** (`app/libraries/Cron_logger.php`)
   - Centralized logging functionality
   - Methods: `log_success()`, `log_failure()`, `log_rate_limited()`, `log_info()`
   - Automatic execution time tracking
   - Email notifications
   - Log cleanup

2. **Cron_monitor Hook** (`app/hooks/Cron_monitor.php`)
   - Auto-detects cron endpoints
   - Initializes logging for detected crons
   - Fallback logging on shutdown

3. **Cron_logs Module** (`app/modules/cron_logs/`)
   - Admin interface
   - Model for database operations
   - Views for displaying logs
   - Controller for handling requests

4. **Database Table** (`cron_logs`)
   - Stores all cron execution logs
   - Indexed for fast querying

### Security

- All cron endpoints require authentication tokens
- Invalid token attempts are logged
- Admin-only access to cron logs interface
- SQL injection protection via CodeIgniter's query builder
- XSS protection in views

### Performance

- Non-blocking logging operations
- Automatic cleanup to prevent database bloat
- Indexed database queries for fast retrieval
- Pagination to handle large datasets
- Only 5% chance of cleanup on each execution (minimal overhead)

## Troubleshooting

### Logs Not Appearing

1. Check that hooks are enabled in `app/config/config.php`:
   ```php
   $config['enable_hooks'] = TRUE;
   ```

2. Verify the database table exists:
   ```sql
   SHOW TABLES LIKE 'cron_logs';
   ```

3. Check PHP error logs for any issues

### Email Notifications Not Working

1. Verify email settings in admin panel
2. Check SMTP configuration
3. Ensure notification email is set
4. Confirm "Enable Notifications" is turned on

### Performance Issues

1. Reduce log retention period
2. Manually clear old logs
3. Check database indexes are present
4. Consider increasing cleanup frequency

## Future Enhancements

Potential improvements for future versions:

- Dashboard widgets for main admin page
- Real-time cron execution monitoring
- Cron job scheduling from admin interface
- Performance metrics and trends
- Alert thresholds for failures
- Integration with external monitoring tools
- API endpoints for external monitoring
- Cron job dependency management

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review PHP error logs
3. Check database for errors
4. Contact support with detailed error information

## Version History

### v1.0.0 (Current)
- Initial release
- Automatic cron detection
- Comprehensive logging
- Admin interface
- Email notifications
- Log management tools
- Integration with all existing crons
