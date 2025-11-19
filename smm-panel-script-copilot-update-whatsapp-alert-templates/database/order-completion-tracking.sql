-- ============================================================================
-- SMM Panel - Order Completion Tracking & Last 10 Orders Average Feature
-- ============================================================================
-- This migration adds tracking for order completion times and average 
-- completion times based on the last 10 completed orders per service
-- ============================================================================

-- Add completed_at column to orders table
-- Stores the datetime when an order status was changed to 'completed'
ALTER TABLE `orders`
ADD COLUMN `completed_at` DATETIME DEFAULT NULL AFTER `created`;

-- Add last_10_avg_time column to orders table
-- Stores the average completion time of the last 10 completed orders 
-- for this order's service (in seconds)
ALTER TABLE `orders`
ADD COLUMN `last_10_avg_time` INT(11) DEFAULT NULL AFTER `completed_at`;

-- Add avg_completion_time column to services table
-- Stores the average completion time for each service based on 
-- the last 10 completed orders (in seconds)
ALTER TABLE `services`
ADD COLUMN `avg_completion_time` INT(11) DEFAULT NULL AFTER `created`;

-- ============================================================================
-- Notes:
-- 1. completed_at is set automatically when order status changes to 'completed'
-- 2. avg_completion_time is calculated and updated by cron job
-- 3. last_10_avg_time is updated when order is completed
-- 4. Times are stored in seconds for easy calculation
-- ============================================================================
