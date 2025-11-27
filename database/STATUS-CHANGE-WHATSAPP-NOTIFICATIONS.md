# Status Change WhatsApp Notifications - Summary

## Overview

The SMM Panel system sends WhatsApp notifications to users when their order status changes. This document provides a detailed summary of how these notifications work, when they are triggered, and how they can be managed.

## When Status Change Notifications Are Sent

Status change WhatsApp notifications are triggered in **two scenarios**:

### 1. Automatic (via Cron Job)

**Location:** `/app/modules/api_provider/controllers/api_provider.php`  
**Methods:** `cron_status_subscriptions()` and `cron_status_orders()`

When the cron job runs to check order statuses from API providers, it automatically sends WhatsApp notifications when:

- The order status changes to **Completed**
- The order status changes to **Partial**
- The order status changes to **Canceled/Cancelled** (or Refunded)

**Flow:**
1. Cron job fetches order status from external API provider
2. If status changed, it updates the order in the database
3. Before sending notification, it checks if notifications are enabled for that status
4. If enabled, it sends the notification to the user's WhatsApp number

### 2. Manual (Admin Panel)

**Location:** `/app/modules/order/controllers/order.php`  
**Method:** Around line 951-980

When an admin manually changes an order status through the admin panel, WhatsApp notifications are sent for:

- **Canceled** or **Refunded** orders → Uses `order_cancelled` template
- **Partial** orders → Uses `order_partial` template

## Notification Types for Status Changes

| Status | Event Type | Recipient | Template Variables |
|--------|------------|-----------|-------------------|
| Completed | `order_completed` | User | order_id, service_name, quantity, link, charge, username, delivered_quantity |
| Partial | `order_partial` | User | order_id, service_name, delivered_quantity, ordered_quantity, refund_amount, new_balance |
| Canceled | `order_cancelled` | User | order_id, refund_amount, service_name, new_balance |
| Refunded | `order_refunded` | User | order_id, refund_amount, service_name, new_balance |

## How the System Works (Technical Flow)

### 1. Core Library

**File:** `/app/libraries/Whatsapp_notification.php`

The `Whatsapp_notification` library provides:

```php
// Main method for status change notifications
public function send_order_status_notification($order, $old_status, $new_status, $phone = null)

// Helper methods
public function send_order_completed_notification($order, $old_status, $phone = null)
public function send_order_partial_notification($order, $old_status, $phone = null)
public function send_order_cancelled_notification($order, $old_status, $phone = null)
public function send_order_refunded_notification($order, $old_status, $phone = null)

// Check if notification is enabled
public function is_status_notification_enabled($status)
```

### 2. Status Mapping

The library maps order statuses to event types:

```php
$status_map = array(
    'completed' => 'order_completed',
    'partial'   => 'order_partial',
    'canceled'  => 'order_cancelled',  // American spelling (from API)
    'cancelled' => 'order_cancelled',  // British spelling (both accepted)
    'refunded'  => 'order_refunded',
);
```

> **Note:** The system accepts both "canceled" (American spelling from external APIs) and "cancelled" (British spelling). Both are mapped to the same `order_cancelled` event type.

### 3. Notification Process

1. **Status Check**: Only sends notification if status actually changed
2. **Event Type Lookup**: Gets the event type from status mapping
3. **User Phone Lookup**: Gets user's WhatsApp number from database
4. **Template Processing**: Replaces variables in the template with actual values
5. **API Call**: Sends message via WhatsApp API

### 4. Variables Available in Templates

| Variable | Description |
|----------|-------------|
| `{order_id}` | Order ID number |
| `{service_name}` | Name of the service ordered |
| `{quantity}` | Original quantity ordered |
| `{link}` | Order link/URL |
| `{charge}` | Amount charged |
| `{old_status}` | Previous order status |
| `{new_status}` | New order status |
| `{username}` | User's name |
| `{remains}` | Remaining quantity |
| `{delivered_quantity}` | Quantity actually delivered |
| `{ordered_quantity}` | Original quantity ordered |
| `{refund_amount}` | Amount refunded to user |
| `{new_balance}` | User's balance after refund |
| `{website_name}` | Panel name |
| `{currency_symbol}` | Currency symbol |

