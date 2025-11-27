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
-- END OF SCHEMA
-- =====================================================
