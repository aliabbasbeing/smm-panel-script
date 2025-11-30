-- =====================================================
-- Email Marketing Observability & Multi-SMTP Update
-- =====================================================
-- This script adds:
-- 1. Multi-SMTP rotation support (columns in email_campaigns)
-- 2. SMTP tracking in email_logs  
-- 3. Time tracking for observability
--
-- Run this script after the initial email-marketing.sql setup
-- =====================================================

-- =====================================================
-- STEP 1: Add multi-SMTP rotation columns to email_campaigns
-- (from email-marketing-multi-smtp.sql)
-- =====================================================

-- Add smtp_config_ids column for storing multiple SMTP IDs as JSON array
-- This column stores the list of SMTP servers to rotate through
ALTER TABLE `email_campaigns` 
ADD COLUMN IF NOT EXISTS `smtp_config_ids` text DEFAULT NULL 
COMMENT 'JSON array of SMTP config IDs for rotation' 
AFTER `smtp_config_id`;

-- Add smtp_rotation_index to track current position in round-robin rotation
ALTER TABLE `email_campaigns` 
ADD COLUMN IF NOT EXISTS `smtp_rotation_index` int(11) NOT NULL DEFAULT 0 
COMMENT 'Current index for round-robin rotation' 
AFTER `smtp_config_ids`;

-- =====================================================
-- STEP 2: Add smtp_config_id column to email_logs
-- This tracks which SMTP was used to send each email
-- =====================================================

ALTER TABLE `email_logs` 
ADD COLUMN IF NOT EXISTS `smtp_config_id` int(11) DEFAULT NULL 
COMMENT 'SMTP config used for sending this email' 
AFTER `recipient_id`;

-- =====================================================
-- STEP 3: Add time_taken_ms column to email_logs
-- This tracks how long each email takes to send
-- =====================================================

ALTER TABLE `email_logs` 
ADD COLUMN IF NOT EXISTS `time_taken_ms` decimal(10,2) DEFAULT NULL 
COMMENT 'Time taken to send email in milliseconds' 
AFTER `error_message`;

-- =====================================================
-- NOTES
-- =====================================================
-- 
-- After running this script:
-- 1. Existing campaigns with single SMTP will continue to work (backward compatible)
-- 2. New campaigns can select multiple SMTPs for rotation
-- 3. Each email log will record which SMTP was used and how long it took
--
-- Settings (managed via /email_marketing/settings):
-- - email_domain_filter: 'gmail_only', 'custom', or 'disabled'
-- - email_allowed_domains: Comma-separated domains (e.g., 'gmail.com,yahoo.com')
-- - enable_open_tracking: 1 or 0
--
-- Cron metrics (auto-updated):
-- - last_cron_run, last_cron_duration_sec
-- - last_cron_sent, last_cron_failed, last_cron_rejected_domain
