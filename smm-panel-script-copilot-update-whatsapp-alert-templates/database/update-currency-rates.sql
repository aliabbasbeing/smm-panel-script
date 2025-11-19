-- Update script to fix incorrect exchange rates
-- Run this if you already have the currencies table installed

-- Update USD rate: 1 USD = 282.63 PKR, so 1 PKR = 0.00353876 USD
UPDATE `currencies` SET `exchange_rate` = 0.00353876 WHERE `code` = 'USD';

-- Update EUR rate (approximate)
UPDATE `currencies` SET `exchange_rate` = 0.00325000 WHERE `code` = 'EUR';

-- Update GBP rate (approximate)
UPDATE `currencies` SET `exchange_rate` = 0.00280000 WHERE `code` = 'GBP';

-- Update INR rate (approximate)
UPDATE `currencies` SET `exchange_rate` = 0.29500000 WHERE `code` = 'INR';

-- Update AUD rate (approximate)
UPDATE `currencies` SET `exchange_rate` = 0.00540000 WHERE `code` = 'AUD';

-- Update CAD rate (approximate)
UPDATE `currencies` SET `exchange_rate` = 0.00480000 WHERE `code` = 'CAD';

-- Note: After running this script, use the "Fetch Latest Rates" button 
-- in the Currency Management page to get real-time exchange rates.
