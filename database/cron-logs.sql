-- Cron Logs Table
-- This table stores execution logs for all cron jobs

CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(255) NOT NULL COMMENT 'Name or URL of the cron job',
  `executed_at` datetime NOT NULL COMMENT 'Timestamp of execution',
  `status` enum('success','failed','running') NOT NULL DEFAULT 'running' COMMENT 'Execution status',
  `response_code` int(11) DEFAULT NULL COMMENT 'HTTP status code or custom code',
  `response_message` text DEFAULT NULL COMMENT 'Error message or output',
  `execution_time` decimal(10,4) DEFAULT NULL COMMENT 'Time taken to complete in seconds',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cron_name` (`cron_name`),
  KEY `idx_status` (`status`),
  KEY `idx_executed_at` (`executed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cron Settings Table
-- Store settings for cron notifications and management
CREATE TABLE IF NOT EXISTS `cron_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `changed` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `cron_settings` (`setting_key`, `setting_value`) VALUES
('enable_email_notifications', '0'),
('notification_email', ''),
('log_retention_days', '30');
