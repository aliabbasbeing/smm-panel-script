-- Multi-currency support table
CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(18,8) NOT NULL DEFAULT '1.00000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default currencies with corrected exchange rates
-- PKR is the default base currency, all rates are relative to PKR
-- Exchange rates updated as of 2024 (approximate values)
-- Note: 1 USD = 282.63 PKR, so 1 PKR = 1/282.63 = 0.00353876 USD
INSERT INTO `currencies` (`code`, `name`, `symbol`, `exchange_rate`, `is_default`, `status`) VALUES
('PKR', 'Pakistani Rupee', 'Rs', 1.00000000, 1, 1),
('USD', 'US Dollar', '$', 0.00353876, 0, 1),
('EUR', 'Euro', '€', 0.00325000, 0, 1),
('GBP', 'British Pound', '£', 0.00280000, 0, 1),
('INR', 'Indian Rupee', '₹', 0.29500000, 0, 1),
('AUD', 'Australian Dollar', 'A$', 0.00540000, 0, 1),
('CAD', 'Canadian Dollar', 'C$', 0.00480000, 0, 1);
