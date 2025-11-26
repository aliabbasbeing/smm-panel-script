-- Email Marketing Management System Database Schema
-- This schema adds complete email marketing functionality to the SMM Panel

-- =====================================================
-- TABLE: email_smtp_configs
-- Purpose: Store multiple SMTP configuration profiles
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_smtp_configs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `host` VARCHAR(255) NOT NULL,
  `port` INT(11) NOT NULL DEFAULT 587,
  `username` VARCHAR(255) NOT NULL,
  `password` TEXT NOT NULL,
  `encryption` ENUM('none', 'ssl', 'tls') NOT NULL DEFAULT 'tls',
  `from_name` VARCHAR(255) NOT NULL,
  `from_email` VARCHAR(255) NOT NULL,
  `reply_to` VARCHAR(255) DEFAULT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: email_templates
-- Purpose: Store reusable email templates
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(500) NOT NULL,
  `body` LONGTEXT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: email_campaigns
-- Purpose: Manage email marketing campaigns
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_campaigns` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `template_id` INT(11) NOT NULL,
  `smtp_config_id` INT(11) NOT NULL,
  `smtp_config_ids` TEXT DEFAULT NULL COMMENT 'JSON array of multiple SMTP config IDs for rotation',
  `smtp_rotation_index` INT(11) NOT NULL DEFAULT 0 COMMENT 'Current index for round-robin SMTP rotation',
  `status` ENUM('pending', 'running', 'paused', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `total_emails` INT(11) NOT NULL DEFAULT 0,
  `sent_emails` INT(11) NOT NULL DEFAULT 0,
  `failed_emails` INT(11) NOT NULL DEFAULT 0,
  `opened_emails` INT(11) NOT NULL DEFAULT 0,
  `bounced_emails` INT(11) NOT NULL DEFAULT 0,
  `sending_limit_hourly` INT(11) DEFAULT NULL,
  `sending_limit_daily` INT(11) DEFAULT NULL,
  `last_sent_at` DATETIME DEFAULT NULL,
  `started_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `template_id` (`template_id`),
  KEY `smtp_config_id` (`smtp_config_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- UPGRADE NOTES: For existing installations, uncomment and run the following ALTER statements
-- These are only needed if you already have the email_campaigns table without the new columns
-- =====================================================
-- ALTER TABLE `email_campaigns` ADD COLUMN `smtp_config_ids` TEXT DEFAULT NULL COMMENT 'JSON array of multiple SMTP config IDs for rotation' AFTER `smtp_config_id`;
-- ALTER TABLE `email_campaigns` ADD COLUMN `smtp_rotation_index` INT(11) NOT NULL DEFAULT 0 COMMENT 'Current index for round-robin SMTP rotation' AFTER `smtp_config_ids`;

-- =====================================================
-- TABLE: email_recipients
-- Purpose: Store campaign recipients
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_recipients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `campaign_id` INT(11) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  `user_id` INT(11) DEFAULT NULL COMMENT 'Reference to general_users if imported from DB',
  `custom_data` TEXT DEFAULT NULL COMMENT 'JSON data for template variables',
  `status` ENUM('pending', 'sent', 'failed', 'opened', 'bounced') NOT NULL DEFAULT 'pending',
  `sent_at` DATETIME DEFAULT NULL,
  `opened_at` DATETIME DEFAULT NULL,
  `tracking_token` VARCHAR(64) DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `tracking_token` (`tracking_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: email_logs
-- Purpose: Detailed logging of all email activities
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `campaign_id` INT(11) NOT NULL,
  `recipient_id` INT(11) NOT NULL,
  `smtp_config_id` INT(11) DEFAULT NULL COMMENT 'SMTP configuration used for sending',
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(500) NOT NULL,
  `status` ENUM('queued', 'sent', 'failed', 'opened', 'bounced') NOT NULL DEFAULT 'queued',
  `error_message` TEXT DEFAULT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  `opened_at` DATETIME DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `smtp_config_id` (`smtp_config_id`),
  KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- UPGRADE NOTES: For existing installations, uncomment and run the following ALTER statements
-- These are only needed if you already have the email_logs table without the smtp_config_id column
-- =====================================================
-- ALTER TABLE `email_logs` ADD COLUMN `smtp_config_id` INT(11) DEFAULT NULL COMMENT 'SMTP configuration used for sending' AFTER `recipient_id`;
-- ALTER TABLE `email_logs` ADD INDEX `smtp_config_id` (`smtp_config_id`);

-- =====================================================
-- TABLE: email_settings
-- Purpose: Global email marketing settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert default settings
-- =====================================================
INSERT INTO `email_settings` (`setting_key`, `setting_value`) VALUES
('default_hourly_limit', '100'),
('default_daily_limit', '1000'),
('enable_open_tracking', '1'),
('enable_bounce_tracking', '1'),
('retry_failed_attempts', '3'),
('retry_delay_minutes', '30')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- =====================================================
-- Insert sample SMTP configuration (must be updated)
-- =====================================================
INSERT INTO `email_smtp_configs` 
(`ids`, `name`, `host`, `port`, `username`, `password`, `encryption`, `from_name`, `from_email`, `is_default`, `status`) 
VALUES
(MD5(CONCAT('smtp_default', NOW())), 'Default SMTP', 'smtp.example.com', 587, 'user@example.com', '', 'tls', 'SMM Panel', 'noreply@example.com', 1, 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =====================================================
-- Insert sample email template
-- =====================================================
INSERT INTO `email_templates` 
(`ids`, `name`, `subject`, `body`, `description`, `status`) 
VALUES
(MD5(CONCAT('template_welcome', NOW())), 'Welcome Email', 'Welcome to {site_name}!', 
'<html><body><h1>Hello {username}!</h1><p>Welcome to {site_name}. Your current balance is {balance}.</p><p>Email: {email}</p><p>Thank you for joining us!</p></body></html>',
'Default welcome email template with user variables',
1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =====================================================
-- Add foreign key constraints (optional, for data integrity)
-- =====================================================
-- Note: Uncomment these if you want strict referential integrity
-- ALTER TABLE `email_campaigns` 
--   ADD CONSTRAINT `fk_campaign_template` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`id`) ON DELETE RESTRICT,
--   ADD CONSTRAINT `fk_campaign_smtp` FOREIGN KEY (`smtp_config_id`) REFERENCES `email_smtp_configs` (`id`) ON DELETE RESTRICT;

-- ALTER TABLE `email_recipients` 
--   ADD CONSTRAINT `fk_recipient_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `email_logs` 
--   ADD CONSTRAINT `fk_log_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `fk_log_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `email_recipients` (`id`) ON DELETE CASCADE;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
