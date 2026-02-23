-- =============================================================================
-- DEPRECATED — seed.sql is no longer used.
-- =============================================================================
-- seed.php now generates randomized data via fakerphp/faker directly in PHP.
-- This file is kept as a reference only and is safe to delete.
-- Do NOT run this file manually — the token placeholders ({PREFIX}, {ADMIN_UID},
-- {NOW}) are no longer replaced by seed.php.
-- =============================================================================
--
-- Original header (for reference):
-- This file inserted representative demo data so developers and evaluators
-- could see every feature of the module in action. The seed runner (seed.php)
-- executed this file after replacing:
--   {PREFIX}    → the XOOPS database prefix  (e.g. x7e1_)
--   {ADMIN_UID} → UID of the first admin user (e.g. 1)
--   {NOW}       → current Unix timestamp
-- =============================================================================

-- ----------------------------------------------------------------------------
-- Wipe any previously seeded demo data  (idempotent re-run)
-- ----------------------------------------------------------------------------
DELETE FROM `{PREFIX}subscriptions_module_access_rules`;
DELETE FROM `{PREFIX}subscriptions_connected_modules`;
DELETE FROM `{PREFIX}subscriptions_usage_logs`;
DELETE FROM `{PREFIX}subscriptions_coupon_usage`;
DELETE FROM `{PREFIX}subscriptions_coupons`;
DELETE FROM `{PREFIX}subscriptions_webhooks`;
DELETE FROM `{PREFIX}subscriptions_gateway_configs`;
DELETE FROM `{PREFIX}subscriptions_refunds`;
DELETE FROM `{PREFIX}subscriptions_invoice_items`;
DELETE FROM `{PREFIX}subscriptions_invoices`;
DELETE FROM `{PREFIX}subscriptions_payments`;
DELETE FROM `{PREFIX}subscriptions_subscriptions`;
DELETE FROM `{PREFIX}subscriptions_plan_features`;
DELETE FROM `{PREFIX}subscriptions_plans`;

-- Reset auto-increment counters
ALTER TABLE `{PREFIX}subscriptions_plans`              AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_plan_features`      AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_subscriptions`      AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_payments`           AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_invoices`           AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_invoice_items`      AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_coupons`            AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_coupon_usage`       AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_connected_modules`  AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_module_access_rules` AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_usage_logs`         AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_webhooks`           AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_gateway_configs`    AUTO_INCREMENT = 1;
ALTER TABLE `{PREFIX}subscriptions_refunds`            AUTO_INCREMENT = 1;

-- ============================================================================
-- PLANS  (4 tiers demonstrating all billing models and features)
-- ============================================================================

-- Plan 1: Starter – flat monthly, 14-day trial, no setup fee
INSERT INTO `{PREFIX}subscriptions_plans`
  (plan_id, name, slug, description, price, currency, billing_cycle,
   trial_days, setup_fee, discount_annual, pricing_model,
   usage_unit, usage_price, is_featured, is_active, sort_order, max_users,
   created_at, updated_at)
VALUES
  (1, 'Starter', 'starter',
   'Perfect for individuals getting started. Full access to core features with a 14-day free trial.',
   9.99, 'USD', 'monthly',
   14, 0.00, 15.00, 'flat',
   '', 0.000000, 0, 1, 10, 0,
   {NOW}, {NOW});

-- Plan 2: Professional – flat monthly, featured, setup fee, bigger annual discount
INSERT INTO `{PREFIX}subscriptions_plans`
  (plan_id, name, slug, description, price, currency, billing_cycle,
   trial_days, setup_fee, discount_annual, pricing_model,
   usage_unit, usage_price, is_featured, is_active, sort_order, max_users,
   created_at, updated_at)
VALUES
  (2, 'Professional', 'professional',
   'Our most popular plan. Ideal for growing teams with advanced integrations, priority support, and a 20% annual saving.',
   29.99, 'USD', 'monthly',
   0, 0.00, 20.00, 'flat',
   '', 0.000000, 1, 1, 20, 0,
   {NOW}, {NOW});

-- Plan 3: Business – hybrid pricing (flat + per-API-call usage)
INSERT INTO `{PREFIX}subscriptions_plans`
  (plan_id, name, slug, description, price, currency, billing_cycle,
   trial_days, setup_fee, discount_annual, pricing_model,
   usage_unit, usage_price, is_featured, is_active, sort_order, max_users,
   created_at, updated_at)
