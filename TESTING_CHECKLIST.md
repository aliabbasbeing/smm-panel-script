# Google Login Integration - Testing Checklist

## Pre-Deployment Testing

### 1. Database Setup
- [ ] Run `database/google-login.sql` migration
- [ ] Verify `google_id` field exists in `general_users` table
- [ ] Verify `google_id` has index for performance
- [ ] Verify `enable_google_login` option exists in `general_options`
- [ ] Verify `google_client_id` option exists in `general_options`
- [ ] Verify `google_client_secret` option exists in `general_options`
- [ ] Check database character set supports UTF-8

### 2. Google Cloud Console Setup
- [ ] Create Google Cloud project
- [ ] Enable Google+ API (or People API)
- [ ] Create OAuth 2.0 credentials
- [ ] Configure OAuth consent screen
- [ ] Add authorized JavaScript origins
- [ ] Add authorized redirect URIs (must match exactly)
- [ ] Copy Client ID
- [ ] Copy Client Secret
- [ ] Set publishing status (Testing/Production)

### 3. Admin Configuration
- [ ] Log in as admin
- [ ] Navigate to Settings → Google OAuth
- [ ] Enable Google Login toggle
- [ ] Paste Client ID
- [ ] Paste Client Secret
- [ ] Click Save
- [ ] Verify settings are saved (refresh page)

### 4. Login Page - Visual Verification
- [ ] Log out
- [ ] Visit login page
- [ ] Verify "Sign in with Google" button appears
- [ ] Verify Google logo displays correctly
- [ ] Verify button styling matches design
- [ ] Verify "OR" divider appears
- [ ] Verify button is full width
- [ ] Verify hover effect works

### 5. OAuth Flow - New User
- [ ] Click "Sign in with Google" button
- [ ] Verify redirect to Google login
- [ ] Log in with test Google account (new user)
- [ ] Authorize the application
- [ ] Verify redirect back to panel
- [ ] Verify user is logged in
- [ ] Verify redirect to dashboard/statistics
- [ ] Check user in database:
  - [ ] User exists in `general_users`
  - [ ] `google_id` is populated
  - [ ] `login_type` is 'google'
  - [ ] `email` matches Google account
  - [ ] `first_name` is set
  - [ ] `last_name` is set
  - [ ] `status` is 1 (active)
  - [ ] `password` is empty
  - [ ] `created` timestamp is set

### 6. OAuth Flow - Existing User
- [ ] Create user manually with email matching Google account
- [ ] Click "Sign in with Google" button
- [ ] Log in with Google account
- [ ] Verify user is logged in
- [ ] Check database:
  - [ ] Existing user record updated
  - [ ] `google_id` is now populated
  - [ ] `login_type` updated to 'google'
  - [ ] Other fields unchanged

### 7. Session Management
- [ ] Verify session is created on login
- [ ] Verify session contains user data
- [ ] Verify role is set correctly
- [ ] Verify timezone is set
- [ ] Navigate to different pages
- [ ] Verify session persists
- [ ] Log out
- [ ] Verify session is cleared

### 8. User Activity Logging
- [ ] After Google login, check `general_user_logs` table
- [ ] Verify login record exists
- [ ] Verify IP address is logged
- [ ] Verify type is 1 (login)
- [ ] Verify country is detected (if available)

### 9. Email Notifications
If welcome email is enabled:
- [ ] New Google user receives welcome email
- [ ] Email contains correct user information
- [ ] Email is sent to correct address

If admin notification is enabled:
- [ ] Admin receives new user notification
- [ ] Notification contains Google user info
- [ ] Notification is sent to admin email

### 10. WhatsApp Alerts
If WhatsApp is configured:
- [ ] New user receives WhatsApp signup alert
- [ ] Admin receives WhatsApp login alert
- [ ] Alerts contain correct information
- [ ] No errors in alert sending

### 11. Error Handling
- [ ] Test with invalid OAuth code
  - [ ] Verify redirect to login page
  - [ ] No PHP errors displayed
- [ ] Test with expired OAuth token
  - [ ] Verify graceful handling
  - [ ] Redirect to login page
- [ ] Test with Google login disabled
  - [ ] Button does not appear
  - [ ] Direct URL access redirects to login
- [ ] Test with missing credentials
  - [ ] Button does not appear
  - [ ] Direct URL access redirects to login
