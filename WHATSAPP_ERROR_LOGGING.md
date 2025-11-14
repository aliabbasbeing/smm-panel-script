# WhatsApp Marketing Error Logging System

## Overview
Comprehensive error logging has been implemented for the WhatsApp Marketing module to help diagnose issues.

## Features

### 1. Automatic Error Logging
All errors in the WhatsApp Marketing module are automatically logged to:
```
app/logs/whatsapp_marketing_errors.log
```

### 2. Error Information Captured
Each log entry includes:
- Timestamp
- Error context (which method/operation failed)
- Error message
- Stack trace (for exceptions)
- Additional data (parameters, IDs, etc.)
- Request URL and method
- User IP address
- Database error details (if applicable)
- Last executed SQL query

### 3. Debug Tools
Access these URLs to view diagnostics (admin only):

#### View Error Log
```
https://your-domain.com/whatsapp_marketing_debug/error_log
```
Shows all logged errors in plain text format.

#### Clear Error Log
```
https://your-domain.com/whatsapp_marketing_debug/clear_log
```
Clears the error log file.

#### System Check
```
https://your-domain.com/whatsapp_marketing_debug/phpinfo_check
```
Shows:
- PHP configuration
- Database table status
- Row counts for each table

## Usage

### To Diagnose Blank Page Issues:

1. **Access the page that's showing blank**
   - Example: `https://your-domain.com/whatsapp_marketing/campaign_details/YOUR_CAMPAIGN_ID`

2. **View the error log**
   - Go to: `https://your-domain.com/whatsapp_marketing_debug/error_log`
   - Look for recent errors with timestamps matching when you accessed the page

3. **Check system status**
   - Go to: `https://your-domain.com/whatsapp_marketing_debug/phpinfo_check`
   - Verify all database tables exist and have data

4. **Common Issues to Look For:**
   - "Campaign not found" - The campaign ID doesn't exist in database
   - "Table doesn't exist" - Database schema not imported
   - Database connection errors
   - Missing template or API configuration data

### Error Log Format Example:
```
================================================================================
[2024-11-14 12:45:23] WhatsApp Marketing Error
Context: Campaign Details Access
Error: Campaign not found
Additional Data: Array
(
    [ids] => 93f7bc92183ee7310b4c9233ceb8d7ec
)
URL: /whatsapp_marketing/campaign_details/93f7bc92183ee7310b4c9233ceb8d7ec
Method: GET
User IP: 192.168.1.100
================================================================================
```

## Troubleshooting

### If error log shows "Campaign not found":
1. Check if the campaign exists in `whatsapp_campaigns` table
2. Verify the `ids` column matches the ID in the URL
3. Check if related templates and API configs exist

### If error log shows database errors:
1. Verify `database/whatsapp-marketing.sql` has been imported
2. Check all 6 tables exist: campaigns, templates, api_configs, recipients, logs, settings
3. Verify database credentials in CodeIgniter config

### If no errors are logged:
1. Check file permissions on `app/logs/` directory (should be writable)
2. Verify PHP error reporting is enabled
3. Check if CodeIgniter's error logging is working

## Security Note
The debug URLs are restricted to admin users only. Regular users cannot access error logs or system information.

## Disabling Verbose Logging
Once issues are resolved, you can reduce logging by commenting out the detailed `_log_error` calls in the controller methods, keeping only exception logging.
