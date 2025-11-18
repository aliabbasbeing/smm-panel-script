-- Cron Logs Table
-- This table stores execution logs for all cron jobs in the SMM panel
-- Date: 2025-11-18

CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(255) NOT NULL COMMENT 'Cron URL or identifier',
  `executed_at` datetime NOT NULL COMMENT 'When the cron was executed',
  `status` enum('Success','Failed') NOT NULL DEFAULT 'Success',
  `response_code` int(11) DEFAULT NULL COMMENT 'HTTP response code',
  `response_message` text DEFAULT NULL COMMENT 'Output or error message',
  `execution_time` decimal(10,3) DEFAULT NULL COMMENT 'Execution time in seconds',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cron_name` (`cron_name`),
  KEY `idx_executed_at` (`executed_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs for all cron job executions';
