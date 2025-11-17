# Order Completion Tracking & Average Time Feature

## Overview
This feature tracks order completion times and calculates average completion times for services based on the last 10 completed orders. It provides users with estimated completion times when placing new orders.

## Database Changes

### Migration File
Run the SQL migration file to add the required columns:
```bash
mysql -u username -p database_name < database/order-completion-tracking.sql
```

### New Columns

#### orders table
- **completed_at** (DATETIME, NULL): Stores the timestamp when an order status changes to 'completed'
- **last_10_avg_time** (INT, NULL): Stores the average completion time (in seconds) of the last 10 completed orders for this order's service

#### services table
- **avg_completion_time** (INT, NULL): Stores the average completion time (in seconds) for the service based on the last 10 completed orders

## Automatic Tracking

### When `completed_at` is Set
The `completed_at` timestamp is automatically set when an order status changes to 'completed' in two scenarios:

1. **API Provider Cron** (`/cron/status`): When the cron job checks order status via API and receives 'completed' status
2. **Admin Manual Update**: When an admin manually updates an order status to 'completed' via the admin panel

### Code Locations
- `app/modules/api_provider/controllers/api_provider.php` - Lines ~1043-1046 (drip-feed) and ~1085-1088 (regular orders)
- `app/modules/order/controllers/order.php` - Lines ~949-952

## Cron Job for Average Calculation

### Setup
Add the following cron job to run every few hours (recommended: every 3-6 hours):

```bash
# Run every 3 hours
0 */3 * * * wget --spider -o - https://yourdomain.com/cron/completion_time >/dev/null 2>&1
```

Or add to crontab:
```bash
crontab -e
```

### What It Does
The cron job (`/cron/completion_time`):
1. Loops through all services
2. For each service, fetches the last 10 completed orders
3. Calculates average completion time using: `TIMESTAMPDIFF(SECOND, created, completed_at)`
4. Updates `services.avg_completion_time` with the calculated average
5. Updates `orders.last_10_avg_time` for the last 50 completed orders of each service

### Manual Execution
You can manually trigger the calculation by visiting:
```
https://yourdomain.com/cron/completion_time
```

## User Interface

### Order Add Form
When a user selects a service in the order/add form:
- If the service has an average completion time (`avg_completion_time > 0`), it displays an info alert
- Shows the time in human-readable format (e.g., "2 hours, 30 minutes, 45 seconds")
- Includes a note "(Based on last 10 orders)"

### Display Example
```
Average Completion Time
🕐 2 hours, 30 minutes (Based on last 10 orders)
```

## Technical Details

### Calculation Logic
```sql
SELECT AVG(TIMESTAMPDIFF(SECOND, created, completed_at)) AS avg_time
FROM orders
WHERE service_id = :service_id 
  AND status = 'completed'
  AND completed_at IS NOT NULL
ORDER BY completed_at DESC
LIMIT 10
```

### Exclusions
The calculation automatically excludes:
- Orders with status other than 'completed'
- Orders without a `completed_at` timestamp
- Orders older than the last 10 completed orders

### Time Conversion
Times are stored in seconds and converted to human-readable format:
- Hours: `floor(seconds / 3600)`
- Minutes: `floor((seconds % 3600) / 60)`
- Seconds: `seconds % 60`

## Files Modified/Created

### New Files
- `database/order-completion-tracking.sql` - Database migration
- `app/controllers/order_completion_cron.php` - Cron controller for calculations

### Modified Files
- `app/modules/api_provider/controllers/api_provider.php` - Added completed_at tracking in cron
- `app/modules/order/controllers/order.php` - Added completed_at tracking for manual updates
- `app/modules/order/views/add/get_service.php` - Display average completion time
- `app/config/routes.php` - Added route for completion_time cron
- `app/language/english/common_lang.php` - Added language keys
- `cron-jobs.txt` - Added new cron job entry

## Language Keys

New language keys added to `app/language/english/common_lang.php`:
- `Average_Completion_Time` - "Average Completion Time"
- `based_on_last_10_orders` - "(Based on last 10 orders)"
- `hour` / `hours` - Singular and plural
- `minute` / `minutes` - Singular and plural
- `second` / `seconds` - Singular and plural

## Backward Compatibility

- Existing orders without `completed_at` are excluded from calculations
- The feature gracefully handles services with no completed orders
- Display only shows if avg_completion_time > 0

## Performance Considerations

- The cron job processes services in batches with progress logging every 50 services
- Query uses indexes on `service_id`, `status`, and `completed_at`
- Results are cached in the database to avoid real-time calculations
- AJAX calls fetch pre-calculated values from the services table

## Troubleshooting

### Average not showing?
1. Ensure the database migration has been run
2. Run the cron job manually: `https://yourdomain.com/cron/completion_time`
3. Check that at least one order for the service has been completed with a `completed_at` timestamp

### Cron not running?
1. Verify cron is set up correctly: `crontab -l`
2. Check server logs for errors
3. Test manual execution via browser

### Times seem incorrect?
1. Ensure server timezone is configured correctly
2. Verify `completed_at` timestamps are being set properly
3. Check that orders are actually completing (status = 'completed')
