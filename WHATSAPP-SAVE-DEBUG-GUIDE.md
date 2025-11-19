# WhatsApp Notification Save - Debugging Guide

## Log File Location
All save operations are logged to: `/app/logs/whatsapp_notification_save.log`

## What Gets Logged

Each save attempt creates a detailed log entry including:

1. **Request Information**
   - Timestamp
   - HTTP Method (should be POST)
   - Request URL

2. **Received Data**
   - Event Type (e.g., welcome_message, order_placed)
   - Status (1 for enabled, 0 for disabled)
   - Template content length

3. **Database Operation**
   - Update data being sent
   - Affected rows count
   - Any database errors

4. **Result**
   - SUCCESS or ERROR status
   - Error messages if any
   - Exception details with stack trace

## How to View Logs

### Via SSH/Terminal
```bash
tail -f /path/to/app/logs/whatsapp_notification_save.log
```

### Via FTP/File Manager
1. Navigate to `app/logs/`
2. Download `whatsapp_notification_save.log`
3. Open in text editor

## Browser Console Logging

Client-side logging is also enabled. Open browser Developer Tools (F12) and check the Console tab for:

- Page load confirmation
- Toggle switch changes
- Form submission details:
  - Action URL
  - Form data being sent
  - Event type
  - Status (checked/unchecked)
  - Template length

## Common Issues & Solutions

### Issue: "Invalid method" error
**Cause:** Request is not POST  
**Solution:** Check if form has `method="POST"` attribute

### Issue: "Event type is required" error
**Cause:** Hidden input for event_type is missing or empty  
**Solution:** Verify each form has: `<input type="hidden" name="event_type" value="...">`

### Issue: Template not saving
**Cause:** Various - check logs for specific error  
**Solution:** 
1. Check `whatsapp_notification_save.log` for database errors
2. Verify `whatsapp_notifications` table exists
3. Check database permissions

### Issue: Status always saves as 0
**Cause:** Checkbox value not being captured  
**Solution:** Each form should have:
```html
<input type="hidden" name="status" value="0">
<input type="checkbox" name="status" value="1" ...>
```

### Issue: No logs created
**Cause:** Write permissions issue  
**Solution:** 
```bash
chmod 755 app/logs/
chmod 644 app/logs/whatsapp_notification_save.log
```

## Testing Steps

1. **Enable Browser Console**
   - Press F12
   - Go to Console tab

2. **Edit a Template**
   - Change any notification template
   - Toggle the status switch if needed

3. **Click Save**
   - Watch for console logs showing form data
   - Check for success/error message

4. **Check Server Logs**
   - View `app/logs/whatsapp_notification_save.log`
   - Look for the latest entry with your timestamp
   - Check for any ERROR messages

## Sample Log Entry

```
================================================================================
[2025-11-19 17:00:00] WhatsApp Notification Save Request
Method: post
URL: /setting/ajax_save_notification_template
Received Data:
  event_type: 'welcome_message'
  status: '1'
  template length: 245 chars
Update Data:
  status: 1
  template: *Welcome to {website_name}!* 👋

Hello *{username}*,

Thank you for joining us!...
Database Update Result:
  affected_rows: 1
SUCCESS: Notification updated successfully
```

## Need More Help?

If the logs don't reveal the issue:
1. Check PHP error logs: `/var/log/php_errors.log` (location may vary)
2. Check web server error logs: `/var/log/nginx/error.log` or `/var/log/apache2/error.log`
3. Enable CodeIgniter debugging: Set `$config['log_threshold'] = 4;` in `application/config/config.php`
