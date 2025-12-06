# Code Parts Module - Testing Guide

## Overview
This document provides a comprehensive testing plan to verify all functionality of the enhanced Code Parts module works correctly on both localhost and live servers.

## Pre-Test Checklist

Before running tests, ensure:
- [ ] Database has `code_parts` table (run `/database/code-parts.sql` if needed)
- [ ] User is logged in as admin
- [ ] Browser cache is cleared
- [ ] Browser DevTools console is open for debugging

## Test Suite

### 1. Tab Navigation Tests

#### Test 1.1: Click Navigation
**Objective**: Verify tabs switch when clicked

**Steps**:
1. Navigate to `/code_parts`
2. Click on "New Order" tab
3. Click on "Services" tab
4. Click on "Dashboard" tab

**Expected Results**:
- ✅ Each click switches the visible content
- ✅ URL hash updates (e.g., `#code_new_order`)
- ✅ Active tab is highlighted with blue color
- ✅ Previous tab becomes inactive (white background)

**Failure Indicators**:
- ❌ Tab doesn't switch
- ❌ URL doesn't update
- ❌ Multiple tabs appear active

---

#### Test 1.2: Hash Navigation
**Objective**: Verify direct URL hash navigation works

**Steps**:
1. Open `/code_parts#code_services`
2. Open `/code_parts#code_add_funds`
3. Open `/code_parts#code_api`

**Expected Results**:
- ✅ Correct tab is active when page loads
- ✅ Content for that tab is visible
- ✅ Editor for that tab initializes

**Failure Indicators**:
- ❌ Dashboard always shows (original bug)
- ❌ Wrong tab is active
- ❌ No tab is active

---

#### Test 1.3: Browser History
**Objective**: Verify back/forward buttons work

**Steps**:
1. Navigate to `/code_parts`
2. Click "New Order" tab
3. Click "Services" tab
4. Click browser back button
5. Click browser back button again
6. Click browser forward button

**Expected Results**:
- ✅ Back button returns to "New Order" tab
- ✅ Second back returns to "Dashboard" tab
- ✅ Forward button returns to "Services" tab
- ✅ Each navigation updates visible content

**Failure Indicators**:
- ❌ Back/forward navigates away from page
- ❌ Tab doesn't switch with history navigation

---

### 2. Performance Tests

#### Test 2.1: Initial Load Time
**Objective**: Verify fast initial page load

**Steps**:
1. Open browser DevTools > Performance tab
2. Clear cache
3. Navigate to `/code_parts`
4. Stop recording after page fully loads

**Expected Results**:
- ✅ Page loads in under 1 second
- ✅ Only first tab's editor is initialized
- ✅ Console shows "Code Parts page loaded" message
- ✅ No JavaScript errors in console

**Metrics to Check**:
- Initial load: < 1 second
- DOM Content Loaded: < 500ms
- No TinyMCE initialization errors

---

#### Test 2.2: Editor Lazy Loading
**Objective**: Verify editors load on-demand

**Steps**:
1. Navigate to `/code_parts` (Dashboard tab)
2. Wait for page to fully load
3. Open DevTools Console
4. Check for initialization messages
5. Click "New Order" tab
6. Wait for loading indicator
7. Click "Services" tab

**Expected Results**:
- ✅ Dashboard editor initializes on page load
- ✅ New Order editor initializes when tab is clicked
- ✅ Loading indicator appears briefly (spinner icon)
- ✅ Console shows "Editor initialized for #code_new_order"
- ✅ Green dot appears next to tab name after loading

**Failure Indicators**:
- ❌ All editors load at once (slow)
- ❌ Editor doesn't load when tab is clicked
- ❌ Multiple loading indicators appear

---

#### Test 2.3: Memory Usage
**Objective**: Verify efficient memory usage

**Steps**:
1. Open DevTools > Memory tab
2. Take heap snapshot
3. Navigate to `/code_parts`
4. Take another heap snapshot
5. Compare memory usage

**Expected Results**:
- ✅ Initial memory increase is minimal (< 10MB)
- ✅ Memory increases gradually as tabs are visited
- ✅ No memory leaks detected

---

### 3. Content Saving Tests

#### Test 3.1: Basic Save
**Objective**: Verify content saves correctly

**Steps**:
1. Navigate to `/code_parts`
2. Click "Dashboard" tab
3. Add test content: `<h1>Test Dashboard Content</h1>`
4. Click "Save" button
5. Wait for success message
6. Refresh page
7. Check if content is still there

**Expected Results**:
- ✅ Success message appears
- ✅ Content persists after refresh
- ✅ TinyMCE content is saved to database

**Failure Indicators**:
- ❌ Error message appears
- ❌ Content is lost after refresh

---

#### Test 3.2: HTML Sanitization
**Objective**: Verify dangerous HTML is removed

