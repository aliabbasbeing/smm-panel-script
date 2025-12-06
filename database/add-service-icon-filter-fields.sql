-- Add new fields to services table for icon and filter management
-- This migration adds fields to make Order/Add page fully dynamic

-- Add icon field to store Font Awesome icon class for each service
ALTER TABLE `services` ADD COLUMN `icon` VARCHAR(255) DEFAULT NULL COMMENT 'Font Awesome icon class or image URL for service' AFTER `desc`;

-- Add filter_enabled field to control which services appear in filter buttons
ALTER TABLE `services` ADD COLUMN `filter_enabled` TINYINT(1) DEFAULT 1 COMMENT 'Whether this service appears in platform filters' AFTER `icon`;

-- Add filter_name field to customize filter button label
ALTER TABLE `services` ADD COLUMN `filter_name` VARCHAR(100) DEFAULT NULL COMMENT 'Custom name for filter button' AFTER `filter_enabled`;

-- Add filter_order field to control the order of filter buttons
ALTER TABLE `services` ADD COLUMN `filter_order` INT(11) DEFAULT 999 COMMENT 'Order of filter button (lower = first)' AFTER `filter_name`;

-- Add filter_category field to group services under a specific filter
ALTER TABLE `services` ADD COLUMN `filter_category` VARCHAR(100) DEFAULT 'other' COMMENT 'Platform category for filtering (tiktok, youtube, instagram, etc.)' AFTER `filter_order`;

-- Update existing services to have default filter categories based on their names
UPDATE `services` SET `filter_category` = 'tiktok' WHERE LOWER(`name`) LIKE '%tiktok%';
UPDATE `services` SET `filter_category` = 'youtube' WHERE LOWER(`name`) LIKE '%youtube%' OR LOWER(`name`) LIKE '%yt %';
UPDATE `services` SET `filter_category` = 'instagram' WHERE LOWER(`name`) LIKE '%instagram%' OR LOWER(`name`) LIKE '%insta%';
UPDATE `services` SET `filter_category` = 'telegram' WHERE LOWER(`name`) LIKE '%telegram%' OR LOWER(`name`) LIKE '%tg %';
UPDATE `services` SET `filter_category` = 'facebook' WHERE LOWER(`name`) LIKE '%facebook%' OR LOWER(`name`) LIKE '%fb %';
UPDATE `services` SET `filter_category` = 'twitter' WHERE LOWER(`name`) LIKE '%twitter%' OR LOWER(`name`) REGEXP '[[:<:]]x[[:>:]]';
UPDATE `services` SET `filter_category` = 'whatsapp' WHERE LOWER(`name`) LIKE '%whatsapp%' OR LOWER(`name`) LIKE '%wa %';
UPDATE `services` SET `filter_category` = 'snapchat' WHERE LOWER(`name`) LIKE '%snapchat%' OR LOWER(`name`) LIKE '%snap%';
UPDATE `services` SET `filter_category` = 'linkedin' WHERE LOWER(`name`) LIKE '%linkedin%';

-- Auto-populate icon field based on filter_category
UPDATE `services` SET `icon` = 'fa-brands fa-tiktok' WHERE `filter_category` = 'tiktok';
UPDATE `services` SET `icon` = 'fa-brands fa-youtube' WHERE `filter_category` = 'youtube';
UPDATE `services` SET `icon` = 'fa-brands fa-instagram' WHERE `filter_category` = 'instagram';
UPDATE `services` SET `icon` = 'fa-brands fa-telegram' WHERE `filter_category` = 'telegram';
UPDATE `services` SET `icon` = 'fa-brands fa-facebook' WHERE `filter_category` = 'facebook';
UPDATE `services` SET `icon` = 'fa-brands fa-x-twitter' WHERE `filter_category` = 'twitter';
UPDATE `services` SET `icon` = 'fa-brands fa-whatsapp' WHERE `filter_category` = 'whatsapp';
UPDATE `services` SET `icon` = 'fa-brands fa-snapchat' WHERE `filter_category` = 'snapchat';
UPDATE `services` SET `icon` = 'fa-brands fa-linkedin' WHERE `filter_category` = 'linkedin';
UPDATE `services` SET `icon` = 'fas fa-plus' WHERE `filter_category` = 'other';
