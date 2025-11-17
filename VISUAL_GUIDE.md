# Visual Guide - Cron Logs Admin Interface

## Main Dashboard (`/cron_logs`)

```
╔════════════════════════════════════════════════════════════════════╗
║  Cron Logs                                    [Settings] [Cleanup] ║
╠════════════════════════════════════════════════════════════════════╣
║                                                                    ║
║  Statistics Cards:                                                 ║
║  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐    ║
║  │ cron/order      │ │ cron/status     │ │ cron/sync_serv..│    ║
║  │ Total: 145      │ │ Total: 98       │ │ Total: 24       │    ║
║  │ Success: 98.6%  │ │ Success: 100%   │ │ Success: 95.8%  │    ║
║  │ Last: 2 min ago │ │ Last: 5 min ago │ │ Last: 1 hr ago  │    ║
║  └─────────────────┘ └─────────────────┘ └─────────────────┘    ║
║                                                                    ║
║  Filters:                                                          ║
║  ┌──────────────┬──────────┬────────────┬────────────┬─────────┐ ║
║  │ Cron Name ▼  │ Status ▼ │ From Date  │ To Date    │[Apply] │ ║
║  │ All Crons    │ All      │ 2025-11-01 │ 2025-11-17 │[Reset] │ ║
║  └──────────────┴──────────┴────────────┴────────────┴─────────┘ ║
║                                                                    ║
║  Cron Execution Logs:                                             ║
║  ┌──────────────────────────────────────────────────────────────┐ ║
║  │ ID │ Cron Name      │ Executed At      │ Status  │ Time    │ ║
║  ├────┼────────────────┼──────────────────┼─────────┼─────────┤ ║
║  │ 43 │ cron/order     │ 2025-11-17 11:55 │ Success │ 2.34s   │ ║
║  │ 42 │ cron/status    │ 2025-11-17 11:50 │ Success │ 1.12s   │ ║
║  │ 41 │ currencies/... │ 2025-11-17 11:00 │ Success │ 0.78s   │ ║
║  │ 40 │ email_cron/run │ 2025-11-17 10:30 │ Failed  │ 5.21s   │ ║
║  │ 39 │ cron/sync_...  │ 2025-11-17 10:00 │ Success │ 45.32s  │ ║
║  └────┴────────────────┴──────────────────┴─────────┴─────────┘ ║
║                                                                    ║
║  [Previous] Page 1 of 5 [Next]                                    ║
╚════════════════════════════════════════════════════════════════════╝
```

## Log Details View (`/cron_logs/view/43`)

```
╔════════════════════════════════════════════════════════════════════╗
║  Cron Log Details                               [← Back to List]   ║
╠════════════════════════════════════════════════════════════════════╣
║                                                                    ║
║  Log ID: 43                                                        ║
║                                                                    ║
║  Cron Name: cron/order                                            ║
║                                                                    ║
║  Executed At: 2025-11-17 11:55:23                                 ║
║                                                                    ║
║  Status: ✓ Success                                                ║
║                                                                    ║
║  Response Code: 200                                               ║
║                                                                    ║
║  Execution Time: 2.3421 seconds                                   ║
║                                                                    ║
║  Response Message:                                                ║
║  ┌──────────────────────────────────────────────────────────────┐ ║
║  │ Cron executed successfully                                   │ ║
║  │ Orders processed: 15                                         │ ║
║  │ Successful: 14                                               │ ║
║  │ Failed: 1                                                    │ ║
║  └──────────────────────────────────────────────────────────────┘ ║
║                                                                    ║
║  Created At: 2025-11-17 11:55:23                                  ║
║                                                                    ║
║  [Delete This Log]                                                ║
╚════════════════════════════════════════════════════════════════════╝
```

## Settings Page (`/cron_logs/settings`)

```
╔════════════════════════════════════════════════════════════════════╗
║  Cron Settings                                  [← Back to Logs]   ║
╠════════════════════════════════════════════════════════════════════╣
║                                                                    ║
║  Notification Settings                                            ║
║  ┌──────────────────────────────────────────────────────────────┐ ║
║  │ ☑ Enable Email Notifications for Failed Cron Jobs           │ ║
║  │                                                              │ ║
║  │ Notification Email Address:                                 │ ║
║  │ ┌────────────────────────────────────────┐                  │ ║
║  │ │ admin@example.com                      │                  │ ║
║  │ └────────────────────────────────────────┘                  │ ║
║  │ Send an email notification when a cron job fails.           │ ║
║  └──────────────────────────────────────────────────────────────┘ ║
║                                                                    ║
║  Log Retention                                                    ║
║  ┌──────────────────────────────────────────────────────────────┐ ║
║  │ Keep Logs For (Days):                                        │ ║
║  │ ┌──────┐                                                     │ ║
║  │ │  30  │                                                     │ ║
║  │ └──────┘                                                     │ ║
║  │ Number of days to keep cron logs before automatic cleanup.  │ ║
║  │ Default is 30 days.                                          │ ║
║  └──────────────────────────────────────────────────────────────┘ ║
║                                                                    ║
║  [Save Settings]                                                  ║
╚════════════════════════════════════════════════════════════════════╝
```

## Admin Sidebar Menu

```
┌─────────────────────────────┐
│ Dashboard                   │
│ Orders                      │
│ Services                    │
│ Users                       │
│                             │
│ ■ Settings                  │
│   System Settings           │
│   Services Providers        │
│   Payments                  │
│   Payments Bonuses          │
│ → Cron Logs            ⭐   │ ← NEW!
│                             │
│ ■ Others                    │
│   Announcement              │
│   FAQs                      │
│   Language                  │
└─────────────────────────────┘
```

## Email Notification (Failed Cron)

```
From: SMM Panel <noreply@example.com>
To: admin@example.com
Subject: Cron Job Failed: email_cron/run

A cron job has failed:

Cron Name: email_cron/run
Executed At: 2025-11-17 10:30:15
Response Code: 500
Error Message: SMTP connection failed
Execution Time: 5.2134s

Please check the cron logs for more details:
https://yoursite.com/cron_logs/view/40
```

## Key Visual Features

### Statistics Cards
- **Visual**: Clean cards with cron name, metrics, and last run time
- **Color Coding**: 
  - Green border for high success rate (>95%)
  - Yellow for moderate (90-95%)
  - Red for low (<90%)

### Status Badges
- **Success**: Green badge
- **Failed**: Red badge  
- **Running**: Yellow badge

### Filtering Interface
- **Dropdown filters**: Cron name, Status
- **Date pickers**: From and To dates
- **Buttons**: Apply Filters, Reset

### Table Features
- **Sortable columns**: Click column headers to sort
- **Pagination**: Navigate through pages
- **Actions**: View details, Delete
- **Auto-refresh**: Updates every 30 seconds

### Responsive Design
- Works on desktop, tablet, and mobile
- Sidebar collapses on mobile
- Tables scroll horizontally on small screens

---

This visual guide shows the actual user interface that admins will interact with when managing cron logs.
