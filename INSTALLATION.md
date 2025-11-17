# Quick Installation Guide - Cron Logging System

## Option 1: Automated Installation (Recommended)

1. **Access the Installation UI**
   - Navigate to: `http://yoursite.com/install/install_cron_logs_ui.php`
   - Click "Install Database Table"
   - Click "Verify Installation" to confirm

2. **Access Dashboard**
   - Go to: `http://yoursite.com/cron_logs/dashboard`
   - View all logs: `http://yoursite.com/cron_logs`

## Option 2: Manual SQL Installation

1. **Run SQL Script**
   ```bash
   mysql -u your_username -p your_database < database/cron-logs.sql
   ```

2. **Or execute directly in phpMyAdmin**
   - Open phpMyAdmin
   - Select your database
   - Go to SQL tab
   - Copy and paste the contents of `database/cron-logs.sql`
   - Click "Go"

## Verification

To verify the installation is working:

1. **Trigger a test cron**:
   ```bash
   # Example: Trigger the order cron
   wget -O - http://yoursite.com/cron/order
   ```

2. **Check the logs**:
   - Go to `http://yoursite.com/cron_logs`
   - You should see the execution logged

## What's Installed?

✅ Database table: `cron_logs`
✅ Library: `app/libraries/Cron_logger.php`
✅ Module: `app/modules/cron_logs/`
✅ Routes configured in `app/config/routes.php`
✅ All existing cron endpoints now auto-log

## Automatically Logged Crons

The following crons are now automatically logged:

- `/cron/order` - Order processing
- `/cron/status` - Status updates
- `/cron/status_subscriptions` - Subscription status
- `/cron/sync_services` - Service synchronization
- `/cron/completion_time` - Order completion calculation
- `/cron/email_marketing` - Email marketing
- `/cron/childpanel` - Child panel renewal
- `/cron/check_panel_status` - Panel status check
- `/whatsapp_cron/run` - WhatsApp marketing
- `/currencies/cron_fetch_rates` - Currency rate updates
- `/imap-auto-verify` - IMAP auto verification

## Next Steps

1. **Add to Admin Menu** (optional):
   Add a menu item in your admin navigation:
   ```php
   <li>
       <a href="<?=cn('cron_logs/dashboard')?>">
           <i class="fe fe-clock"></i> Cron Logs
       </a>
   </li>
   ```

2. **Set Up Notifications** (optional):
   Enable failure notifications in your settings:
   ```php
   set_option('cron_failure_notifications', 1);
   set_option('admin_email', 'admin@yoursite.com');
   ```

3. **Schedule Log Cleanup** (recommended):
   Add a monthly cron to clean old logs:
   ```bash
   0 0 1 * * php /path/to/cleanup_logs.php
   ```

## Troubleshooting

**Issue**: Logs not appearing
- Check database connection
- Verify table exists: `SHOW TABLES LIKE 'cron_logs'`
- Check file permissions

**Issue**: Installation fails
- Ensure database user has CREATE TABLE permissions
- Check MySQL version compatibility
- Review error logs

## Support

For detailed documentation, see: `database/CRON-LOGGING-README.md`

## Uninstallation

To remove the cron logging system:

1. Drop the table:
   ```sql
   DROP TABLE IF EXISTS `cron_logs`;
   ```

2. Remove files:
   - `app/libraries/Cron_logger.php`
   - `app/modules/cron_logs/` (directory)
   - `app/core/Base_cron.php`

3. Revert controller changes (remove `$this->load->library('cron_logger')` and logging calls)
