# WhatsApp Notification Module - UI Screenshots

## Navigation
The WhatsApp Notification module appears in the main sidebar navigation under Settings:

```
Settings
├── System Settings
├── Services Providers
├── Payments
├── Payments Bonuses
└── WhatsApp Notification ← NEW
```

---

## Page 1: API Configuration

### Header
- **Title**: "WhatsApp API Configuration"
- **Background**: WhatsApp green (#25D366)
- **Icon**: Font Awesome WhatsApp icon

### Info Alert
Blue info box with message:
"Configure your WhatsApp API credentials here. These settings are required for sending WhatsApp notifications."

### Form Fields

**1. API URL** (Required)
- Icon: Link icon
- Placeholder: "https://api.example.com/send"
- Help text: "The endpoint URL for your WhatsApp API service"
- Input: Full width text field

**2. API Key** (Required)
- Icon: Key icon
- Placeholder: "Your API key"
- Help text: "Your WhatsApp API authentication key"
- Input: Half width text field

**3. Admin Phone Number** (Required)
- Icon: Phone icon
- Placeholder: "+1234567890"
- Help text: "Admin phone number to receive notifications (with country code)"
- Input: Half width text field

### Configuration Tips Card
Light gray card with:
- Heading: "Configuration Tips"
- Bullet points with helpful guidance
- Professional styling with borders

### Buttons
- Primary: "Save API Settings" (Blue, large)
- Secondary: "Configure Notifications" (Outlined, large)

---

## Page 2: Notification Templates

### Header
- **Title**: "WhatsApp Notification Templates"
- **Background**: WhatsApp green (#25D366)
- **Icon**: Bell icon

### Info Alert
Blue info box with message:
"Manage Notifications: Enable or disable notifications and customize message templates for different events."

### Notification Cards (8 total)

Each card includes:

**Card Header** (Gradient background)
- Left: Notification name with bell icon
- Right: Toggle switch with status badge
  - Green badge: "Enabled"
  - Gray badge: "Disabled"
- Description text below

**Card Body**
- **Template Editor**: Large textarea with monospace font
- **Variables Section**: 
  - Title: "Available Variables"
  - Variable tags: Green pills with {variable_name}
  - Click to copy functionality
  - Help text explaining usage

**Example Card: "Welcome Message"**
```
┌─────────────────────────────────────────────────┐
│ 🔔 Welcome Message          [●] Enabled         │
│ Welcome message sent to new users after         │
│ registration                                     │
├─────────────────────────────────────────────────┤
│ Message Template:                               │
│ ┌─────────────────────────────────────────────┐│
│ │ *Welcome to {website_name}!* 👋            ││
│ │                                             ││
│ │ Hello *{username}*,                        ││
│ │                                             ││
│ │ Thank you for joining us!                  ││
│ └─────────────────────────────────────────────┘│
│                                                 │
│ Available Variables:                            │
│ {username} {email} {balance} {currency_symbol} │
│ {website_name}                                 │
│                                                 │
│ 💡 Use these variables in your template...     │
└─────────────────────────────────────────────────┘
```

### Buttons
- Primary: "Save All Notification Settings" (Blue, large)
- Secondary: "API Settings" (Outlined, large)

---

## Sidebar Navigation

### Card Header
- Background: WhatsApp green
- Title: "WhatsApp Settings" with icon

### Navigation Links
1. **API Configuration** (active indicator)
   - Icon: Cog
   - Link: /whatsapp_notification/api_settings

2. **Notification Templates**
   - Icon: Bell
   - Link: /whatsapp_notification/notification_templates

### Quick Guide Card
Light colored card with:
- Title: "Quick Guide"
- Three bullet points with helpful tips
- Small text, muted color

---

## Color Scheme

**Primary Colors:**
- WhatsApp Green: #25D366
- White: #FFFFFF
- Light Gray: #F8F9FA

**Status Colors:**
- Success/Enabled: #28A745
- Secondary/Disabled: #6C757D
- Info: #17A2B8

**UI Elements:**
- Card borders: #E0E0E0
- Hover state: Elevated shadow
- Focus state: Green outline

---

## Interactive Features

### Hover Effects:
- Cards: Shadow elevation
- Variables: Color change and slight lift
- Buttons: Darker shade

### Toggle Switch:
- Large custom switch (3rem wide)
- Smooth animation on change
- Badge updates in real-time

### Copy Variables:
- Click variable tag
- Shows "Copied!" temporarily
- Returns to original text

### Form Validation:
- Real-time validation
- Error messages inline
- Success notifications

---

## Responsive Design

**Desktop (>768px):**
- Sidebar on left (3 columns)
- Content on right (9 columns)
- Two-column form layout

**Mobile (<768px):**
- Sidebar stacks on top
- Full-width content
- Single column forms
- Touch-friendly controls

---

## Accessibility

- High contrast text
- Clear focus states
- Keyboard navigation
- Screen reader friendly
- Semantic HTML structure

---

This modern UI provides a professional, user-friendly interface for managing WhatsApp notifications with visual feedback, clear organization, and intuitive controls.
