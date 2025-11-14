# Google Login Integration - SMM Panel

## Overview
This document describes the Google Login integration for the SMM Panel script. Users can now authenticate using their Google accounts in addition to the traditional email/password method.

## Features Implemented

### 1. Database Schema Updates
- **File**: `database/google-login.sql`
- Added `google_id` VARCHAR(255) field to `general_users` table
- Added index on `google_id` for faster lookups
- Added three new options to `general_options` table:
  - `enable_google_login`: Enable/disable Google login (default: 0)
  - `google_client_id`: Google OAuth Client ID
  - `google_client_secret`: Google OAuth Client Secret

### 2. Backend Implementation

#### Auth Controller (`app/modules/auth/controllers/auth.php`)
Added two new methods:

**`google()`** - Initiates Google OAuth flow
- Checks if Google login is enabled
- Validates Google credentials are configured
- Loads Google OAuth library with proper redirect URL
- Redirects user to Google authentication page

**`google_callback()`** - Handles Google OAuth callback
- Receives OAuth token from Google
- Retrieves user information from Google
- Checks if user exists by `google_id` or `email`
- For existing users:
  - Updates `google_id` if not set
  - Validates user status
  - Creates session
  - Logs activity
  - Sends WhatsApp alert
  - Redirects to dashboard
- For new users:
  - Creates new user record with Google information
  - Auto-activates account (status = 1)
  - Sets default timezone to 'Asia/Karachi'
  - Creates session
  - Sends welcome email if enabled
  - Sends admin notification if enabled
  - Sends WhatsApp signup alert
  - Redirects to dashboard

#### Settings Controller (`app/modules/setting/`)
- **View**: `views/google_oauth.php` - Admin settings page for Google OAuth
- **Sidebar**: Updated `views/sidebar.php` to add Google OAuth menu item

### 3. Frontend Implementation

#### Login Page (`themes/pergo/views/sign_in.php`)
- Added conditional Google login button (shows only when enabled)
- Added "OR" divider between traditional and Google login
- Integrated Google branding with official logo and colors
- Responsive design that matches existing theme

#### Styling (`themes/pergo/assets/css/theme_style.css`)
Added CSS classes:
- `.btn-google-login` - Google login button styling
- `.login-divider` - OR divider styling
- Hover and active states for better UX

### 4. Admin Settings Interface
The Google OAuth settings page includes:
- Enable/disable toggle
- Google Client ID input field
- Google Client Secret input field
- Read-only redirect URI field (for copying to Google Console)
- Step-by-step instructions for setting up Google OAuth
- Important notes and security warnings

## Setup Instructions

### 1. Database Migration
Run the SQL migration file:
```sql
mysql -u username -p database_name < database/google-login.sql
```

Or manually execute the SQL commands in your database.

### 2. Configure Google OAuth Application

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Navigate to "APIs & Services" > "Credentials"
4. Click "Create Credentials" and select "OAuth client ID"
5. Choose "Web application" as the application type
6. Configure the application:
   - **Name**: SMM Panel (or your preferred name)
   - **Authorized JavaScript origins**: Your website URL (e.g., `https://yourdomain.com`)
   - **Authorized redirect URIs**: `https://yourdomain.com/auth/google_callback`
7. Click "Create"
8. Copy the Client ID and Client Secret

### 3. Configure in Admin Panel

1. Log in to your admin panel
2. Navigate to "Settings" > "Google OAuth"
3. Enable Google Login toggle
4. Paste the Client ID in the "Google Client ID" field
5. Paste the Client Secret in the "Google Client Secret" field
6. Click "Save"

### 4. Test the Integration

1. Log out of the admin panel
2. Visit the login page
3. You should see a "Sign in with Google" button
4. Click it to test the Google authentication flow
5. Verify that you are redirected to Google
6. After authorizing, verify you are redirected back and logged in

## User Flow

### New User Registration via Google
1. User clicks "Sign in with Google" button
2. User is redirected to Google authentication page
3. User authorizes the application
4. System receives user information from Google
5. System creates new user account with:
   - Email from Google
   - First and last name from Google
   - Google ID stored in `google_id` field
   - `login_type` set to 'google'
   - No password (password field empty)
   - Auto-activated status
6. User is logged in automatically
7. Welcome email sent (if enabled)
8. Admin notification sent (if enabled)
9. WhatsApp alerts sent (if configured)

### Existing User Login via Google
1. User clicks "Sign in with Google" button
2. User is redirected to Google authentication page
3. User authorizes the application
4. System receives user information from Google
5. System finds existing user by email or `google_id`
6. System updates `google_id` if not previously set
7. User is logged in
8. Activity is logged
9. WhatsApp login alert sent (if configured)

## Security Considerations

1. **OAuth Token Validation**: All OAuth tokens are validated server-side
2. **HTTPS Required**: Google OAuth requires HTTPS for production
3. **Client Secret Protection**: Client Secret is stored in database, never exposed to frontend
4. **Session Security**: Standard session management applies to Google users
5. **Email Verification**: Google users are auto-activated (Google already verifies email)
6. **Error Handling**: All OAuth errors redirect to login page

## Database Fields Used

### `general_users` table
- `id` - Primary key
- `google_id` - Stores Google user ID (new field)
- `login_type` - Set to 'google' for Google users
- `email` - User's email from Google
- `first_name` - User's first name from Google
- `last_name` - User's last name from Google
- `status` - Account status (1 = active)
- `created` - Account creation timestamp
- `password` - Empty for Google users

### `general_options` table
- `enable_google_login` - Boolean (0/1)
- `google_client_id` - Google OAuth Client ID
- `google_client_secret` - Google OAuth Client Secret

## Files Modified

1. `app/modules/auth/controllers/auth.php` - Added Google OAuth methods
2. `themes/pergo/views/sign_in.php` - Added Google login button
3. `themes/pergo/assets/css/theme_style.css` - Added Google button styles
4. `app/modules/setting/views/google_oauth.php` - Created admin settings page
5. `app/modules/setting/views/sidebar.php` - Added menu item
6. `database/google-login.sql` - Database migration file

## Troubleshooting

### Google Login Button Not Showing
- Check if `enable_google_login` is set to 1 in `general_options`
- Verify `google_client_id` and `google_client_secret` are set
- Clear browser cache

### OAuth Error on Callback
- Verify redirect URI in Google Console matches exactly: `https://yourdomain.com/auth/google_callback`
- Check that HTTPS is enabled (required for production)
- Verify Client ID and Client Secret are correct

### User Creation Fails
- Check database permissions
- Verify `general_users` table has `google_id` field
- Check PHP error logs

### "Access from your IP address has been blocked"
- User's IP is in the blocked IP list
- Check `general_user_block_ip` table

## Future Enhancements

Potential improvements for future versions:
1. Support for other OAuth providers (Facebook, Twitter, etc.)
2. Account linking (allow users to link Google to existing accounts)
3. Profile picture sync from Google
4. Google account unlinking feature
5. Multi-language support for Google OAuth settings
6. OAuth token refresh for extended sessions

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review server error logs
3. Verify Google Cloud Console configuration
4. Contact system administrator

---
**Version**: 1.0  
**Last Updated**: November 14, 2024  
**Author**: SMM Panel Development Team