VALUES
  (3, 'Business', 'business',
   'For power users and small businesses. Flat monthly base fee plus pay-as-you-go API calls billed at the end of each cycle.',
   79.99, 'USD', 'monthly',
   0, 49.99, 25.00, 'hybrid',
   'api_call', 0.005000, 0, 1, 30, 50,
   {NOW}, {NOW});

-- Plan 4: Enterprise – annual, one-time setup, limited seats, no trial
INSERT INTO `{PREFIX}subscriptions_plans`
  (plan_id, name, slug, description, price, currency, billing_cycle,
   trial_days, setup_fee, discount_annual, pricing_model,
   usage_unit, usage_price, is_featured, is_active, sort_order, max_users,
   created_at, updated_at)
VALUES
  (4, 'Enterprise', 'enterprise',
   'Custom annual licensing for large organisations. Includes dedicated account management, SLA guarantee, and unlimited API calls.',
   999.00, 'USD', 'annual',
   0, 499.00, 0.00, 'flat',
   '', 0.000000, 0, 1, 40, 500,
   {NOW}, {NOW});

-- ============================================================================
-- PLAN FEATURES
-- ============================================================================

-- Starter features
INSERT INTO `{PREFIX}subscriptions_plan_features` (plan_id, feature, sort_order) VALUES
  (1, 'Up to 3 projects',          0),
  (1, '5 GB storage',              1),
  (1, 'Email support (48h SLA)',   2),
  (1, 'API access (read-only)',    3),
  (1, '14-day free trial',         4);

-- Professional features
INSERT INTO `{PREFIX}subscriptions_plan_features` (plan_id, feature, sort_order) VALUES
  (2, 'Unlimited projects',              0),
  (2, '50 GB storage',                   1),
  (2, 'Priority email & chat support',   2),
  (2, 'Full API access',                 3),
  (2, 'Webhook integrations',            4),
  (2, 'Advanced analytics dashboard',    5),
  (2, '20% discount when billed annually',6);

-- Business features
INSERT INTO `{PREFIX}subscriptions_plan_features` (plan_id, feature, sort_order) VALUES
  (3, 'Everything in Professional',     0),
  (3, '200 GB storage',                  1),
  (3, 'Up to 50 team members',           2),
  (3, 'Pay-per-use API calls ($0.005)',   3),
  (3, 'Custom integrations',             4),
  (3, 'Monthly usage report',            5),
  (3, 'Phone support (business hours)',  6);

-- Enterprise features
INSERT INTO `{PREFIX}subscriptions_plan_features` (plan_id, feature, sort_order) VALUES
  (4, 'Everything in Business',           0),
  (4, 'Unlimited storage',                1),
  (4, 'Up to 500 users',                  2),
  (4, 'Dedicated account manager',        3),
  (4, '99.9% uptime SLA',                4),
  (4, 'On-premise deployment option',     5),
  (4, 'Custom billing & invoicing',       6),
  (4, '24/7 phone & on-site support',     7);

-- ============================================================================
-- SUBSCRIPTIONS  (shows all lifecycle states)
-- ============================================================================

-- Sub 1: Admin on Professional plan – ACTIVE (started 35 days ago, renews in 25 days)
INSERT INTO `{PREFIX}subscriptions_subscriptions`
  (sub_id, user_id, plan_id, status, billing_cycle,
   started_at, trial_ends_at,
   current_period_start, current_period_end,
   cancelled_at, cancel_reason,
   gateway, gateway_sub_id,
   coupon_id, discount_amount, amount_paid,
   auto_renew, created_at, updated_at)
VALUES
  (1, {ADMIN_UID}, 2, 'active', 'monthly',
   {NOW} - 2678400, 0,
   {NOW} - 518400, {NOW} + 2160000,
   0, '',
   'paypal', 'I-DEMO-PAYPAL-001',
   0, 0.00, 29.99,
   1, {NOW} - 2678400, {NOW});

-- Sub 2: Admin on Starter plan – TRIAL (started 3 days ago, trial ends in 11 days)
INSERT INTO `{PREFIX}subscriptions_subscriptions`
  (sub_id, user_id, plan_id, status, billing_cycle,
   started_at, trial_ends_at,
   current_period_start, current_period_end,
   cancelled_at, cancel_reason,
   gateway, gateway_sub_id,
   coupon_id, discount_amount, amount_paid,
   auto_renew, created_at, updated_at)
