-- Update USD exchange rate to correct value
-- 1 USD = 282.63 PKR means 1 PKR = 1/282.63 USD = 0.00353941 USD
UPDATE `currencies` 
SET `exchange_rate` = 0.00353941 
WHERE `code` = 'USD';

-- Note: This updates the USD rate to match 1 USD = 282.63 PKR
-- After running this, PKR 916.65 will convert to USD 3.24 (916.65 × 0.00353941 = 3.24)
