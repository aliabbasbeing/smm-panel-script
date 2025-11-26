-- =====================================================
-- Email Marketing Multi-SMTP Rotation Feature
-- Run this script to add multi-SMTP rotation support
-- =====================================================

-- Add columns for multi-SMTP rotation to email_campaigns table
ALTER TABLE `email_campaigns` 
ADD COLUMN `smtp_config_ids` text DEFAULT NULL COMMENT 'JSON array of SMTP config IDs for rotation' AFTER `smtp_config_id`,
ADD COLUMN `smtp_rotation_index` int(11) NOT NULL DEFAULT 0 COMMENT 'Current index for round-robin rotation' AFTER `smtp_config_ids`;

-- Add column to track which SMTP was used for each email log
ALTER TABLE `email_logs` 
ADD COLUMN `smtp_config_id` int(11) DEFAULT NULL COMMENT 'SMTP config used for sending this email' AFTER `recipient_id`;
