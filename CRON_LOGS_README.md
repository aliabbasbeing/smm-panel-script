# Cron Logging Error Logs

All cron execution errors and information are logged to the CodeIgniter log files located in:

```
app/logs/
```

## Log Format

Cron logs are written with the following format:

```
CRON [YYYY-MM-DD HH:MM:SS] /cron/endpoint - Status: success/failed, Code: 200, Time: 1.2345s, Message: Details
```

## Log Levels

- **INFO**: Successful cron executions and cron detection events
- **ERROR**: Failed cron executions, database errors, and hook failures

## Viewing Logs

You can view the logs using:

```bash
# View latest log file
tail -f app/logs/log-YYYY-MM-DD.php

# Search for cron-specific logs
grep "CRON \[" app/logs/log-YYYY-MM-DD.php

# Search for errors only
grep "ERROR.*CRON" app/logs/log-YYYY-MM-DD.php
```

## Log Retention

CodeIgniter automatically manages log files based on the configuration in `app/config/config.php`:
- `$config['log_threshold']` - Controls which messages are logged
- Old log files are kept according to your server's retention policy

## Troubleshooting

If you're experiencing issues with cron logging:

1. Check that the `app/logs/` directory is writable (chmod 755 or 777)
2. Review the log files for specific error messages
3. Check that the `cron_logs` table exists in the database
4. Verify that hooks are enabled in `app/config/config.php`

## Additional Database Logs

Cron execution details are also stored in the database table `cron_logs` and can be viewed through the admin interface at Settings > Cron Logs.
