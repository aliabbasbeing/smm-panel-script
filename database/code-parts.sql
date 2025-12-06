-- Code Parts Database Schema
-- This schema adds a table for storing custom HTML code blocks for different pages
-- Run this migration to enable the Code Parts feature

-- =====================================================
-- TABLE: code_parts
-- Purpose: Store custom HTML code blocks for various pages
-- =====================================================
CREATE TABLE IF NOT EXISTS `code_parts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `page_key` VARCHAR(100) NOT NULL COMMENT 'Unique identifier for the page',
  `page_name` VARCHAR(255) NOT NULL COMMENT 'Human-readable page name',
  `content` LONGTEXT DEFAULT NULL COMMENT 'HTML content for the page',
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=enabled, 0=disabled',
  `device_visibility` VARCHAR(20) NOT NULL DEFAULT 'both' COMMENT 'Device visibility: mobile, desktop, or both',
  `display_position` VARCHAR(20) NOT NULL DEFAULT 'top' COMMENT 'Display position: top or bottom',
  `show_on_mobile` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=show on mobile, 0=hide on mobile',
  `show_on_desktop` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=show on desktop, 0=hide on desktop',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert default code parts for all pages
-- =====================================================

INSERT INTO `code_parts` (`page_key`, `page_name`, `content`, `status`) VALUES
('dashboard', 'Dashboard Page', '', 1),
('new_order', 'New Order Page', '', 1),
('orders', 'Order Logs Page', '', 1),
('services', 'Services Page', '', 1),
('add_funds', 'Add Funds Page', '', 1),
('api', 'API Page', '', 1),
('tickets', 'Tickets Page', '', 1),
('child_panel', 'Child Panel Page', '', 1),
('transactions', 'Transactions Page', '', 1),
('signin', 'Sign In Page', '', 1),
('signup', 'Sign Up Page', '', 1)
ON DUPLICATE KEY UPDATE `page_name` = VALUES(`page_name`);

-- =====================================================
-- MIGRATION: Add advanced settings columns (if upgrading)
-- For existing installations, run these ALTER TABLE commands
-- =====================================================

-- Add device visibility and position columns if they don't exist
ALTER TABLE `code_parts` 
ADD COLUMN IF NOT EXISTS `device_visibility` VARCHAR(20) NOT NULL DEFAULT 'both' COMMENT 'Device visibility: mobile, desktop, or both' AFTER `status`,
ADD COLUMN IF NOT EXISTS `display_position` VARCHAR(20) NOT NULL DEFAULT 'top' COMMENT 'Display position: top or bottom' AFTER `device_visibility`,
ADD COLUMN IF NOT EXISTS `show_on_mobile` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=show on mobile, 0=hide on mobile' AFTER `display_position`,
ADD COLUMN IF NOT EXISTS `show_on_desktop` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=show on desktop, 0=hide on desktop' AFTER `show_on_mobile`;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
