# WhatsApp Notification System - Documentation

## Overview

The WhatsApp Notification System provides a comprehensive, reusable notification framework for sending WhatsApp alerts for various events in the SMM Panel. This system allows administrators to enable/disable notifications individually and customize message templates.

## Features

- ✅ Individual ON/OFF toggles for each notification type
- ✅ Customizable message templates with variable placeholders
- ✅ Reusable notification library (`Whatsapp_notification`)
- ✅ Admin settings UI for easy management
- ✅ Database-driven configuration
- ✅ Consistent with existing "Order Placed" notification

## Installation

### 1. Database Setup

Run the SQL migration file to create the necessary table and insert default templates:

```sql
/database/whatsapp-notifications.sql
```

This will create the `whatsapp_notifications` table and populate it with default templates for all event types.

### 2. Library Setup

The WhatsApp notification library is located at:
```
/app/libraries/Whatsapp_notification.php
```

It's automatically available for use in all controllers.

### 3. Admin UI

Access the admin settings page at:
```
Admin > Settings > WhatsApp Notifications
```

## Event Types

The system supports the following notification types:

### 1. Order Placed ✅ (Already Working)
- **Event Type:** `order_placed`
- **Sent To:** Admin
- **Trigger:** When a new order is placed
- **Variables:**
  - `{order_id}` - Order ID
  - `{total_charge}` - Total charge amount
  - `{quantity}` - Order quantity
  - `{link}` - Order link/URL
  - `{user_email}` - User's email
  - `{currency_symbol}` - Currency symbol
  - `{website_name}` - Website name

### 2. Welcome Message
- **Event Type:** `welcome_message`
- **Sent To:** New user
- **Trigger:** After successful registration
- **Variables:**
  - `{username}` - User's name
  - `{email}` - User's email
  - `{balance}` - User's balance
  - `{currency_symbol}` - Currency symbol
  - `{website_name}` - Website name

### 3. Order Cancelled
- **Event Type:** `order_cancelled`
- **Sent To:** User
- **Trigger:** When an order is cancelled
- **Variables:**
  - `{order_id}` - Order ID
  - `{refund_amount}` - Refunded amount
  - `{service_name}` - Service name
  - `{new_balance}` - Updated balance after refund
  - `{currency_symbol}` - Currency symbol
  - `{website_name}` - Website name

### 4. Order Partial
- **Event Type:** `order_partial`
- **Sent To:** User
- **Trigger:** When an order is partially completed
- **Variables:**
  - `{order_id}` - Order ID
  - `{service_name}` - Service name
  - `{delivered_quantity}` - Quantity delivered
  - `{ordered_quantity}` - Quantity ordered
  - `{refund_amount}` - Partial refund amount
  - `{new_balance}` - Updated balance
  - `{currency_symbol}` - Currency symbol
  - `{website_name}` - Website name

### 5. API Key Changed
- **Event Type:** `api_key_changed`
- **Sent To:** User
- **Trigger:** When user's API key is changed
- **Variables:**
  - `{username}` - User's name
  - `{api_key}` - New API key
  - `{changed_at}` - Timestamp of change
  - `{ip_address}` - IP address of requester
  - `{website_name}` - Website name

### 6. Support Ticket
- **Event Type:** `support_ticket`
- **Sent To:** Admin
- **Trigger:** When a new support ticket is created
- **Variables:**
  - `{ticket_id}` - Ticket ID
  - `{user_email}` - User's email
  - `{subject}` - Ticket subject
  - `{message}` - Ticket message
  - `{website_name}` - Website name

### 7. Verification OTP
- **Event Type:** `verification_otp`
- **Sent To:** User
- **Trigger:** When OTP is sent for verification
- **Variables:**
  - `{username}` - User's name
  - `{otp_code}` - OTP code
  - `{expiry_minutes}` - Expiry time in minutes
  - `{website_name}` - Website name

### 8. Reset Password
- **Event Type:** `reset_password`
- **Sent To:** User
- **Trigger:** When password reset is requested
- **Variables:**
  - `{username}` - User's name
  - `{reset_link}` - Password reset link
  - `{expiry_minutes}` - Link expiry time in minutes
  - `{website_name}` - Website name

### 9. Order Error (Admin Notification)
- **Event Type:** `order_error`
- **Sent To:** Admin
- **Trigger:** When an order encounters an error during processing (via cron)
- **Variables:**
  - `{order_id}` - Order ID
  - `{service_name}` - Service name
  - `{username}` - User's name
  - `{user_email}` - User's email
  - `{error_message}` - The error message from API
  - `{link}` - Order link/URL
  - `{quantity}` - Order quantity
  - `{charge}` - Order charge amount
  - `{error_time}` - Timestamp of the error
  - `{currency_symbol}` - Currency symbol
  - `{website_name}` - Website name

## Cron-based Notification Queue

To ensure that order processing is not delayed by slow or unavailable WhatsApp API, error notifications are queued and sent via a separate cron job.

