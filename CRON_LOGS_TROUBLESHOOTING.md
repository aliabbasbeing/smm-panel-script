# Cron Logs Access Troubleshooting Guide

## Issue: Cron Logs Page Redirects to Statistics

If you're experiencing issues accessing the Cron Logs page (it redirects to statistics), follow these steps:

### Step 0: Enable Logging (CRITICAL)

**If you don't see any logs, logging might be disabled!**

1. Check `app/config/config.php` and find the `log_threshold` setting
2. If it's set to `0`, change it to at least `3` for debugging:
   ```php
   $config['log_threshold'] = 3; // or 4 for all messages
   ```
3. The values are:
   - 0 = Disable logging
   - 1 = Error Messages only
   - 2 = Debug Messages
   - 3 = Informational Messages
   - 4 = All Messages

4. **ALTERNATIVE**: Check the emergency debug file at `app/logs/cron_logs_debug.txt`
   - This file is written directly, bypassing CodeIgniter's logging system
   - It will show you the exact point where the redirect happens

### Step 1: Verify Admin Role

1. The cron_logs page requires admin access
2. Check your user role in the database:
   ```sql
   SELECT id, email, role FROM general_users WHERE id = YOUR_USER_ID;
   ```
3. The `role` field should be set to `admin`

### Step 2: Check Session Data

Access the debug endpoint to see your current session state:
```
https://yourdomain.com/cron_logs/debug_access
```

This will show:
- Whether `get_role('admin')` returns TRUE or FALSE
- Your session UID
- Current session data
- User role information

### Step 3: Review Logs

Check `app/logs/log-YYYY-MM-DD.php` for entries like:
```
INFO - Cron_logs access attempt - Is Admin: No, Session UID: 123, User Role: user
WARNING - Cron_logs access denied - User does not have admin role
```

### Step 4: Common Issues and Solutions

#### Issue: Role is not 'admin'
**Solution:** Update the database:
```sql
UPDATE general_users SET role = 'admin' WHERE id = YOUR_USER_ID;
```

#### Issue: Session not persisting
**Solution:** 
1. Clear browser cookies
2. Log out and log in again
3. Check that session files are writable: `app/cache/` or system temp directory

#### Issue: User info not in session
**Solution:**
1. The session might be expired
2. Log out completely
3. Clear browser cache
4. Log in again

### Step 5: Temporary Workaround (Development Only)

**WARNING: Only use this for debugging, never in production!**

If you need immediate access for testing, you can temporarily comment out the admin check in:
`app/modules/cron_logs/controllers/cron_logs.php`

Lines 17-21:
```php
// if (!$is_admin) {
//     log_message('warning', 'Cron_logs access denied - User does not have admin role. Redirecting to statistics.');
//     redirect(cn('statistics'));
//     return;
// }
```

**IMPORTANT:** Remember to uncomment this after debugging!

### Step 6: Check for Conflicting Code

1. Ensure you don't have multiple instances of the redirect code
2. Check if there's a custom theme or layout that's checking permissions
3. Look for any global middleware or hooks that might interfere

### Step 7: Browser Issues

1. Clear browser cache completely
2. Try accessing in incognito/private mode
3. Try a different browser
4. Check browser console for JavaScript errors

### Getting Help

If none of these solutions work, provide the following information:
1. Output from `/cron_logs/debug_access`
2. Relevant lines from `app/logs/log-YYYY-MM-DD.php`
3. Your user's role from the database
4. PHP and CodeIgniter version
5. Whether you're on shared hosting or VPS

## Security Note

The debug_access endpoint should be removed or secured once the issue is resolved, as it exposes session information.