VALUES
  (2, {ADMIN_UID}, 1, 'trial', 'monthly',
   {NOW} - 259200, {NOW} + 950400,
   {NOW} - 259200, {NOW} + 950400,
   0, '',
   'manual', '',
   0, 0.00, 0.00,
   1, {NOW} - 259200, {NOW});

-- Sub 3: Admin – PAST_DUE (payment failed 5 days ago, period ended 5 days ago)
INSERT INTO `{PREFIX}subscriptions_subscriptions`
  (sub_id, user_id, plan_id, status, billing_cycle,
   started_at, trial_ends_at,
   current_period_start, current_period_end,
   cancelled_at, cancel_reason,
   gateway, gateway_sub_id,
   coupon_id, discount_amount, amount_paid,
   auto_renew, created_at, updated_at)
VALUES
  (3, {ADMIN_UID}, 3, 'past_due', 'monthly',
   {NOW} - 5184000, 0,
   {NOW} - 2592000, {NOW} - 432000,
   0, '',
   'stripe', 'sub_demo_STRIPE_003',
   0, 0.00, 79.99,
   1, {NOW} - 5184000, {NOW});

-- Sub 4: Admin – CANCELLED (cancelled 10 days ago, reason given)
INSERT INTO `{PREFIX}subscriptions_subscriptions`
  (sub_id, user_id, plan_id, status, billing_cycle,
   started_at, trial_ends_at,
   current_period_start, current_period_end,
   cancelled_at, cancel_reason,
   gateway, gateway_sub_id,
   coupon_id, discount_amount, amount_paid,
   auto_renew, created_at, updated_at)
VALUES
  (4, {ADMIN_UID}, 1, 'cancelled', 'monthly',
   {NOW} - 7776000, 0,
   {NOW} - 5184000, {NOW} - 2592000,
   {NOW} - 864000, 'Switching to a higher tier plan',
   'paypal', 'I-DEMO-PAYPAL-004',
   0, 0.00, 9.99,
   0, {NOW} - 7776000, {NOW});

-- Sub 5: Admin on Business plan – ACTIVE with coupon discount
INSERT INTO `{PREFIX}subscriptions_subscriptions`
  (sub_id, user_id, plan_id, status, billing_cycle,
   started_at, trial_ends_at,
   current_period_start, current_period_end,
   cancelled_at, cancel_reason,
   gateway, gateway_sub_id,
   coupon_id, discount_amount, amount_paid,
   auto_renew, created_at, updated_at)
VALUES
  (5, {ADMIN_UID}, 3, 'active', 'monthly',
   {NOW} - 1296000, 0,
   {NOW} - 1296000, {NOW} + 1296000,
   0, '',
   'stripe', 'sub_demo_STRIPE_005',
   1, 20.00, 59.99,
   1, {NOW} - 1296000, {NOW});

-- ============================================================================
-- INVOICES
-- ============================================================================

-- Invoice 1: Paid – Professional plan month 1 (sub 1, 35 days ago)
INSERT INTO `{PREFIX}subscriptions_invoices`
  (invoice_id, invoice_number, user_id, sub_id, status,
   subtotal, tax_rate, tax_amount, discount_amount, total, currency,
   due_date, paid_at, period_start, period_end, notes, created_at, updated_at)
VALUES
  (1, 'INV-20240101-0001', {ADMIN_UID}, 1, 'paid',
   29.99, 0.00, 0.00, 0.00, 29.99, 'USD',
   {NOW} - 2419200, {NOW} - 2678400,
   {NOW} - 2678400, {NOW} - 518400,
   '', {NOW} - 2678400, {NOW} - 2678400);

-- Invoice 2: Paid – Professional plan month 2 (sub 1, current period)
INSERT INTO `{PREFIX}subscriptions_invoices`
  (invoice_id, invoice_number, user_id, sub_id, status,
   subtotal, tax_rate, tax_amount, discount_amount, total, currency,
   due_date, paid_at, period_start, period_end, notes, created_at, updated_at)
VALUES
  (2, 'INV-20240201-0001', {ADMIN_UID}, 1, 'paid',
   29.99, 0.00, 0.00, 0.00, 29.99, 'USD',
   {NOW} - 259200, {NOW} - 518400,
   {NOW} - 518400, {NOW} + 2160000,
   '', {NOW} - 518400, {NOW} - 518400);

