-- Verification queries for multi-currency support
-- Run these queries to verify the installation

-- 1. Check if currencies table exists
SHOW TABLES LIKE 'currencies';

-- 2. View table structure
DESCRIBE currencies;

-- 3. Check default currencies
SELECT code, name, symbol, exchange_rate, is_default, status 
FROM currencies 
ORDER BY is_default DESC, name ASC;

-- 4. Count active currencies
SELECT COUNT(*) as active_currencies 
FROM currencies 
WHERE status = 1;

-- 5. Check default currency
SELECT code, name, symbol 
FROM currencies 
WHERE is_default = 1;

-- 6. Show all exchange rates relative to default
SELECT 
    c.code,
    c.name,
    c.exchange_rate,
    CASE WHEN c.is_default = 1 THEN 'Default' ELSE 'Secondary' END as currency_type
FROM currencies c
WHERE c.status = 1
ORDER BY c.is_default DESC, c.code ASC;