**Steps**:
1. Click "New Order" tab
2. Add malicious content:
   ```html
   <script>alert('XSS')</script>
   <h1 onclick="alert('click')">Test</h1>
   <iframe src="evil.com"></iframe>
   ```
3. Click "Save"
4. Refresh page
5. Check saved content

**Expected Results**:
- ✅ `<script>` tag is removed
- ✅ `onclick` handler is removed
- ✅ `<iframe>` is removed
- ✅ `<h1>` tag remains (without onclick)

**Database Check**:
```sql
SELECT content FROM code_parts WHERE page_key = 'new_order';
```

---

#### Test 3.3: Multiple Tab Saves
**Objective**: Verify saving across different tabs

**Steps**:
1. Click "Dashboard" tab, add content, save
2. Click "Services" tab, add content, save
3. Click "API" tab, add content, save
4. Refresh page
5. Visit each tab and verify content

**Expected Results**:
- ✅ Each tab's content is saved independently
- ✅ No content mixing between tabs
- ✅ All content persists after refresh

---

### 4. Editor Functionality Tests

#### Test 4.1: TinyMCE Features
**Objective**: Verify TinyMCE editor works properly

**Steps**:
1. Click any tab
2. Wait for editor to load
3. Test these features:
   - Bold/italic/underline
   - Font size changes
   - Text alignment
   - Code view button (first in toolbar)
   - Insert link
   - Insert image

**Expected Results**:
- ✅ All formatting buttons work
- ✅ Code view shows HTML
- ✅ Content persists when switching between visual/code mode

---

#### Test 4.2: Template Variables
**Objective**: Verify variable substitution info is shown

**Steps**:
1. Navigate to `/code_parts`
2. Scroll to info alerts
3. Check variable list

**Expected Results**:
- ✅ Variables list is visible
- ✅ Shows user variables ({{user.balance}}, etc.)
- ✅ Shows site variables ({{site.name}}, etc.)
- ✅ Shows date variables ({{date.today}}, etc.)

---

### 5. Visual Feedback Tests

#### Test 5.1: Loading Indicators
**Objective**: Verify loading states are shown

**Steps**:
1. Navigate to `/code_parts`
2. Click "New Order" tab (first time)
3. Watch for loading indicator
4. Click "Services" tab (first time)

**Expected Results**:
- ✅ Spinner icon appears
- ✅ "Loading editor..." text is shown
- ✅ Indicator disappears when editor is ready
- ✅ Smooth fade-out animation

---

#### Test 5.2: Loaded Badges
**Objective**: Verify green dots show loaded editors

**Steps**:
1. Navigate to `/code_parts`
2. Click "Dashboard" tab
3. Look for green dot next to tab name
4. Click "New Order" tab
5. Wait for editor to load
6. Check for green dot

**Expected Results**:
- ✅ Green dot appears after editor loads
- ✅ Dot persists when switching tabs
- ✅ Dot indicates which editors are ready

---

### 6. Responsive Design Tests

#### Test 6.1: Mobile View
**Objective**: Verify mobile responsiveness

**Steps**:
1. Open DevTools
2. Toggle device toolbar (mobile emulation)
3. Set viewport to iPhone SE (375px)
4. Navigate to `/code_parts`
5. Try switching tabs

**Expected Results**:
- ✅ Tabs stack vertically
- ✅ Full-width tab buttons
- ✅ Tab switching works
- ✅ Editor is usable

---

#### Test 6.2: Tablet View
**Objective**: Verify tablet layout

**Steps**:
1. Set viewport to iPad (768px)
2. Check tab layout
3. Test all functionality

**Expected Results**:
- ✅ Tabs display properly
- ✅ All features work
- ✅ Good readability

---

### 7. Error Handling Tests

#### Test 7.1: Missing Table
**Objective**: Verify graceful degradation

**Steps**:
1. Temporarily rename `code_parts` table in database
2. Try to save content
3. Check error message

**Expected Results**:
- ✅ Clear error message about missing table
- ✅ Suggestion to run migration script
- ✅ No JavaScript errors

---

#### Test 7.2: Network Error
**Objective**: Verify save failure handling

**Steps**:
1. Open DevTools > Network tab
2. Enable "Offline" mode
3. Try to save content
4. Check error message

**Expected Results**:
- ✅ Error message appears
- ✅ Content is not lost
- ✅ User can retry

---

### 8. Accessibility Tests

#### Test 8.1: Keyboard Navigation
**Objective**: Verify keyboard accessibility

**Steps**:
1. Navigate to `/code_parts`
2. Press Tab key repeatedly
3. Use Enter to activate tabs
4. Use arrow keys if supported

**Expected Results**:
- ✅ Focus is visible on tabs
- ✅ Can navigate with keyboard
- ✅ Enter key activates tabs

---

#### Test 8.2: Screen Reader
**Objective**: Verify screen reader compatibility