-- Invoice 3: Open – Business plan past-due (sub 3, payment failed)
INSERT INTO `{PREFIX}subscriptions_invoices`
  (invoice_id, invoice_number, user_id, sub_id, status,
   subtotal, tax_rate, tax_amount, discount_amount, total, currency,
   due_date, paid_at, period_start, period_end, notes, created_at, updated_at)
VALUES
  (3, 'INV-20240201-0002', {ADMIN_UID}, 3, 'open',
   79.99, 0.00, 0.00, 0.00, 79.99, 'USD',
   {NOW} - 86400, 0,
   {NOW} - 2592000, {NOW} - 432000,
   'Payment failed – awaiting retry', {NOW} - 2592000, {NOW} - 2592000);

-- Invoice 4: Paid – Business plan with coupon (sub 5, $20 discount)
INSERT INTO `{PREFIX}subscriptions_invoices`
  (invoice_id, invoice_number, user_id, sub_id, status,
   subtotal, tax_rate, tax_amount, discount_amount, total, currency,
   due_date, paid_at, period_start, period_end, notes, created_at, updated_at)
VALUES
  (4, 'INV-20240210-0001', {ADMIN_UID}, 5, 'paid',
   79.99, 0.00, 0.00, 20.00, 59.99, 'USD',
   {NOW} - 1036800, {NOW} - 1296000,
   {NOW} - 1296000, {NOW} + 1296000,
   'Coupon LAUNCH20 applied', {NOW} - 1296000, {NOW} - 1296000);

-- Invoice 5: Draft – Enterprise quote (no sub yet)
INSERT INTO `{PREFIX}subscriptions_invoices`
  (invoice_id, invoice_number, user_id, sub_id, status,
   subtotal, tax_rate, tax_amount, discount_amount, total, currency,
   due_date, paid_at, period_start, period_end, notes, created_at, updated_at)
VALUES
  (5, 'INV-20240215-0001', {ADMIN_UID}, 0, 'draft',
   999.00, 8.50, 84.92, 0.00, 1083.92, 'USD',
   {NOW} + 1209600, 0,
   {NOW}, {NOW} + 31536000,
   'Enterprise annual quote – pending contract signature', {NOW}, {NOW});

-- ============================================================================
-- INVOICE LINE ITEMS
-- ============================================================================

-- Invoice 1 items
INSERT INTO `{PREFIX}subscriptions_invoice_items` (invoice_id, description, quantity, unit_price, amount) VALUES
  (1, 'Professional Plan – Monthly Subscription', 1.00, 29.99, 29.99);

-- Invoice 2 items
INSERT INTO `{PREFIX}subscriptions_invoice_items` (invoice_id, description, quantity, unit_price, amount) VALUES
  (2, 'Professional Plan – Monthly Subscription', 1.00, 29.99, 29.99);

-- Invoice 3 items (failed Business payment)
INSERT INTO `{PREFIX}subscriptions_invoice_items` (invoice_id, description, quantity, unit_price, amount) VALUES
  (3, 'Business Plan – Monthly Subscription', 1.00, 79.99, 79.99);

-- Invoice 4 items (Business with coupon)
INSERT INTO `{PREFIX}subscriptions_invoice_items` (invoice_id, description, quantity, unit_price, amount) VALUES
  (4, 'Business Plan – Monthly Subscription',   1.00,  79.99,  79.99),
  (4, 'Discount – Coupon LAUNCH20',              1.00, -20.00, -20.00);

-- Invoice 5 items (Enterprise draft)
INSERT INTO `{PREFIX}subscriptions_invoice_items` (invoice_id, description, quantity, unit_price, amount) VALUES
  (5, 'Enterprise Plan – Annual License',    1.00,  999.00, 999.00),
  (5, 'One-Time Setup & Onboarding Fee',     1.00,  499.00, 499.00),
  (5, 'Tax (8.5%)',                          1.00,   84.92,  84.92);

-- ============================================================================
-- PAYMENTS
-- ============================================================================

-- Payment 1: completed – sub 1, invoice 1 (PayPal, 35 days ago)
INSERT INTO `{PREFIX}subscriptions_payments`
  (payment_id, sub_id, user_id, invoice_id, gateway, gateway_txn_id,
   amount, currency, status, payment_method, payment_data, ip_address,
   paid_at, created_at)