### Queue Table

The `whatsapp_notification_queue` table stores pending notifications:
- `pending` - Waiting to be sent
- `sent` - Successfully sent
- `failed` - Failed after 3 attempts
- `skipped` - Skipped (notification disabled or not configured)

### Cron Jobs

1. **Process Queue**: `/cron/whatsapp_notifications`
   - Processes pending notifications in the queue
   - Uses short timeouts to prevent delays
   - Retries failed notifications up to 3 times

2. **Cleanup Queue**: `/cron/whatsapp_notifications_cleanup`
   - Removes old processed entries (default: 7 days)
   - Run periodically to prevent database growth

### Add to Crontab

```bash
# Process WhatsApp notification queue (every minute or as needed)
* * * * * wget --spider -o - https://domain.com/cron/whatsapp_notifications >/dev/null 2>&1

# Cleanup old queue entries (daily)
0 0 * * * wget --spider -o - https://domain.com/cron/whatsapp_notifications_cleanup >/dev/null 2>&1
```

## Usage Guide

### For Developers

#### Sending a Notification

```php
// Load the library
$this->load->library('whatsapp_notification');

// Prepare variables
$variables = array(
    'username' => $user->username,
    'email' => $user->email,
    'balance' => '0.00'
);

// Send notification
$result = $this->whatsapp_notification->send(
    'welcome_message',        // Event type
    $variables,               // Variables array
    $user->whatsapp_number    // Phone number (optional, defaults to admin)
);

// Check result
if ($result === true) {
    log_message('info', 'Welcome notification sent successfully');
} else {
    log_message('error', 'Failed to send welcome notification: ' . $result);
}
```

#### Example: Welcome Message Implementation

In the auth controller after successful registration:

```php
// After user registration
if ($user_created) {
    // Load library
    $this->load->library('whatsapp_notification');
    
    // Send welcome notification
    $this->whatsapp_notification->send('welcome_message', array(
        'username' => $username,
        'email' => $email,
        'balance' => '0.00'
    ), $whatsapp_number);
}
```

#### Example: Order Cancelled Implementation

In the order controller when cancelling an order:

```php
// After order cancellation
$this->load->library('whatsapp_notification');

$this->whatsapp_notification->send('order_cancelled', array(
    'order_id' => $order_id,
    'refund_amount' => $refund_amount,
    'service_name' => $service_name,
    'new_balance' => $new_balance
), $user->whatsapp_number);
```

### For Administrators

#### Managing Notifications

1. Navigate to **Admin > Settings > WhatsApp Notifications**
2. Each notification can be:
   - **Enabled/Disabled** using the toggle switch
   - **Customized** by editing the message template
3. Use the provided variables in your templates (shown below each template)
4. Click **Save Notification Settings** to apply changes

#### Template Customization

Templates support markdown-style formatting:
- `*Bold Text*` - Bold
- `_Italic Text_` - Italic
- Emojis can be used directly: 👋 🔔 ✅ ⚠️

Example template:
```
*Welcome to {website_name}!* 👋

Hello *{username}*,

Thank you for joining us!

📧 *Email:* {email}
💰 *Balance:* {currency_symbol}{balance}

Best regards,
{website_name} Team
```

## Integration Points

### Controllers to Update

1. **auth/controllers/auth.php** - Welcome message, OTP, Reset password
2. **order/controllers/order.php** - Order cancelled, Order partial
3. **tickets/controllers/tickets.php** - Support ticket
4. **api_access/controllers/api_access.php** - API key changed

### Database Tables

- `whatsapp_config` - WhatsApp API configuration
- `whatsapp_notifications` - Notification templates and settings
- `whatsapp_notification_queue` - Queue for cron-based notification sending

## API Configuration

Ensure WhatsApp API is configured at:
**Admin > Settings > [WhatsApp Config Section]**

Required fields:
- API URL
- API Key
- Admin Phone Number

## Troubleshooting

### Notifications Not Sending

1. Check if WhatsApp API is configured (`whatsapp_config` table)
2. Check if notification is enabled in settings
3. Check error logs: `/app/logs/`
4. Verify phone number format (should not include + prefix)

### Template Variables Not Replacing

1. Ensure variable names match exactly (case-sensitive)
2. Variables must be enclosed in curly braces: `{variable_name}`
3. Check that variables are passed in the send() method

### Database Table Missing

Run the migration file:
```
/database/whatsapp-notifications.sql
```

## Security Considerations

- API keys are stored securely in the database
- Phone numbers are validated before sending
- Templates are sanitized to prevent injection
- Logging includes IP addresses for audit trails

## Future Enhancements

Possible future features:
- Notification scheduling
- Multi-language support
- SMS fallback
- Notification history/logs
- Custom event types
- Bulk notification testing

## Support

For issues or questions:
1. Check the error logs
2. Review this documentation
3. Contact the development team

---

**Version:** 1.0  
**Last Updated:** 2025-11-19  
**Author:** SMM Panel Development Team