## Enabling/Disabling Notifications

### Admin Interface

Navigate to: **Admin > Settings > WhatsApp Notifications**

From here you can:
- Enable/disable each notification type individually
- Edit message templates
- View available variables for each template

### Database Table

**Table:** `whatsapp_notifications`

| Field | Description |
|-------|-------------|
| `event_type` | Unique identifier (e.g., 'order_completed') |
| `event_name` | Display name |
| `status` | 1 = enabled, 0 = disabled |
| `template` | Message template with variables |
| `description` | Description of when notification is sent |
| `variables` | JSON array of available variables |

### Programmatic Check

```php
$this->load->library('whatsapp_notification');
if ($this->whatsapp_notification->is_status_notification_enabled('completed')) {
    // Send notification
}
```

## Prerequisites for Notifications to Work

1. **WhatsApp API Configured**: API URL, API Key, and Admin Phone must be set in `whatsapp_config` table
2. **Notification Enabled**: The specific notification type must have `status = 1` in database
3. **User Phone Number**: User must have a WhatsApp number in their profile (`users.whatsapp_number`)

## Default Message Templates

### Order Completed
```
*✅ Order Completed*

🔢 *Order ID:* #{order_id}
📦 *Service:* {service_name}
✅ *Delivered:* {delivered_quantity}

Your order has been completed successfully!

Thank you,
{website_name}
```

### Order Partial
```
*📊 Order Partially Completed*

🔢 *Order ID:* #{order_id}
📦 *Service:* {service_name}
✅ *Delivered:* {delivered_quantity}
📋 *Ordered:* {ordered_quantity}
💰 *Refund:* {currency_symbol}{refund_amount}

Your order has been partially completed. The remaining amount has been refunded to your account.

💵 *New Balance:* {currency_symbol}{new_balance}

Thank you,
{website_name}
```

### Order Cancelled
```
*⚠️ Order Cancelled*

🔢 *Order ID:* #{order_id}
💰 *Refund Amount:* {currency_symbol}{refund_amount}
📦 *Service:* {service_name}

Your order has been cancelled and the amount has been refunded to your account.

💵 *New Balance:* {currency_symbol}{new_balance}

If you have any questions, please contact our support team.

Thank you,
{website_name}
```

## Logging

All notification attempts are logged:
- **Success**: "WhatsApp Notification: Sent to [phone] - Response: [response]"
- **Failure**: "WhatsApp Notification: [error message]"

Status change notifications include detailed logging:
```
WhatsApp Notification: Order #[id] status changed from [old] to [new], event: [type], sent: YES/NO
```

## Cron Job Setup

Add to your cron schedule to enable automatic status checking and notifications:

```bash
# Check order status every 5 minutes
*/5 * * * * wget -q -O - https://yourdomain.com/cron/status >/dev/null 2>&1
```

The cron outputs helpful information:
```
Order ID: 12345
Checking Order ID: 12345
12345 → API Status: Completed
12345 → WhatsApp notification sent for status: completed
```

## Troubleshooting

### Notifications Not Sending

1. **Check API Configuration**: Verify `whatsapp_config` table has valid URL and API key
2. **Check Notification Status**: Ensure `whatsapp_notifications.status = 1` for the event type
3. **Check User Phone**: Verify user has a WhatsApp number in their profile
4. **Check Logs**: Review `/app/logs/log-*.php` for error messages

### Common Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| "WhatsApp API not configured" | Missing API settings | Configure API in admin panel |
| "Notification template not found" | Missing event type | Run database migration |
| "Notification disabled" | Status = 0 | Enable in admin panel |
| "No phone number provided" | Missing user phone | User needs to add WhatsApp number |
| "Status unchanged" | Same old/new status | Normal - no notification needed |

---

**Version:** 1.0  
**Last Updated:** 2025-11-27  
**Related Files:**
- `/app/libraries/Whatsapp_notification.php`
- `/app/modules/api_provider/controllers/api_provider.php`
- `/app/modules/order/controllers/order.php`
- `/database/whatsapp-notifications.sql`
