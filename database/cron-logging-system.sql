-- Cron Logging System Database Migration
-- This creates the cron_logs table for tracking all cron executions

CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(255) NOT NULL COMMENT 'Name/URL of the cron job',
  `executed_at` datetime NOT NULL COMMENT 'Timestamp when cron was executed',
  `status` enum('success','failed','rate_limited','info') NOT NULL DEFAULT 'success' COMMENT 'Execution status',
  `response_code` int(11) DEFAULT NULL COMMENT 'HTTP status code',
  `response_message` text DEFAULT NULL COMMENT 'Error or output message',
  `execution_time` decimal(10,4) DEFAULT NULL COMMENT 'Time taken in seconds',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cron_name` (`cron_name`),
  KEY `idx_executed_at` (`executed_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create settings table entry for cron notification email (if general_options table exists)
-- If using a different settings mechanism, adjust accordingly
INSERT IGNORE INTO `general_options` (`name`, `value`, `created`) 
VALUES ('cron_notification_email', '', NOW()),
       ('cron_enable_notifications', '0', NOW()),
       ('cron_log_retention_days', '30', NOW());
