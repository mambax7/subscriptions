-- Subscriptions Database Updates v1.1.0
-- Run this manually or via a migration script after upgrading the module files.
--
-- Changes:
--   1. Add cancel_at_period_end column to subscriptions_subscriptions
--   2. Add recurring_months column to subscriptions_coupons

-- ============================================================
-- 1. cancel_at_period_end: schedule cancellation at period end
--    0 = no scheduled cancel (default)
--    1 = cancel when current_period_end passes (cron job handles it)
-- ============================================================
ALTER TABLE subscriptions_subscriptions
    ADD COLUMN cancel_at_period_end TINYINT(1) NOT NULL DEFAULT '0'
    AFTER cancel_reason;

-- ============================================================
-- 2. recurring_months: how many billing periods the coupon discount applies
--    0  = first payment only (one-time discount at checkout)
--    N  = applies to first N billing periods (counted from subscription creation)
--   -1  = applies to all renewals forever
-- ============================================================
ALTER TABLE subscriptions_coupons
    ADD COLUMN recurring_months INT(5) NOT NULL DEFAULT '0'
    AFTER max_uses_per_user;
