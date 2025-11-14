# Google Login Integration - Quick Installation Guide

## Prerequisites
- SMM Panel installed and running
- Admin access to the panel
- Google Cloud Console account
- HTTPS enabled on your website (required for OAuth in production)

## Step 1: Database Migration

Run the SQL migration to add Google login support to your database:

```bash
# Method 1: Using MySQL command line
mysql -u your_username -p your_database_name < database/google-login.sql

# Method 2: Using phpMyAdmin
# 1. Login to phpMyAdmin
# 2. Select your database
# 3. Go to SQL tab
# 4. Copy and paste the contents of database/google-login.sql
# 5. Click Execute
```

The migration will:
- Add `google_id` field to the `general_users` table
- Add an index on `google_id` for performance
- Insert three new options for Google OAuth configuration

## Step 2: Set Up Google OAuth Application

### 2.1 Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click "Select a project" → "New Project"
3. Enter project name (e.g., "SMM Panel OAuth")
4. Click "Create"

### 2.2 Enable Google+ API

1. In your project, go to "APIs & Services" → "Library"
2. Search for "Google+ API"
3. Click on it and press "Enable"

### 2.3 Create OAuth Credentials

1. Go to "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "OAuth client ID"
3. If prompted, configure the consent screen:
   - Choose "External" for User Type
   - Fill in App name, User support email, and Developer contact
   - Add scopes: `userinfo.email` and `userinfo.profile`
   - Add test users if in development mode
   - Save and continue

4. Create OAuth Client ID:
   - Application type: **Web application**
   - Name: SMM Panel
   - Authorized JavaScript origins: 
     - `https://yourdomain.com`
   - Authorized redirect URIs:
     - `https://yourdomain.com/auth/google_callback`
   - Click "Create"

5. **Copy the Client ID and Client Secret** - you'll need these!

## Step 3: Configure SMM Panel

### 3.1 Access Admin Settings

1. Log in to your SMM Panel as admin
2. Navigate to **Settings** → **Google OAuth**

### 3.2 Configure Google Login

1. **Enable Google Login**: Toggle to ON
2. **Google Client ID**: Paste the Client ID from Step 2
3. **Google Client Secret**: Paste the Client Secret from Step 2
4. Click **Save**

## Step 4: Test the Integration

### 4.1 Test Login Flow

1. Log out of the admin panel
2. Visit your login page
3. You should see a "Sign in with Google" button below the regular login form
4. Click the Google button
5. You'll be redirected to Google's login page
6. Sign in with your Google account
7. Authorize the application
8. You should be redirected back to your panel and logged in

### 4.2 Verify User Creation

1. Log in as admin
2. Go to Users section
3. Find the user that logged in with Google
4. Verify that:
   - Email matches Google account
   - `google_id` field is populated
   - `login_type` is set to 'google'
   - User is active (status = 1)

## Step 5: Going Live

### Before Production

1. **SSL Certificate**: Ensure HTTPS is properly configured
2. **Consent Screen**: Publish your OAuth consent screen in Google Cloud Console
3. **Privacy Policy**: Add your privacy policy URL to the consent screen
4. **Test Thoroughly**: Test with multiple Google accounts
5. **Backup Database**: Always backup before major changes

### Production Checklist

- [ ] HTTPS is enabled and working
- [ ] Google OAuth consent screen is verified
- [ ] Client ID and Secret are correctly configured
- [ ] Test users can successfully log in with Google
- [ ] Existing users can link their Google accounts
- [ ] Email notifications work for Google users
- [ ] WhatsApp alerts work for Google users (if configured)
- [ ] Session management is working correctly

## Troubleshooting

### Common Issues

**1. "Redirect URI mismatch" error**
- Solution: Ensure the redirect URI in Google Console exactly matches: `https://yourdomain.com/auth/google_callback`
- Check for trailing slashes or HTTP vs HTTPS

**2. Google button not showing**
- Check if `enable_google_login` is set to 1 in database
- Verify Client ID and Secret are saved
- Clear browser cache
- Check browser console for JavaScript errors

**3. "OAuth error" or blank page**
- Verify Google+ API is enabled
- Check PHP error logs
- Ensure Google OAuth library is present in `app/libraries/Google/`
- Verify redirect URI is whitelisted in Google Console

**4. User creation fails**
- Check database permissions
- Verify `google_id` field exists in `general_users` table
- Check PHP error logs
- Ensure database user has INSERT permissions

**5. "Access from your IP address has been blocked"**
- User's IP is in the blocked IP list
- Check `general_user_block_ip` table
- Remove IP or whitelist it

### Enable Debug Mode (Development Only)

To see detailed error messages during development:

1. Edit `index.php`
2. Change `error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);`
   to `error_reporting(E_ALL);`
3. Set `ini_set('display_errors', 1);`

**Important**: Never enable debug mode in production!

## Security Considerations

1. **HTTPS Required**: Google OAuth requires HTTPS in production
2. **Keep Secrets Safe**: Never commit Client Secret to version control
3. **Rotate Secrets**: Periodically rotate your OAuth credentials
4. **Monitor Logs**: Regularly check for suspicious login attempts
5. **User Verification**: Google already verifies emails, so users are auto-activated

## Support

For detailed documentation, see: `GOOGLE_LOGIN_INTEGRATION.md`

For issues:
1. Check troubleshooting section above
2. Review PHP error logs
3. Check Google Cloud Console OAuth settings
4. Verify database schema

---
**Installation Date**: _____________  
**Installed By**: _____________  
**Version**: 1.0