**Steps**:
1. Enable screen reader (NVDA/JAWS)
2. Navigate to page
3. Tab through elements

**Expected Results**:
- ✅ Tabs are announced
- ✅ Active state is communicated
- ✅ Content is readable

---

### 9. Browser Compatibility Tests

Run all core tests on:

- [ ] **Chrome** (latest)
  - Tab navigation
  - Editor loading
  - Content saving

- [ ] **Firefox** (latest)
  - Tab navigation
  - Editor loading
  - Content saving

- [ ] **Safari** (latest)
  - Tab navigation
  - Editor loading
  - Content saving

- [ ] **Edge** (latest)
  - Tab navigation
  - Editor loading
  - Content saving

---

### 10. Live Server Specific Tests

**Important**: These tests MUST be run on live Linux hosting to verify the original bug is fixed.

#### Test 10.1: Live Server Tab Switching
**Objective**: Verify tabs work on live server (original bug location)

**Steps**:
1. Deploy to live Linux server
2. Navigate to `/code_parts`
3. Click "New Order" tab
4. Click "Services" tab
5. Check URL hash

**Expected Results**:
- ✅ Tabs switch properly (was broken before)
- ✅ URL hash updates
- ✅ No stuck on first tab

**Critical**: If this test fails, the core issue is not fixed.

---

#### Test 10.2: Live Server Hash Navigation
**Objective**: Verify direct hash URLs work on live server

**Steps**:
1. Open `/code_parts#code_new_order` on live server
2. Check which tab is active
3. Try `/code_parts#code_services`

**Expected Results**:
- ✅ Correct tab activates
- ✅ Hash navigation works
- ✅ No default to first tab

---

## Security Verification

### XSS Prevention
Test that these are removed/neutralized:
- [ ] `<script>` tags
- [ ] `onclick` handlers
- [ ] `javascript:` URLs
- [ ] `<iframe>` tags
- [ ] `<object>` tags

### CSRF Protection
- [ ] All forms include CSRF token
- [ ] POST requests are protected

### SQL Injection
- [ ] All database queries use prepared statements
- [ ] User input is sanitized

---

## Performance Benchmarks

Record these metrics:

| Metric | Target | Actual |
|--------|--------|--------|
| Initial Page Load | < 1s | ___ |
| Dashboard Editor Load | < 300ms | ___ |
| First Tab Switch | < 300ms | ___ |
| Subsequent Tab Switch | Instant | ___ |
| Save Action | < 2s | ___ |
| Memory Initial | < 10MB | ___ |
| Memory Peak (all tabs) | < 30MB | ___ |

---

## Regression Testing

Verify existing features still work:

- [ ] Admin can access module
- [ ] Non-admin cannot access
- [ ] All 11 page keys exist
- [ ] Variable processing works
- [ ] Status toggle works (if implemented)
- [ ] Created/updated timestamps update

---

## Bug Reporting Template

If you find issues, report using this format:

```
**Bug Title**: [Short description]

**Environment**:
- Server: [localhost/live Linux]
- Browser: [Chrome/Firefox/Safari/Edge]
- Version: [Browser version]

**Steps to Reproduce**:
1. 
2. 
3. 

**Expected Behavior**:
[What should happen]

**Actual Behavior**:
[What actually happens]

**Screenshots**:
[Attach screenshots]

**Console Errors**:
[Paste any console errors]

**Additional Info**:
[Any other relevant details]
```

---

## Test Results Summary

After completing all tests, fill out:

| Category | Tests Passed | Tests Failed | Notes |
|----------|--------------|--------------|-------|
| Tab Navigation | __/6 | __/6 | |
| Performance | __/3 | __/3 | |
| Content Saving | __/3 | __/3 | |
| Editor Functionality | __/2 | __/2 | |
| Visual Feedback | __/2 | __/2 | |
| Responsive Design | __/2 | __/2 | |
| Error Handling | __/2 | __/2 | |
| Accessibility | __/2 | __/2 | |
| Browser Compatibility | __/4 | __/4 | |
| Live Server | __/2 | __/2 | |
| **TOTAL** | **__/32** | **__/32** | |

---

## Sign-Off

**Tested By**: _______________  
**Date**: _______________  
**Environment**: [ ] Localhost [ ] Live Server  
**Overall Status**: [ ] PASS [ ] FAIL  

**Comments**:
```
[Add any additional notes or observations]
```

---

## Quick Test (5 minutes)

If you only have 5 minutes, test these critical items:

1. [ ] Click 3 different tabs - all should switch
2. [ ] Open `/code_parts#code_services` - Services tab should be active
3. [ ] Save content in any tab - should persist after refresh
4. [ ] Test on live server - tabs should switch (was broken before)
5. [ ] Check console - no JavaScript errors

If all 5 pass, deployment is likely safe. Run full test suite when possible.
