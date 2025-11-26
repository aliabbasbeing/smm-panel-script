-- WhatsApp Notification Settings Database Schema
-- This schema adds notification settings for various alert types

-- =====================================================
-- TABLE: whatsapp_notifications
-- Purpose: Store WhatsApp notification templates and settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `event_type` VARCHAR(100) NOT NULL,
  `event_name` VARCHAR(255) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=enabled, 0=disabled',
  `template` TEXT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `variables` TEXT DEFAULT NULL COMMENT 'JSON array of available variables',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert default notification templates
-- =====================================================

-- 1. Order Placed (already working, adding for consistency)
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('order_placed', 'Order Placed', 1, 
'*🔔 New Order Received*\n\n🔢 *Order ID:* #{order_id}\n💰 *Total Charge:* {currency_symbol}{total_charge}\n📦 *Quantity:* {quantity}\n🔗 *Link:* {link}\n📧 *User Email:* {user_email}\n\nPlease review the order details.',
'Admin notification when a new order is placed',
'["order_id", "total_charge", "quantity", "link", "user_email", "currency_symbol", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 2. Welcome Message
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('welcome_message', 'Welcome Message', 1,
'*Welcome to {website_name}!* 👋\n\nHello *{username}*,\n\nThank you for joining us! Your account has been successfully created.\n\n📧 *Email:* {email}\n💰 *Balance:* {currency_symbol}{balance}\n\nStart placing your orders now and grow your social media presence!\n\nBest regards,\n{website_name} Team',
'Welcome message sent to new users after registration',
'["username", "email", "balance", "currency_symbol", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 3. Order Cancelled
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('order_cancelled', 'Order Cancelled', 1,
'*⚠️ Order Cancelled*\n\n🔢 *Order ID:* #{order_id}\n💰 *Refund Amount:* {currency_symbol}{refund_amount}\n📦 *Service:* {service_name}\n\nYour order has been cancelled and the amount has been refunded to your account.\n\n💵 *New Balance:* {currency_symbol}{new_balance}\n\nIf you have any questions, please contact our support team.\n\nThank you,\n{website_name}',
'Notification when an order is cancelled',
'["order_id", "refund_amount", "service_name", "new_balance", "currency_symbol", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 4. Order Partial
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('order_partial', 'Order Partial', 1,
'*📊 Order Partially Completed*\n\n🔢 *Order ID:* #{order_id}\n📦 *Service:* {service_name}\n✅ *Delivered:* {delivered_quantity}\n📋 *Ordered:* {ordered_quantity}\n💰 *Refund:* {currency_symbol}{refund_amount}\n\nYour order has been partially completed. The remaining amount has been refunded to your account.\n\n💵 *New Balance:* {currency_symbol}{new_balance}\n\nThank you,\n{website_name}',
'Notification when an order is partially completed',
'["order_id", "service_name", "delivered_quantity", "ordered_quantity", "refund_amount", "new_balance", "currency_symbol", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 5. API Key Changed
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('api_key_changed', 'API Key Changed', 1,
'*🔐 API Key Changed*\n\nHello *{username}*,\n\nYour API key has been successfully changed.\n\n🔑 *New API Key:* `{api_key}`\n\n⏰ *Changed At:* {changed_at}\n🌐 *IP Address:* {ip_address}\n\nIf you did not make this change, please contact our support team immediately.\n\nThank you,\n{website_name}',
'Notification when user API key is changed',
'["username", "api_key", "changed_at", "ip_address", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 6. Support Ticket
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('support_ticket', 'Support Ticket', 1,
'*🎫 New Support Ticket*\n\n🔢 *Ticket ID:* #{ticket_id}\n📧 *User Email:* {user_email}\n📝 *Subject:* {subject}\n\n💬 *Message:*\n{message}\n\nPlease respond to this ticket as soon as possible.\n\n{website_name}',
'Admin notification when a new support ticket is created',
'["ticket_id", "user_email", "subject", "message", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 7. Verification OTP
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('verification_otp', 'Verification OTP', 1,
'*🔐 Verification Code*\n\nHello *{username}*,\n\nYour verification code is:\n\n*{otp_code}*\n\nThis code will expire in {expiry_minutes} minutes.\n\n⚠️ Do not share this code with anyone.\n\nThank you,\n{website_name}',
'OTP verification code sent to users',
'["username", "otp_code", "expiry_minutes", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 8. Reset Password
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('reset_password', 'Reset Password', 1,
'*🔑 Password Reset Request*\n\nHello *{username}*,\n\nWe received a request to reset your password.\n\n🔗 *Reset Link:*\n{reset_link}\n\n⏰ This link will expire in {expiry_minutes} minutes.\n\nIf you did not request this, please ignore this message and your password will remain unchanged.\n\nThank you,\n{website_name}',
'Password reset link sent to users',
'["username", "reset_link", "expiry_minutes", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 9. Order Completed (for cron status updates)
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('order_completed', 'Order Completed', 1,
'*✅ Order Completed*\n\nHello *{username}*,\n\nGreat news! Your order has been completed successfully.\n\n🔢 *Order ID:* #{order_id}\n📦 *Service:* {service_name}\n📋 *Quantity:* {quantity}\n🔗 *Link:* {link}\n💰 *Charge:* {currency_symbol}{charge}\n\n📊 *Status:* {old_status} → {new_status}\n\nThank you for using our service!\n\n{website_name}',
'Notification when order status changes to completed',
'["order_id", "service_name", "quantity", "link", "charge", "old_status", "new_status", "username", "currency_symbol", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- 10. Order Refunded (for cron status updates)
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `status`, `template`, `description`, `variables`) VALUES
('order_refunded', 'Order Refunded', 1,
'*💰 Order Refunded*\n\nHello *{username}*,\n\nYour order has been refunded.\n\n🔢 *Order ID:* #{order_id}\n📦 *Service:* {service_name}\n📋 *Quantity:* {quantity}\n🔗 *Link:* {link}\n💵 *Refund Amount:* {currency_symbol}{charge}\n\n📊 *Status:* {old_status} → {new_status}\n\nThe amount has been credited back to your account.\n\nThank you,\n{website_name}',
'Notification when order status changes to refunded',
'["order_id", "service_name", "quantity", "link", "charge", "old_status", "new_status", "username", "currency_symbol", "website_name"]')
ON DUPLICATE KEY UPDATE `template` = VALUES(`template`);

-- =====================================================
-- END OF SCHEMA
-- =====================================================