- [ ] Test with blocked IP
  - [ ] Verify "IP blocked" message
  - [ ] User cannot proceed

### 12. Security Testing
- [ ] Verify HTTPS is enabled (required)
- [ ] Verify Client Secret is not exposed in HTML
- [ ] Verify OAuth tokens are validated server-side
- [ ] Test CSRF protection
- [ ] Verify no SQL injection vulnerabilities
- [ ] Check for XSS vulnerabilities
- [ ] Verify redirect URI cannot be manipulated

### 13. Browser Compatibility
Test on multiple browsers:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### 14. Responsive Design
- [ ] Test on desktop (1920x1080)
- [ ] Test on laptop (1366x768)
- [ ] Test on tablet (768x1024)
- [ ] Test on mobile (375x667)
- [ ] Verify button scales properly
- [ ] Verify text is readable
- [ ] Verify spacing is appropriate

### 15. Theme Compatibility
- [ ] Test with Pergo theme (active)
- [ ] Test with Regular theme
- [ ] Verify Monoka theme (no login page, should redirect)
- [ ] Switch themes and verify button appears

### 16. Admin Settings Interface
- [ ] Access Google OAuth settings
- [ ] Verify all fields display
- [ ] Verify instructions are clear
- [ ] Test save functionality
- [ ] Test reset button
- [ ] Verify redirect URI is read-only
- [ ] Verify toggle switch works
- [ ] Test with empty fields (validation)

### 17. Edge Cases
- [ ] Google account with no last name
  - [ ] Verify first_name only is used
- [ ] Google account with special characters in name
  - [ ] Verify proper encoding
- [ ] Multiple login attempts
  - [ ] Verify no duplicate users created
- [ ] Concurrent logins
  - [ ] Verify session handling
- [ ] User exists with same email but different google_id
  - [ ] Verify proper matching by email

### 18. Performance Testing
- [ ] Measure OAuth redirect time
- [ ] Measure callback processing time
- [ ] Verify database queries are optimized
- [ ] Check for N+1 query issues
- [ ] Test with 100+ existing users

### 19. Integration Testing
- [ ] Affiliate system works with Google users
- [ ] Payment system works with Google users
- [ ] Order system works with Google users
- [ ] Ticket system works with Google users
- [ ] All user features accessible

### 20. Rollback Testing
- [ ] Disable Google login
- [ ] Verify button disappears
- [ ] Verify traditional login still works
- [ ] Re-enable Google login
- [ ] Verify button reappears

## Post-Deployment Monitoring

### Day 1
- [ ] Monitor error logs for PHP errors
- [ ] Monitor database for new Google users
- [ ] Check email delivery
- [ ] Check WhatsApp alerts
- [ ] Monitor server load

### Week 1
- [ ] Review Google Cloud Console logs
- [ ] Check OAuth success/failure rates
- [ ] Monitor user feedback
- [ ] Check for abuse or spam signups
- [ ] Review security logs

### Month 1
- [ ] Analyze Google login adoption rate
- [ ] Compare with traditional login usage
- [ ] Review and address any issues
- [ ] Consider UX improvements
- [ ] Update documentation as needed

## Security Audit Checklist

- [ ] Client Secret is stored securely
- [ ] No credentials in version control
- [ ] HTTPS is enforced
- [ ] OAuth tokens are not logged
- [ ] User data is sanitized
- [ ] SQL queries use prepared statements
- [ ] XSS protection is in place
- [ ] CSRF tokens are validated
- [ ] Rate limiting is considered
- [ ] IP blocking works correctly

## Documentation Review

- [ ] INSTALLATION_GUIDE.md is accurate
- [ ] GOOGLE_LOGIN_INTEGRATION.md is complete
- [ ] UI_DOCUMENTATION.md matches implementation
- [ ] Code comments are clear
- [ ] Admin instructions are user-friendly

## Sign-Off

**Tester Name**: _______________________  
**Test Date**: _______________________  
**Environment**: [ ] Development [ ] Staging [ ] Production  
**Overall Status**: [ ] Pass [ ] Fail  
**Issues Found**: _______________________  
**Ready for Production**: [ ] Yes [ ] No  

**Notes**:
___________________________________________
___________________________________________
___________________________________________

---

**Important**: Never deploy to production without completing this entire checklist!
