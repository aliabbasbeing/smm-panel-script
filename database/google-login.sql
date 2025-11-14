-- Add google_id field to general_users table for Google login integration
ALTER TABLE `general_users` ADD COLUMN `google_id` VARCHAR(255) DEFAULT NULL AFTER `login_type`;

-- Add index for faster lookups
ALTER TABLE `general_users` ADD INDEX `idx_google_id` (`google_id`);

-- Insert Google OAuth settings into general_options table
INSERT INTO `general_options` (`name`, `value`) VALUES ('enable_google_login', '0');
INSERT INTO `general_options` (`name`, `value`) VALUES ('google_client_id', '');
INSERT INTO `general_options` (`name`, `value`) VALUES ('google_client_secret', '');
