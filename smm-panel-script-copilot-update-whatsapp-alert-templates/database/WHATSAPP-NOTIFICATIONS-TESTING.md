# WhatsApp Notification System - Testing Guide

## Manual Testing Instructions

### Prerequisites
1. Ensure database migration has been run: `/database/whatsapp-notifications.sql`
2. Configure WhatsApp API settings in Admin > Settings > [WhatsApp Config]
   - API URL
   - API Key
   - Admin Phone Number

### Test Cases

#### 1. Test Admin Settings UI
**Steps:**
1. Login as admin
2. Navigate to: Admin > Settings > WhatsApp Notifications
3. Verify all 8 notification types are displayed
4. Toggle ON/OFF switches for different notifications
5. Edit a template and save
6. Refresh page and verify changes persisted

**Expected Results:**
- All notifications show with their templates
- Toggle switches work correctly
- Template changes are saved
- Available variables are shown for each template

---

#### 2. Test Welcome Message
**Steps:**
1. Ensure "Welcome Message" notification is ENABLED
2. Register a new user with a valid WhatsApp number (format: +92XXXXXXXXXX)
3. Check WhatsApp on the registered phone number

**Expected Results:**
- User receives welcome message via WhatsApp
- Message contains: username, email, balance
- Template variables are replaced correctly

**Test Template:**
```
*Welcome to {website_name}!* 👋

Hello *{username}*,

Thank you for joining us! Your account has been successfully created.

📧 *Email:* {email}
💰 *Balance:* {currency_symbol}{balance}
```

---

#### 3. Test Order Placed (Admin Notification)
**Steps:**
1. Ensure "Order Placed" notification is ENABLED
2. Login as a user
3. Place a new order
4. Check admin's WhatsApp number

**Expected Results:**
- Admin receives notification via WhatsApp
- Message contains: order_id, total_charge, quantity, link, user_email
- Variables are replaced with actual order data

---

#### 4. Test Order Cancelled
**Steps:**
1. Ensure "Order Cancelled" notification is ENABLED
2. Admin cancels an existing order
3. Check user's WhatsApp number

**Expected Results:**
- User receives cancellation notification
- Message shows refund amount and new balance
- User balance is updated correctly

---

#### 5. Test Order Partial
**Steps:**
1. Ensure "Order Partial" notification is ENABLED
2. Admin marks an order as partial with remaining quantity
3. Check user's WhatsApp number

**Expected Results:**
- User receives partial completion notification
- Message shows delivered vs ordered quantity
- Partial refund is calculated correctly
- User balance is updated

---

#### 6. Test API Key Changed
**Steps:**
1. Ensure "API Key Changed" notification is ENABLED
2. Login as a user with WhatsApp number
3. Go to Profile > API Access
4. Click "Regenerate API Key"
5. Check user's WhatsApp number

**Expected Results:**
- User receives API key change notification
- Message contains new API key
- Timestamp and IP address are included

---

#### 7. Test Support Ticket
**Steps:**
1. Ensure "Support Ticket" notification is ENABLED
2. Login as a user
3. Create a new support ticket
4. Check admin's WhatsApp number

**Expected Results:**
- Admin receives ticket notification
- Message contains: ticket_id, user_email, subject, message
- Variables are replaced correctly

---

#### 8. Test Reset Password
**Steps:**
1. Ensure "Reset Password" notification is ENABLED
2. Use "Forgot Password" feature with email of user who has WhatsApp number
3. Check user's WhatsApp number

**Expected Results:**
- User receives password reset notification
- Message contains clickable reset link
- Link expires after specified time (60 minutes)

---

## Testing the Library Directly

You can test the WhatsApp notification library in any controller:

```php
// Load the library
$this->load->library('whatsapp_notification');

// Test if configured
if ($this->whatsapp_notification->is_configured()) {
    echo "WhatsApp API is configured!";
} else {
    echo "WhatsApp API is NOT configured!";
}

// Send a test notification
$variables = array(
    'username' => 'Test User',
    'email' => 'test@example.com',
    'balance' => '100.00'
);

$result = $this->whatsapp_notification->send(
    'welcome_message',      // Event type
    $variables,             // Variables
    '923001234567'         // Phone number (without +)
);

if ($result === true) {
    echo "Notification sent successfully!";
} else {
    echo "Failed to send: " . $result;
}
```

---

## Troubleshooting

### Notifications Not Sending

**Check 1: Is WhatsApp API configured?**
```sql
SELECT * FROM whatsapp_config;
```
Ensure url, api_key, and admin_phone are set.

**Check 2: Are notifications enabled?**
```sql
SELECT event_type, status FROM whatsapp_notifications;
```
Status should be 1 for enabled notifications.

**Check 3: Check error logs**
```bash
tail -f /app/logs/log-*.php
```

**Check 4: Verify phone number format**
- Should be in format: +92XXXXXXXXXX (10 digits after +92)
- Library removes the + before sending

### Template Variables Not Replacing

**Issue:** Variables show as {variable_name} instead of actual values

**Solution:**
- Ensure variable names match exactly (case-sensitive)
- Check that variables are passed to send() method
- Verify variables array has correct keys

**Example:**
```php
// Wrong - variable won't be replaced
$variables = array('Username' => 'John');  // Wrong case

// Correct
$variables = array('username' => 'John');  // Correct case
```

### Database Table Not Found

**Error:** Table 'whatsapp_notifications' doesn't exist

**Solution:**
Run the migration file:
```bash
mysql -u username -p database_name < /database/whatsapp-notifications.sql
```

---

## Success Criteria

All tests pass when:
- ✅ Admin can view and manage all 8 notification types
- ✅ Templates can be edited and saved
- ✅ ON/OFF toggles work correctly
- ✅ All notification triggers fire correctly
- ✅ Template variables are replaced with actual data
- ✅ WhatsApp messages are received on target phone numbers
- ✅ Existing Order Placed notification still works
- ✅ No errors in application logs

---

## Performance Testing

### Load Testing
1. Create 10 orders rapidly
2. Verify all order placed notifications are sent
3. Check for delays or timeouts
4. Monitor error logs

### Concurrency Testing
1. Have multiple users register simultaneously
2. Verify all welcome messages are sent
3. Check for race conditions
4. Monitor queue/API limits

---

## Rollback Plan

If issues occur:

1. **Disable notifications temporarily:**
```sql
UPDATE whatsapp_notifications SET status = 0;
```

2. **Revert to previous version:**
```bash
git revert <commit-hash>
```

3. **Remove new tables (if needed):**
```sql
DROP TABLE IF EXISTS whatsapp_notifications;
```

---

Last Updated: 2025-11-19
