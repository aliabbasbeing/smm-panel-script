-- Email Marketing Observability Update
-- This script adds time tracking and observability features to the email marketing module
-- Run this script after the initial email-marketing.sql setup
-- 
-- IMPORTANT: If you haven't run email-marketing-multi-smtp.sql yet, run it first!
-- That script adds the smtp_config_id column to email_logs which is required for SMTP tracking.

-- First, ensure smtp_config_id column exists (from email-marketing-multi-smtp.sql)
-- This is idempotent - it will fail silently if the column already exists
-- Run the ALTER TABLE statement from email-marketing-multi-smtp.sql if this column is missing:
-- ALTER TABLE `email_logs` ADD COLUMN `smtp_config_id` int(11) DEFAULT NULL COMMENT 'SMTP config used for sending this email' AFTER `recipient_id`;

-- Add time_taken_ms column to email_logs table for observability
-- This tracks how long each email takes to send in milliseconds
-- Note: Column position (AFTER error_message) places it before sent_at
ALTER TABLE `email_logs` 
ADD COLUMN `time_taken_ms` decimal(10,2) DEFAULT NULL 
COMMENT 'Time taken to send email in milliseconds' 
AFTER `error_message`;

-- Note: The following settings are managed via the Settings page (/email_marketing/settings)
-- and stored in the email_settings table:
-- 
-- email_domain_filter: Controls domain filtering behavior
--   - 'gmail_only': Only allow @gmail.com addresses (default)
--   - 'custom': Allow custom comma-separated domains
--   - 'disabled': Allow all email domains
--
-- email_allowed_domains: Comma-separated list of allowed domains
--   - Example: 'gmail.com,yahoo.com,outlook.com'
--   - Only used when email_domain_filter is 'custom'
--
-- enable_open_tracking: Enable/disable email open tracking (1 or 0)
--
-- Cron metrics (automatically updated by the cron job):
-- last_cron_run: Timestamp of last cron execution
-- last_cron_duration_sec: Duration of last cron run in seconds
-- last_cron_sent: Number of emails sent in last cron run
-- last_cron_failed: Number of failed emails in last cron run
-- last_cron_rejected_domain: Number of emails rejected due to domain filter
