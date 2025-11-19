# WhatsApp Notification System - Quick Installation Guide

## Installation Steps

### Step 1: Database Migration

Run the SQL migration file to create the notification table and insert default templates:

**Via phpMyAdmin:**
1. Open phpMyAdmin
2. Select your database
3. Click "Import" tab
4. Choose file: `/database/whatsapp-notifications.sql`
5. Click "Go"

**Via Command Line:**
```bash
mysql -u username -p database_name < /database/whatsapp-notifications.sql
```

**Via SQL tab in phpMyAdmin:**
```sql
-- Copy and paste the entire contents of whatsapp-notifications.sql
```

### Step 2: Verify Installation

Check that the table was created successfully:

```sql
-- Check table exists
SHOW TABLES LIKE 'whatsapp_notifications';

-- View all notification templates
SELECT event_type, event_name, status FROM whatsapp_notifications;
```

You should see 8 notification types:
- order_placed
- welcome_message
- order_cancelled
- order_partial
- api_key_changed
- support_ticket
- verification_otp
- reset_password

### Step 3: Configure WhatsApp API

1. Login as admin
2. Navigate to your WhatsApp API settings
3. Configure:
   - **API URL** - Your WhatsApp API endpoint
   - **API Key** - Your API authentication key
   - **Admin Phone** - Admin WhatsApp number (format: 923001234567 - no +)

### Step 4: Access Notification Settings

1. Login as admin
2. Navigate to: **Admin > Settings > WhatsApp Notifications**
3. You should see all 8 notification types with their templates

### Step 5: Configure Notifications

For each notification type:
1. **Enable/Disable** - Use the toggle switch
2. **Edit Template** - Customize the message template
3. **Review Variables** - See available placeholder variables
4. Click **Save Notification Settings**

### Step 6: Test

Follow the testing guide in:
```
/database/WHATSAPP-NOTIFICATIONS-TESTING.md
```

## Quick Test

Test if the library is working:

1. Register a new user with a WhatsApp number
2. Check if welcome message is received
3. Check error logs if no message received: `/app/logs/`

## Troubleshooting

### Issue: Notification settings page not showing

**Solution:** Clear browser cache and reload

### Issue: Table doesn't exist error

**Solution:** Re-run the migration SQL file

### Issue: Notifications not sending

**Check:**
1. WhatsApp API is configured (`whatsapp_config` table)
2. Notifications are enabled (`status = 1`)
3. Phone numbers are in correct format (no + prefix when stored)
4. Check error logs in `/app/logs/`

### Issue: Variables not replacing

**Solution:**
- Ensure variable names in template match exactly (case-sensitive)
- Use format: `{variable_name}` not `{{variable_name}}`

## Configuration Example

**Good template:**
```
Hello *{username}*,

Your order #{order_id} is confirmed!
Total: {currency_symbol}{total_charge}

Thank you,
{website_name}
```

**Bad template (won't work):**
```
Hello *{{username}}*,    <!-- Wrong: double braces -->

Your order #{Order_ID} is confirmed!  <!-- Wrong: case mismatch -->
Total: $total_charge    <!-- Wrong: no curly braces -->
```

## Default Settings

By default, all notifications are **ENABLED** except:
- All notifications are ON (status = 1)

You can disable any notification from the admin panel.

## File Permissions

Ensure the following files are readable by the web server:
- `/app/libraries/Whatsapp_notification.php`
- `/app/modules/setting/views/whatsapp_notifications.php`

Typical permissions:
```bash
chmod 644 /app/libraries/Whatsapp_notification.php
chmod 644 /app/modules/setting/views/whatsapp_notifications.php
```

## Support

For detailed documentation, see:
- `/database/WHATSAPP-NOTIFICATIONS-README.md` - Full documentation
- `/database/WHATSAPP-NOTIFICATIONS-TESTING.md` - Testing guide

## Rollback (if needed)

To remove the notification system:

```sql
-- Disable all notifications
UPDATE whatsapp_notifications SET status = 0;

-- Or remove the table completely
DROP TABLE IF EXISTS whatsapp_notifications;
```

Then restore previous code version using git.

---

**Installation Complete!** ✅

You can now manage WhatsApp notifications from:
**Admin > Settings > WhatsApp Notifications**

Enjoy your new notification system!
