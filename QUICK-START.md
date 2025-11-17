# Quick Start Guide - Order Completion Tracking

## 1. Run Database Migration

```bash
mysql -u your_username -p your_database < database/order-completion-tracking.sql
```

## 2. Set Up Cron Job

Add to your crontab (runs every 3 hours):

```bash
0 */3 * * * wget --spider -o - https://yourdomain.com/cron/completion_time >/dev/null 2>&1
```

## 3. Test the Feature

1. Visit: `https://yourdomain.com/cron/completion_time`
2. Create a test order
3. Mark it as completed
4. Run the cron job again
5. Check the order add form - you should see the average completion time

## 4. Verify Installation

Run the verification script:

```bash
./verify-installation.sh
```

For detailed documentation, see: `database/ORDER-COMPLETION-FEATURE-README.md`
