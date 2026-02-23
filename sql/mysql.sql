-- Subscriptions Module Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.3+

-- ============================================================
-- Plans: defines pricing tiers
-- ============================================================
CREATE TABLE subscriptions_plans (
  plan_id       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(150)     NOT NULL DEFAULT '',
  slug          VARCHAR(150)     NOT NULL DEFAULT '',
  description   TEXT             NOT NULL,
  price         DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  currency      VARCHAR(10)      NOT NULL DEFAULT 'USD',
  billing_cycle ENUM('one_time','daily','weekly','monthly','quarterly','annual') NOT NULL DEFAULT 'monthly',
  trial_days    SMALLINT(5)      NOT NULL DEFAULT '0',
  setup_fee     DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  discount_annual DECIMAL(5,2)  NOT NULL DEFAULT '0.00' COMMENT 'Annual discount percentage',
  pricing_model ENUM('flat','usage','hybrid') NOT NULL DEFAULT 'flat',
  usage_unit    VARCHAR(50)      NOT NULL DEFAULT '' COMMENT 'e.g. visit, download',
  usage_price   DECIMAL(12,6)   NOT NULL DEFAULT '0.000000' COMMENT 'Price per usage unit',
  is_featured   TINYINT(1)       NOT NULL DEFAULT '0',
  is_active     TINYINT(1)       NOT NULL DEFAULT '1',
  sort_order    SMALLINT(5)      NOT NULL DEFAULT '0',
  max_users     INT(10)          NOT NULL DEFAULT '0' COMMENT '0 = unlimited',
  created_at    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  updated_at    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (plan_id),
  UNIQUE KEY slug (slug),
  KEY is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Plan features
-- ============================================================
CREATE TABLE subscriptions_plan_features (
  feature_id  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  plan_id     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  feature     VARCHAR(255)     NOT NULL DEFAULT '',
  sort_order  SMALLINT(5)      NOT NULL DEFAULT '0',
  PRIMARY KEY (feature_id),
  KEY plan_id (plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Subscriptions: active user plans
-- ============================================================
CREATE TABLE subscriptions_subscriptions (
  sub_id        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  plan_id       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  status        ENUM('trial','active','past_due','cancelled','expired','paused') NOT NULL DEFAULT 'active',
  billing_cycle ENUM('one_time','daily','weekly','monthly','quarterly','annual') NOT NULL DEFAULT 'monthly',
  started_at    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  trial_ends_at INT(10) UNSIGNED NOT NULL DEFAULT '0',
  current_period_start INT(10) UNSIGNED NOT NULL DEFAULT '0',
  current_period_end   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  cancelled_at  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  cancel_reason VARCHAR(255)     NOT NULL DEFAULT '',
  gateway       VARCHAR(50)      NOT NULL DEFAULT '',
  gateway_sub_id VARCHAR(255)    NOT NULL DEFAULT '' COMMENT 'External subscription ID',
  coupon_id     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  discount_amount DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  amount_paid   DECIMAL(12,2)   NOT NULL DEFAULT '0.00',
  auto_renew    TINYINT(1)       NOT NULL DEFAULT '1',
  created_at    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  updated_at    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (sub_id),
  KEY user_id (user_id),
  KEY plan_id (plan_id),
  KEY status (status),
  KEY current_period_end (current_period_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Payments: individual payment transactions
-- ============================================================
CREATE TABLE subscriptions_payments (
  payment_id      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  sub_id          INT(10) UNSIGNED NOT NULL DEFAULT '0',
  user_id         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  invoice_id      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  gateway         VARCHAR(50)      NOT NULL DEFAULT '',
  gateway_txn_id  VARCHAR(255)     NOT NULL DEFAULT '',
  amount          DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  currency        VARCHAR(10)      NOT NULL DEFAULT 'USD',
  status          ENUM('pending','completed','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
  payment_method  VARCHAR(50)      NOT NULL DEFAULT '' COMMENT 'card, paypal, wallet, etc',
  payment_data    TEXT             NOT NULL COMMENT 'JSON gateway response',
  ip_address      VARCHAR(45)      NOT NULL DEFAULT '',
  paid_at         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  created_at      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (payment_id),
  KEY sub_id (sub_id),
  KEY user_id (user_id),
  KEY gateway_txn_id (gateway_txn_id),
  KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Invoices
-- ============================================================
CREATE TABLE subscriptions_invoices (
  invoice_id     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_number VARCHAR(50)      NOT NULL DEFAULT '',
  user_id        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  sub_id         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  status         ENUM('draft','open','paid','void','uncollectible') NOT NULL DEFAULT 'open',
  subtotal       DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  tax_rate       DECIMAL(5,2)     NOT NULL DEFAULT '0.00',
  tax_amount     DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  discount_amount DECIMAL(12,2)  NOT NULL DEFAULT '0.00',
  total          DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  currency       VARCHAR(10)      NOT NULL DEFAULT 'USD',
  due_date       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  paid_at        INT(10) UNSIGNED NOT NULL DEFAULT '0',
  period_start   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  period_end     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  notes          TEXT             NOT NULL,
  created_at     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  updated_at     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (invoice_id),
  UNIQUE KEY invoice_number (invoice_number),
  KEY user_id (user_id),
  KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Invoice line items
-- ============================================================
CREATE TABLE subscriptions_invoice_items (
  item_id     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  invoice_id  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  description VARCHAR(255)     NOT NULL DEFAULT '',
  quantity    DECIMAL(10,2)    NOT NULL DEFAULT '1.00',
  unit_price  DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  amount      DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  PRIMARY KEY (item_id),
  KEY invoice_id (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Coupons
-- ============================================================
CREATE TABLE subscriptions_coupons (
  coupon_id      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  code           VARCHAR(50)      NOT NULL DEFAULT '',
  name           VARCHAR(150)     NOT NULL DEFAULT '',
  discount_type  ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
  discount_value DECIMAL(10,2)    NOT NULL DEFAULT '0.00',
  min_amount     DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  max_uses       INT(10)          NOT NULL DEFAULT '0' COMMENT '0 = unlimited',
  uses_count     INT(10)          NOT NULL DEFAULT '0',
  max_uses_per_user INT(10)       NOT NULL DEFAULT '1',
  applies_to     SET('all','plan_ids') NOT NULL DEFAULT 'all',
  plan_ids       TEXT             NOT NULL COMMENT 'JSON array of plan IDs',
  valid_from     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  valid_until    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  is_active      TINYINT(1)       NOT NULL DEFAULT '1',
  created_at     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (coupon_id),
  UNIQUE KEY code (code),
  KEY is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Coupon usage tracking
-- ============================================================
CREATE TABLE subscriptions_coupon_usage (
  usage_id   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  coupon_id  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  user_id    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  sub_id     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  used_at    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (usage_id),
  KEY coupon_id (coupon_id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Connected modules (third-party module integrations)
-- ============================================================
CREATE TABLE subscriptions_connected_modules (
  module_id    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  dirname      VARCHAR(50)      NOT NULL DEFAULT '',
  name         VARCHAR(150)     NOT NULL DEFAULT '',
  description  TEXT             NOT NULL,
  owner_uid    INT(10) UNSIGNED NOT NULL DEFAULT '0',
  is_active    TINYINT(1)       NOT NULL DEFAULT '1',
  webhook_url  VARCHAR(500)     NOT NULL DEFAULT '',
  api_key      VARCHAR(64)      NOT NULL DEFAULT '',
  config       TEXT             NOT NULL COMMENT 'JSON module config',
  created_at   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (module_id),
  UNIQUE KEY dirname (dirname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Module access rules (which plans grant access to which modules)
-- ============================================================
CREATE TABLE subscriptions_module_access_rules (
  rule_id     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  module_id   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  plan_id     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  access_type ENUM('full','limited','metered') NOT NULL DEFAULT 'full',
  limit_value INT(10)          NOT NULL DEFAULT '0' COMMENT 'e.g. downloads per month',
  xoops_group INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Grant XOOPS group membership',
  PRIMARY KEY (rule_id),
  UNIQUE KEY module_plan (module_id, plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Usage logs (for usage-based billing)
-- ============================================================
CREATE TABLE subscriptions_usage_logs (
  log_id      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  sub_id      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  user_id     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  module_id   INT(10) UNSIGNED NOT NULL DEFAULT '0',
  event_type  VARCHAR(50)      NOT NULL DEFAULT '' COMMENT 'visit, download, api_call',
  quantity    DECIMAL(10,2)    NOT NULL DEFAULT '1.00',
  unit_price  DECIMAL(12,6)   NOT NULL DEFAULT '0.000000',
  billed      TINYINT(1)       NOT NULL DEFAULT '0',
  created_at  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (log_id),
  KEY sub_id (sub_id),
  KEY user_id (user_id),
  KEY billed (billed),
  KEY created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Webhooks configuration
-- ============================================================
CREATE TABLE subscriptions_webhooks (
  webhook_id  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  url         VARCHAR(500)     NOT NULL DEFAULT '',
  events      TEXT             NOT NULL COMMENT 'JSON array of subscribed events',
  secret      VARCHAR(64)      NOT NULL DEFAULT '',
  is_active   TINYINT(1)       NOT NULL DEFAULT '1',
  last_fired  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  fail_count  SMALLINT(5)      NOT NULL DEFAULT '0',
  created_at  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (webhook_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Refunds
-- ============================================================
CREATE TABLE subscriptions_refunds (
  refund_id   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  payment_id  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  sub_id      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  user_id     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  amount      DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
  reason      VARCHAR(255)     NOT NULL DEFAULT '',
  status      ENUM('pending','completed','failed') NOT NULL DEFAULT 'pending',
  gateway_refund_id VARCHAR(255) NOT NULL DEFAULT '',
  processed_by INT(10) UNSIGNED NOT NULL DEFAULT '0',
  created_at  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (refund_id),
  KEY payment_id (payment_id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Gateway configurations (stored per gateway, encrypted)
-- ============================================================
CREATE TABLE subscriptions_gateway_configs (
  config_id   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  gateway     VARCHAR(50)      NOT NULL DEFAULT '',
  config_key  VARCHAR(100)     NOT NULL DEFAULT '',
  config_val  TEXT             NOT NULL,
  is_active   TINYINT(1)       NOT NULL DEFAULT '1',
  PRIMARY KEY (config_id),
  UNIQUE KEY gateway_key (gateway, config_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
