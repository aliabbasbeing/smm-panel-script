# Quick Start Guide - Cron Logs System

## Installation (3 Steps)

### Step 1: Run Database Migration
Execute the SQL file to create required tables:

**Option A: Using Command Line**
```bash
cd /path/to/smm-panel-script
./install_cron_logs.sh
```

**Option B: Using phpMyAdmin or MySQL Workbench**
1. Open `database/cron-logs.sql`
2. Execute the SQL in your database

**Option C: Using MySQL Command**
```bash
mysql -u your_user -p your_database < database/cron-logs.sql
```

### Step 2: Verify Installation
Access the admin panel:
```
https://yoursite.com/cron_logs
```

You should see the Cron Logs dashboard. If you get a 404 error, ensure you're logged in as admin.

### Step 3: Configure Settings (Optional)
1. Navigate to: `https://yoursite.com/cron_logs/settings`
2. Enable email notifications (optional)
3. Set notification email address
4. Configure log retention days (default: 30)

## Quick Test

### Test a Cron Job
Run any existing cron job, for example:
```bash
curl https://yoursite.com/cron/order
```

Then check the logs:
```
https://yoursite.com/cron_logs
```

You should see the execution logged with:
- Cron name: `cron/order`
- Status: Success or Failed
- Execution time
- Response details

## Features at a Glance

### View Logs
- Main page: `yoursite.com/cron_logs`
- Filter by cron name, status, date range
- Search by name or message
- Auto-refresh every 30 seconds

### Statistics
Each cron shows:
- Total runs
- Success rate
- Last execution time
- Average execution time

### Settings
- Email notifications for failures
- Log retention period
- Manual cleanup option

## Admin Menu
The "Cron Logs" menu item is located in the admin sidebar under "Settings" section.

## Existing Cron Jobs (Already Logged)

All these crons are automatically logged:

1. **Order Processing**: `/cron/order`
2. **Status Updates**: `/cron/status`
3. **Service Sync**: `/cron/sync_services`
4. **Currency Rates**: `/currencies/cron_fetch_rates`
5. **Email Marketing**: `/cron/email_marketing`
6. **WhatsApp Marketing**: `/whatsapp_cron/run`
7. **Child Panel Status**: `/cron/check_panel_status`
8. **Subscription Status**: `/cron/status_subscriptions`
9. **Refill Orders**: `/cron/refill`

## Troubleshooting

### Issue: "Cron Logs" menu not visible
**Solution**: Make sure you're logged in as admin. Only admin users can access cron logs.

### Issue: Logs not appearing
**Solution**: 
1. Check if database tables are created: Run `SHOW TABLES LIKE 'cron_%';` in your database
2. Run a cron job to generate a log
3. Refresh the page

### Issue: 404 Error
**Solution**:
1. Clear application cache
2. Verify the module files exist in `app/modules/cron_logs/`
3. Check file permissions

## Support

For detailed information, see: `CRON_LOGS_README.md`

---
**Version:** 1.0.0