VALUES
  (1, 1, {ADMIN_UID}, 1, 'paypal', 'DEMO-TXN-PP-0001',
   29.99, 'USD', 'completed', 'paypal',
   '{"payer_email":"demo@example.com","payment_type":"instant","mc_gross":"29.99","protection_eligibility":"Eligible"}',
   '127.0.0.1',
   {NOW} - 2678400, {NOW} - 2678400);

-- Payment 2: completed – sub 1, invoice 2 (PayPal, current period start)
INSERT INTO `{PREFIX}subscriptions_payments`
  (payment_id, sub_id, user_id, invoice_id, gateway, gateway_txn_id,
   amount, currency, status, payment_method, payment_data, ip_address,
   paid_at, created_at)
VALUES
  (2, 1, {ADMIN_UID}, 2, 'paypal', 'DEMO-TXN-PP-0002',
   29.99, 'USD', 'completed', 'paypal',
   '{"payer_email":"demo@example.com","payment_type":"instant","mc_gross":"29.99","protection_eligibility":"Eligible"}',
   '127.0.0.1',
   {NOW} - 518400, {NOW} - 518400);

-- Payment 3: failed – sub 3, invoice 3 (Stripe, 5 days ago)
INSERT INTO `{PREFIX}subscriptions_payments`
  (payment_id, sub_id, user_id, invoice_id, gateway, gateway_txn_id,
   amount, currency, status, payment_method, payment_data, ip_address,
   paid_at, created_at)
VALUES
  (3, 3, {ADMIN_UID}, 3, 'stripe', '',
   79.99, 'USD', 'failed', 'card',
   '{"error":{"code":"card_declined","decline_code":"insufficient_funds","message":"Your card has insufficient funds."}}',
   '127.0.0.1',
   0, {NOW} - 432000);

-- Payment 4: partially_refunded – sub 1 historical (refund demo)
INSERT INTO `{PREFIX}subscriptions_payments`
  (payment_id, sub_id, user_id, invoice_id, gateway, gateway_txn_id,
   amount, currency, status, payment_method, payment_data, ip_address,
   paid_at, created_at)
VALUES
  (4, 4, {ADMIN_UID}, 0, 'paypal', 'DEMO-TXN-PP-0004',
   9.99, 'USD', 'partially_refunded', 'paypal',
   '{"payer_email":"demo@example.com","payment_type":"instant","mc_gross":"9.99"}',
   '127.0.0.1',
   {NOW} - 7776000, {NOW} - 7776000);

-- Payment 5: completed – sub 5 with coupon discount (Stripe)
INSERT INTO `{PREFIX}subscriptions_payments`
  (payment_id, sub_id, user_id, invoice_id, gateway, gateway_txn_id,
   amount, currency, status, payment_method, payment_data, ip_address,
   paid_at, created_at)
VALUES
  (5, 5, {ADMIN_UID}, 4, 'stripe', 'pi_DEMO_STRIPE_0005',
   59.99, 'USD', 'completed', 'card',
   '{"brand":"Visa","last4":"4242","exp_month":12,"exp_year":2027,"country":"US"}',
   '127.0.0.1',
   {NOW} - 1296000, {NOW} - 1296000);

-- ============================================================================
-- REFUNDS
-- ============================================================================

-- Refund 1: completed partial refund on payment 4 (half the Starter plan fee)
INSERT INTO `{PREFIX}subscriptions_refunds`
  (refund_id, payment_id, sub_id, user_id, amount, reason, status,
   gateway_refund_id, processed_by, created_at)
VALUES
  (1, 4, 4, {ADMIN_UID}, 5.00, 'Pro-rated refund for unused portion of subscription period',
   'completed', 'DEMO-REFUND-PP-0001', {ADMIN_UID}, {NOW} - 7689600);

-- ============================================================================
-- COUPONS  (all discount types, all validity states)
-- ============================================================================

-- Coupon 1: 20% off everything, unlimited uses, currently valid (launch promo)
INSERT INTO `{PREFIX}subscriptions_coupons`
  (coupon_id, code, name, discount_type, discount_value,
   min_amount, max_uses, uses_count, max_uses_per_user,
   applies_to, plan_ids, valid_from, valid_until, is_active, created_at)
VALUES
  (1, 'LAUNCH20', 'Launch 20% Discount',
   'percentage', 20.00,
   0.00, 0, 47, 1,
   'all', '[]',
   {NOW} - 5184000, {NOW} + 5184000, 1, {NOW} - 5184000);

