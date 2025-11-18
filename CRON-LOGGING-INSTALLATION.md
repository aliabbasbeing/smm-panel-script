# Cron Logging System - Installation & Quick Start

## Quick Installation

### Step 1: Import Database Table

Run the SQL migration to create the `cron_logs` table:

```bash
# Using mysql command line
mysql -u YOUR_USERNAME -p YOUR_DATABASE < database/cron-logs.sql

# Or import via phpMyAdmin
# Import the file: database/cron-logs.sql
```

### Step 2: Verify Installation

1. Login to your admin panel
2. Navigate to the sidebar menu
3. Look for "Cron Logs" under the User Management section
4. Click on "Cron Logs" - you should see an empty page (logs will appear after crons run)

That's it! The system is now ready to use.

## What Gets Logged Automatically

All existing cron jobs are now automatically logged:

✅ `/cron/order` - Order placement
✅ `/cron/status` - Order status updates  
✅ `/cron/status_subscriptions` - Subscription status
✅ `/cron/sync_services` - Service synchronization
✅ `/cron/completion_time` - Completion time calculation
✅ `/cron/email_marketing` - Email campaigns
✅ `/whatsapp_cron/run` - WhatsApp campaigns
✅ `/imap-auto-verify` - IMAP verification

## Testing the System

### Option 1: Wait for Scheduled Crons
Your existing crontab entries will automatically log on their next execution.

### Option 2: Manual Test
Access any cron URL directly in your browser:

```
https://yourdomain.com/cron/completion_time
https://yourdomain.com/cron/order
```

For token-protected crons (email, whatsapp), include the token:
```
https://yourdomain.com/cron/email_marketing?token=YOUR_TOKEN
```

### Option 3: Command Line Test
```bash
curl "https://yourdomain.com/cron/completion_time"
```

## Viewing Logs

1. Go to Admin Panel → Cron Logs
2. You'll see:
   - **Last Run Summary**: Overview of all crons with their last execution
   - **Detailed Logs**: Full execution history with filters

## Using Filters

Filter logs by:
- **Cron Name**: Select specific cron from dropdown
- **Status**: Success or Failed
- **Date Range**: From date and To date

Click "Filter" to apply, "Reset" to clear filters.

## Common Tasks

### View Last Execution Status
Check the "Last Run Summary" section at the top of the Cron Logs page.

### Find Failed Executions
1. Set Status filter to "Failed"
2. Click "Filter"
3. Review the error messages in the Response Message column

### Cleanup Old Logs
1. Click "Action" dropdown
2. Select "Cleanup Old Logs (30+ days)"
3. This removes logs older than 30 days

### Delete All Logs
1. Click "Action" dropdown
2. Select "Clear All"
3. Confirm - all logs will be deleted

## Performance Impact

The logging system is designed to be lightweight:
- Adds ~10-50ms overhead per cron execution
- Uses efficient database indexes
- Failed database writes won't break cron execution
- Automatic cleanup available to maintain performance

## Troubleshooting

### Logs Not Appearing

**Check 1**: Verify table exists
```sql
SHOW TABLES LIKE 'cron_logs';
```

**Check 2**: Run a test cron
```bash
curl -v "https://yourdomain.com/cron/completion_time"
```

**Check 3**: Check PHP error log
```bash
tail -f /var/log/php-error.log
```

### Permission Errors

Ensure web server user has permissions:
```bash
# Check database permissions
mysql -u root -p -e "SHOW GRANTS FOR 'your_db_user'@'localhost';"
```

### Menu Item Not Showing

1. Clear browser cache
2. Re-login to admin panel
3. Check if you're logged in as admin (not supporter/user)

## Adding Logging to New Crons

When you create a new cron job, simply add these lines:

```php
class My_new_cron extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->library('cron_logger');  // Add this
    }
    
    public function run(){
        // Start logging
        $log_id = $this->cron_logger->start('cron/my_new_cron');
        
        try {
            // Your cron logic here
            
            // Log success
            $this->cron_logger->end($log_id, 'Success', 200, 'Done');
        } catch (Exception $e) {
            // Log failure
            $this->cron_logger->end($log_id, 'Failed', 500, $e->getMessage());
        }
    }
}
```

## Files Modified/Created

### New Files
- `database/cron-logs.sql` - Database table
- `app/libraries/Cron_logger.php` - Logging library
- `app/modules/cron_logs/controllers/Cron_logs.php` - Controller
- `app/modules/cron_logs/models/Cron_logs_model.php` - Model
- `app/modules/cron_logs/views/index.php` - Main view
- `app/modules/cron_logs/views/ajax_search.php` - Search view
- `database/CRON-LOGS-README.md` - Full documentation

### Modified Files
- `app/controllers/order_completion_cron.php` - Added logging
- `app/controllers/Email_cron.php` - Added logging
- `app/controllers/whatsapp_cron.php` - Added logging
- `app/controllers/Imap_cron.php` - Added logging
- `app/modules/api_provider/controllers/api_provider.php` - Added logging
- `app/modules/blocks/views/header.php` - Added menu item
- `app/language/english/common_lang.php` - Added translations
- `app/config/constants.php` - Added CRON_LOGS constant

## Next Steps

1. **Monitor Initial Runs**: Watch the first few cron executions to ensure logging works
2. **Set Up Cleanup**: Schedule automatic cleanup of old logs
3. **Review Failed Crons**: Check the logs for any failed executions
4. **Optimize Frequency**: Adjust crontab schedules based on execution data

## Support

For detailed documentation, see: `database/CRON-LOGS-README.md`

For issues:
1. Check application logs: `app/logs/`
2. Enable CodeIgniter debug mode for detailed errors
3. Review execution logs in the admin panel

---

**System Version**: 1.0  
**Installation Date**: 2025-11-18
