-- Cron Logs Table
-- This table stores logs for all cron job executions

CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(255) NOT NULL COMMENT 'URL or identifier of the cron job',
  `executed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the cron was executed',
  `status` enum('success','failed','rate_limited') NOT NULL DEFAULT 'success' COMMENT 'Execution status',
  `response_code` int(11) DEFAULT NULL COMMENT 'HTTP response code if applicable',
  `response_message` text DEFAULT NULL COMMENT 'Output or error message',
  `execution_time` decimal(10,3) DEFAULT NULL COMMENT 'Total execution time in seconds',
  PRIMARY KEY (`id`),
  KEY `idx_cron_name` (`cron_name`),
  KEY `idx_executed_at` (`executed_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Logs for cron job executions';

-- Add indices for better query performance
ALTER TABLE `cron_logs` 
  ADD INDEX `idx_cron_status` (`cron_name`, `status`),
  ADD INDEX `idx_date_status` (`executed_at`, `status`);