-- Coupon 2: $10 fixed off Professional plan only, max 100 uses, min $20 order
INSERT INTO `{PREFIX}subscriptions_coupons`
  (coupon_id, code, name, discount_type, discount_value,
   min_amount, max_uses, uses_count, max_uses_per_user,
   applies_to, plan_ids, valid_from, valid_until, is_active, created_at)
VALUES
  (2, 'PRO10', 'Professional $10 Off',
   'fixed', 10.00,
   20.00, 100, 23, 1,
   'plan_ids', '[2]',
   {NOW} - 2592000, {NOW} + 7776000, 1, {NOW} - 2592000);

-- Coupon 3: 15% off any plan, expired last month
INSERT INTO `{PREFIX}subscriptions_coupons`
  (coupon_id, code, name, discount_type, discount_value,
   min_amount, max_uses, uses_count, max_uses_per_user,
   applies_to, plan_ids, valid_from, valid_until, is_active, created_at)
VALUES
  (3, 'HOLIDAY15', 'Holiday 15% Off',
   'percentage', 15.00,
   0.00, 500, 312, 1,
   'all', '[]',
   {NOW} - 10368000, {NOW} - 518400, 1, {NOW} - 10368000);

-- Coupon 4: 25% off Business or Enterprise, max 50 uses, currently active
INSERT INTO `{PREFIX}subscriptions_coupons`
  (coupon_id, code, name, discount_type, discount_value,
   min_amount, max_uses, uses_count, max_uses_per_user,
   applies_to, plan_ids, valid_from, valid_until, is_active, created_at)
VALUES
  (4, 'BIZQ1', 'Business Q1 Promo',
   'percentage', 25.00,
   50.00, 50, 8, 1,
   'plan_ids', '[3,4]',
   {NOW} - 604800, {NOW} + 5184000, 1, {NOW} - 604800);

-- Coupon 5: Disabled (deactivated manually)
INSERT INTO `{PREFIX}subscriptions_coupons`
  (coupon_id, code, name, discount_type, discount_value,
   min_amount, max_uses, uses_count, max_uses_per_user,
   applies_to, plan_ids, valid_from, valid_until, is_active, created_at)
VALUES
  (5, 'OLDPROMO', 'Old Promotion (Disabled)',
   'fixed', 5.00,
   0.00, 0, 0, 1,
   'all', '[]',
   0, 0, 0, {NOW} - 7776000);

-- ============================================================================
-- COUPON USAGE  (records use of LAUNCH20 on sub 5)
-- ============================================================================
INSERT INTO `{PREFIX}subscriptions_coupon_usage` (coupon_id, user_id, sub_id, used_at)
VALUES (1, {ADMIN_UID}, 5, {NOW} - 1296000);

-- ============================================================================
-- GATEWAY CONFIGURATIONS
-- ============================================================================

-- PayPal sandbox settings (demo values – not real credentials)
INSERT INTO `{PREFIX}subscriptions_gateway_configs` (gateway, config_key, config_val, is_active) VALUES
  ('paypal', 'business_email', 'sandbox@subscriptions-demo.example.com', 1),
  ('paypal', 'sandbox_mode',   '1',                                      1),
  ('paypal', 'currency',       'USD',                                    1);

-- Stripe test settings (demo values – not real credentials)
INSERT INTO `{PREFIX}subscriptions_gateway_configs` (gateway, config_key, config_val, is_active) VALUES
  ('stripe', 'publishable_key', 'pk_test_DEMO_subscriptions_placeholder',  1),
  ('stripe', 'secret_key',      'sk_test_DEMO_subscriptions_placeholder',  1),
  ('stripe', 'sandbox_mode',    '1',                                      1),
  ('stripe', 'currency',        'USD',                                    1);

-- Manual gateway instructions
INSERT INTO `{PREFIX}subscriptions_gateway_configs` (gateway, config_key, config_val, is_active) VALUES
  ('manual', 'instructions',
   'Please transfer payment to:\nBank: First National Demo Bank\nAccount: 1234567890\nRouting: 987654321\nReference: your username + plan name',
   1),
  ('manual', 'currency', 'USD', 1);

-- ============================================================================
-- CONNECTED MODULES  (demonstrates the SDK/access-control integration)
-- ============================================================================

-- Module 1: a hypothetical "Premium Downloads" XOOPS module
INSERT INTO `{PREFIX}subscriptions_connected_modules`
  (module_id, dirname, name, description,
   owner_uid, is_active, webhook_url, api_key, config, created_at)
