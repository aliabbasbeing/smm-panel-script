# WhatsApp Marketing Module - Installation Guide

## Overview
The WhatsApp Marketing module provides comprehensive marketing campaign management through WhatsApp messaging.

## Installation

### Prerequisites
- SMM Panel Script installed and configured
- Database access (phpMyAdmin or MySQL CLI)
- Admin user account

### Database Installation

The WhatsApp Marketing module requires the following database tables:
- `whatsapp_campaigns` - Marketing campaign management
- `whatsapp_templates` - Reusable message templates
- `whatsapp_api_configs` - WhatsApp API configuration profiles
- `whatsapp_recipients` - Campaign recipient management
- `whatsapp_logs` - Activity and delivery logs
- `whatsapp_settings` - Module settings

#### Installation Method 1: Using phpMyAdmin

1. Login to your cPanel
2. Open phpMyAdmin
3. Select your database from the left sidebar
4. Click on the "Import" tab
5. Click "Choose File" and select `database/whatsapp-marketing.sql`
6. Click "Go" at the bottom of the page
7. Wait for the import to complete successfully

#### Installation Method 2: Using MySQL Command Line

```bash
mysql -u your_username -p your_database < database/whatsapp-marketing.sql
```

Replace:
- `your_username` with your MySQL username
- `your_database` with your database name

You will be prompted to enter your MySQL password.

### Verification

After importing the database schema:

1. Navigate to the WhatsApp Marketing module in your admin panel
2. If tables are correctly installed, you'll see the main dashboard
3. If tables are missing, you'll see an installation guide page

## Troubleshooting

### Blank Page on Linux cPanel

**Problem**: The WhatsApp Marketing page shows a blank page on Linux cPanel hosting.

**Cause**: This occurs when the required database tables are not installed. Unlike localhost environments, production servers may not display PHP errors by default.

**Solution**: 
1. Import the database schema using one of the methods above
2. Refresh the WhatsApp Marketing page
3. The module should now load correctly

### Permission Denied

**Problem**: "Permission Denied" message when accessing the module.

**Cause**: Only admin users can access the WhatsApp Marketing module.

**Solution**: Login with an admin account or contact your administrator.

### Tables Already Exist Error

**Problem**: Error when importing SQL file stating tables already exist.

**Cause**: The tables were previously imported.

**Solution**: 
- If the module is working, no action needed
- If experiencing issues, you may need to drop the existing tables and reimport
- **Warning**: Dropping tables will delete all existing campaign data

## Features

Once installed, the WhatsApp Marketing module provides:

- **Campaign Management**: Create and manage WhatsApp marketing campaigns
- **Template System**: Create reusable message templates
- **API Configuration**: Manage multiple WhatsApp API providers
- **Recipient Management**: Import and manage recipient lists
- **Sending Controls**: Set hourly and daily sending limits
- **Detailed Logging**: Track all message deliveries and failures
- **Campaign Reports**: View campaign performance metrics

## Support

For additional support:
1. Check the database import logs for any errors
2. Verify file permissions (should be 644 for files, 755 for directories)
3. Enable PHP error logging to diagnose specific issues
4. Contact your hosting provider for server-specific issues
