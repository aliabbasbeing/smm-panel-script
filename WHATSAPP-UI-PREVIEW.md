# WhatsApp Notifications - UI Overview

## Location
**Settings > WhatsApp Notifications** (under Integrations section in sidebar)

## Page Structure

### Header
```
┌──────────────────────────────────────────────────────────────┐
│ 🟢 WhatsApp Notification Settings                           │
│ (WhatsApp Green Background #25D366)                         │
└──────────────────────────────────────────────────────────────┘
```

---

## Section 1: WhatsApp API Configuration

```
┌──────────────────────────────────────────────────────────────┐
│ ⚙️ WhatsApp API Configuration                                │
├──────────────────────────────────────────────────────────────┤
│ ℹ️  Important: Configure your WhatsApp API credentials.     │
│    These settings are required for sending notifications.    │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│ 🔗 API URL *                                                 │
│ ┌──────────────────────────────────────────────────────────┐│
│ │ https://api.example.com/send                             ││
│ └──────────────────────────────────────────────────────────┘│
│ The endpoint URL for your WhatsApp API service              │
│                                                               │
│ 🔑 API Key *              📞 Admin Phone Number *           │
│ ┌─────────────────────┐  ┌──────────────────────────────┐  │
│ │ Your API key        │  │ +1234567890                  │  │
│ └─────────────────────┘  └──────────────────────────────┘  │
│ Your WhatsApp API key    Admin phone (with country code)   │
│                                                               │
│ [ 💾 Save API Configuration ]                               │
└──────────────────────────────────────────────────────────────┘
```

---

## Section 2: Notification Templates

```
┌──────────────────────────────────────────────────────────────┐
│ 🔔 Notification Templates                                    │
├──────────────────────────────────────────────────────────────┤
│ ℹ️  Manage Notifications: Enable or disable notifications   │
│    and customize message templates for different events.     │
└──────────────────────────────────────────────────────────────┘
```

### Example Notification Card (8 cards total):

```
┌──────────────────────────────────────────────────────────────┐
│ 🔔 Welcome Message               [●──] ✅ Enabled          │
│ ℹ️  Welcome message sent to new users after registration    │
├──────────────────────────────────────────────────────────────┤
│ 📝 Message Template:                                         │
│ ┌────────────────────────────────────────────────────────┐  │
│ │ *Welcome to {website_name}!* 👋                        │  │
│ │                                                         │  │
│ │ Hello *{username}*,                                    │  │
│ │                                                         │  │
│ │ Thank you for joining us! Your account has been        │  │
│ │ successfully created.                                   │  │
│ │                                                         │  │
│ │ 📧 *Email:* {email}                                    │  │
│ │ 💰 *Balance:* {currency_symbol}{balance}               │  │
│ │                                                         │  │
│ │ Start placing your orders now!                         │  │
│ │                                                         │  │
│ │ Best regards,                                          │  │
│ │ {website_name} Team                                    │  │
│ └────────────────────────────────────────────────────────┘  │
│                                                               │
│ 💻 Available Variables:                                      │
│ {username} {email} {balance} {currency_symbol}              │
│ {website_name}                                               │
│                                                               │
│ 💡 Use these variables in your template. They will be       │
│    replaced with actual values when sent.                    │
└──────────────────────────────────────────────────────────────┘
```

### All 8 Notification Cards:

1. **🔔 Order Placed** - Admin notification when new order created
2. **👋 Welcome Message** - User notification on registration
3. **❌ Order Cancelled** - User notification when order cancelled
4. **📊 Order Partial** - User notification when order partially completed
5. **🔑 API Key Changed** - User notification when API key regenerated
6. **🎫 Support Ticket** - Admin notification when new ticket created
7. **🔐 Reset Password** - User notification for password reset
8. **📱 Verification OTP** - User notification for OTP verification

### Footer

```
┌──────────────────────────────────────────────────────────────┐
│ [ 💾 Save All Notification Templates ]                      │
└──────────────────────────────────────────────────────────────┘
```

---

## Interactive Features

### Toggle Switch
- Click to enable/disable each notification
- Badge updates in real-time:
  - 🟢 **Enabled** (green)
  - ⚪ **Disabled** (gray)

### Variable Tags
- Green pills with variable names
- Click to copy to clipboard
- Shows "Copied!" feedback

### Hover Effects
- Cards elevate on hover
- Smooth transitions
- Visual feedback

---

## Color Scheme

- **Primary**: WhatsApp Green (#25D366)
- **Success Badge**: Green (#28A745)
- **Disabled Badge**: Gray (#6C757D)
- **Info Alert**: Blue (#17A2B8)
- **Card Background**: White with light gradient headers

---

## Responsive Design

- Full width on desktop
- Stacks on mobile
- Touch-friendly controls
- Clear visual hierarchy

---

This modern, consolidated interface keeps all WhatsApp settings in one place within the Settings module, making it easy to manage both API configuration and notification templates.
