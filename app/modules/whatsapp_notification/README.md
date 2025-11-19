# WhatsApp Notification Module

## Overview
This module consolidates all WhatsApp API configuration and notification template management into a single, dedicated interface. It provides a modern UI for managing WhatsApp notifications in the SMM Panel.

## Features

### 1. API Configuration
- **WhatsApp API URL**: Configure the endpoint for your WhatsApp API service
- **API Key**: Set your WhatsApp API authentication key
- **Admin Phone**: Configure the admin phone number for receiving notifications

### 2. Notification Templates
- **Template Management**: Edit message templates for different notification types
- **Enable/Disable Controls**: Toggle individual notifications on/off
- **Variable Support**: Use dynamic variables in templates
- **Visual Feedback**: Modern UI with color-coded status indicators

## Navigation
Access the module from the main navigation:
**Settings > WhatsApp Notification**

## Module Structure
```
app/modules/whatsapp_notification/
├── config.php                          # Module configuration
├── controllers/
│   └── Whatsapp_notification.php      # Main controller
├── models/
│   └── Whatsapp_notification_model.php # Database operations
└── views/
    ├── index.php                       # Main layout
    ├── sidebar.php                     # Navigation sidebar
    ├── api_settings.php                # API configuration page
    └── notification_templates.php      # Template management page
```

## Database Tables Used
- **whatsapp_config**: Stores API configuration (url, api_key, admin_phone)
- **whatsapp_notifications**: Stores notification templates and their status

## Usage

### Configuring WhatsApp API
1. Navigate to **WhatsApp Notification > API Configuration**
2. Enter your WhatsApp API URL
3. Enter your API Key
4. Enter the admin phone number (with country code)
5. Click "Save API Settings"

### Managing Notification Templates
1. Navigate to **WhatsApp Notification > Notification Templates**
2. Toggle notifications on/off using the switches
3. Edit message templates as needed
4. Use available variables (shown for each template)
5. Click "Save All Notification Settings"

## Available Notification Types
1. **Order Placed** - Admin notification when new order is created
2. **Welcome Message** - User notification on registration
3. **Order Cancelled** - User notification when order is cancelled
4. **Order Partial** - User notification when order is partially completed
5. **API Key Changed** - User notification when API key is regenerated
6. **Support Ticket** - Admin notification when new ticket is created
7. **Reset Password** - User notification for password reset
8. **Verification OTP** - User notification for OTP verification

## Template Variables
Templates support dynamic variables in the format `{variable_name}`. Available variables depend on the notification type and include:
- `{website_name}` - Site name
- `{currency_symbol}` - Currency symbol
- `{username}` - User's name
- `{email}` - User's email
- `{order_id}` - Order ID
- And more...

## UI Features
- **Modern Design**: Clean, professional interface with WhatsApp green theme (#25D366)
- **Responsive Layout**: Works on desktop and mobile devices
- **Interactive Elements**: Hover effects, smooth transitions
- **Copy-to-Clipboard**: Click variables to copy them instantly
- **Real-time Feedback**: Visual updates when toggling switches

## Security
- Admin-only access control
- Input validation for phone numbers and API credentials
- XSS protection on all inputs
- Secure storage in database

## Integration
The module integrates seamlessly with the existing WhatsApp notification system:
- Uses the `Whatsapp_notification` library for sending messages
- Reads from `whatsapp_config` table for API settings
- Manages `whatsapp_notifications` table for templates

## Changelog
### Version 1.0 (2025-11-19)
- Initial release
- Consolidated WhatsApp settings from Settings module
- Modernized UI with card-based layout
- Added sidebar navigation for easy access
- Improved user experience with visual indicators