VALUES
  (1, 'premdownloads', 'Premium Downloads',
   'Members-only file download module. Starter+ subscribers get limited access; Professional+ get full access.',
   {ADMIN_UID}, 1,
   'http://2512current4.lo/modules/premdownloads/api/webhook.php',
   'demo-api-key-premdownloads-64chars-placeholder000000000000000000000000',
   '{"max_downloads_per_day":10,"allowed_extensions":["zip","pdf","mp3"]}',
   {NOW});

-- Module 2: a hypothetical "Members Forum" module
INSERT INTO `{PREFIX}subscriptions_connected_modules`
  (module_id, dirname, name, description,
   owner_uid, is_active, webhook_url, api_key, config, created_at)
VALUES
  (2, 'memforum', 'Members Forum',
   'Exclusive community forum. Professional and above subscribers gain posting rights.',
   {ADMIN_UID}, 1,
   'http://2512current4.lo/modules/memforum/api/webhook.php',
   'demo-api-key-memforum-64chars-placeholder00000000000000000000000000000',
   '{"allow_attachments":true,"max_attachment_size_mb":5}',
   {NOW});

-- ============================================================================
-- MODULE ACCESS RULES
-- ============================================================================

-- premdownloads: Starter → limited (10 downloads), Professional+ → full
INSERT INTO `{PREFIX}subscriptions_module_access_rules` (module_id, plan_id, access_type, limit_value, xoops_group) VALUES
  (1, 1, 'limited',  10, 0),
  (1, 2, 'full',      0, 0),
  (1, 3, 'full',      0, 0),
  (1, 4, 'full',      0, 0);

-- memforum: Professional and above only get full access
INSERT INTO `{PREFIX}subscriptions_module_access_rules` (module_id, plan_id, access_type, limit_value, xoops_group) VALUES
  (2, 2, 'full', 0, 0),
  (2, 3, 'full', 0, 0),
  (2, 4, 'full', 0, 0);

-- ============================================================================
-- USAGE LOGS  (hybrid billing demo for Business plan, sub 5)
-- ============================================================================
INSERT INTO `{PREFIX}subscriptions_usage_logs`
  (sub_id, user_id, module_id, event_type, quantity, unit_price, billed, created_at)
VALUES
  (5, {ADMIN_UID}, 1, 'api_call',  50.00, 0.005000, 1, {NOW} - 1209600),
  (5, {ADMIN_UID}, 1, 'api_call', 120.00, 0.005000, 1, {NOW} - 864000),
  (5, {ADMIN_UID}, 1, 'api_call',  75.00, 0.005000, 0, {NOW} - 259200),
  (5, {ADMIN_UID}, 2, 'download',   5.00, 0.000000, 0, {NOW} - 86400);

-- ============================================================================
-- WEBHOOKS
-- ============================================================================

-- Webhook 1: All events → a request bin for demo inspection
INSERT INTO `{PREFIX}subscriptions_webhooks`
  (webhook_id, url, events, secret, is_active, last_fired, fail_count, created_at)
VALUES
  (1,
   'https://webhook.site/demo-subscriptions-endpoint',
   '["subscription.created","subscription.renewed","subscription.cancelled","subscription.expired","payment.completed","payment.failed","refund.completed","trial.started","trial.ending"]',
   'demo-webhook-secret-hmacsha256-placeholder',
   1, {NOW} - 86400, 0, {NOW} - 604800);

-- Webhook 2: Payment events only → a hypothetical accounting system
INSERT INTO `{PREFIX}subscriptions_webhooks`
  (webhook_id, url, events, secret, is_active, last_fired, fail_count, created_at)
VALUES
  (2,
   'https://accounting.example.com/api/subscriptions-events',
   '["payment.completed","refund.completed"]',
   'accounting-webhook-secret-placeholder',
   1, {NOW} - 1296000, 0, {NOW} - 1296000);

-- Webhook 3: Disabled webhook (failed too many times)
INSERT INTO `{PREFIX}subscriptions_webhooks`
  (webhook_id, url, events, secret, is_active, last_fired, fail_count, created_at)
VALUES
  (3,
   'https://old-crm.example.com/hooks/membership',
   '["subscription.created","subscription.cancelled"]',
   'old-crm-secret-placeholder',
   0, {NOW} - 5184000, 10, {NOW} - 5184000);
