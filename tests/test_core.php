<?php

use XoopsModules\Subscriptions\Utility;
/**
 * Subscriptions Unit Tests
 *
 * Tests core business logic that does NOT require a live database.
 * Run with: php tests/test_core.php
 *
 * @package    subscriptions
 * @subpackage tests
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start session before any output to avoid CLI session warnings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Minimal stubs so the classes can be loaded outside XOOPS
if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', dirname(__DIR__));
}
if (!defined('XOOPS_URL')) {
    define('XOOPS_URL', 'http://localhost');
}
if (!defined('XOOPS_VAR_PATH')) {
    define('XOOPS_VAR_PATH', sys_get_temp_dir());
}

// Stub XOOPS base classes
if (!class_exists('XoopsObject')) {
    class XoopsObject
    {
        protected array $vars = [];
        public function initVar(string $key, int $dtype, $default = null, bool $req = false, $maxlength = null): void
        {
            $this->vars[$key] = ['value' => $default];
        }
        public function setVar(string $key, $val): void { $this->vars[$key]['value'] = $val; }
        public function getVar(string $key, string $format = 's') { return $this->vars[$key]['value'] ?? null; }
        public function getVars(): array
        {
            $out = [];
            foreach ($this->vars as $k => $v) { $out[$k] = $v['value']; }
            return $out;
        }
    }
}
if (!class_exists('XoopsPersistableObjectHandler')) {
    class XoopsPersistableObjectHandler
    {
        protected $db;
        protected string $table;
        public function __construct($db = null, string $table = '', string $cls = '', string $pk = '', string $identifier = '') {
            $this->db    = $db;
            $this->table = $table;
        }
        public function get($id) { return null; }
        public function create() { return new XoopsObject(); }
        public function insert($obj) { return true; }
        public function delete($obj, $force = false) { return true; }
        public function getAll($criteria = null, $fields = null, $asObject = true, $debug = false) { return []; }
        public function getCount($criteria = null) { return 0; }
        public function deleteAll($criteria = null) { return true; }
    }
}
if (!class_exists('Criteria')) {
    class Criteria { public function __construct(...$args) {} }
}
if (!class_exists('CriteriaCompo')) {
    class CriteriaCompo extends Criteria {
        public function add($c) {}
        public function setSort(string $s) {}
        public function setOrder(string $o) {}
        public function setLimit(int $l) {}
        public function setStart(int $s) {}
    }
}
if (!defined('XOBJ_DTYPE_INT'))    define('XOBJ_DTYPE_INT',    0);
if (!defined('XOBJ_DTYPE_TXTBOX')) define('XOBJ_DTYPE_TXTBOX', 1);
if (!defined('XOBJ_DTYPE_TXTAREA'))define('XOBJ_DTYPE_TXTAREA',2);
if (!defined('XOBJ_DTYPE_OTHER'))  define('XOBJ_DTYPE_OTHER',  3);

// Load language stubs
if (!defined('_MD_SUBSCRIPTIONS_CYCLE_ONE_TIME')) {
    define('_MD_SUBSCRIPTIONS_CYCLE_ONE_TIME',  'One-Time');
    define('_MD_SUBSCRIPTIONS_CYCLE_DAILY',     'Daily');
    define('_MD_SUBSCRIPTIONS_CYCLE_WEEKLY',    'Weekly');
    define('_MD_SUBSCRIPTIONS_CYCLE_MONTHLY',   'Monthly');
    define('_MD_SUBSCRIPTIONS_CYCLE_QUARTERLY', 'Quarterly');
    define('_MD_SUBSCRIPTIONS_CYCLE_ANNUAL',    'Annual');
    define('_MD_SUBSCRIPTIONS_STATUS_TRIAL',    'Trial');
    define('_MD_SUBSCRIPTIONS_STATUS_ACTIVE',   'Active');
    define('_MD_SUBSCRIPTIONS_STATUS_PAST_DUE', 'Past Due');
    define('_MD_SUBSCRIPTIONS_STATUS_CANCELLED','Cancelled');
    define('_MD_SUBSCRIPTIONS_STATUS_EXPIRED',  'Expired');
    define('_MD_SUBSCRIPTIONS_STATUS_PAUSED',   'Paused');
    define('_MD_SUBSCRIPTIONS_COUPON_NOT_FOUND','Coupon code not found.');
    define('_MD_SUBSCRIPTIONS_COUPON_INVALID',  'This coupon is no longer valid.');
    define('_MD_SUBSCRIPTIONS_COUPON_PLAN_MISMATCH','This coupon does not apply to the selected plan.');
    define('_MD_SUBSCRIPTIONS_COUPON_MIN_AMOUNT','Minimum order amount not reached.');
    define('_MD_SUBSCRIPTIONS_COUPON_USER_LIMIT','You have already used this coupon.');
}

require_once XOOPS_ROOT_PATH . '/class/Plan.php';
require_once XOOPS_ROOT_PATH . '/class/Subscription.php';
require_once XOOPS_ROOT_PATH . '/class/Coupon.php';

// -------------------------------------------------------------------------
// Simple test framework
// -------------------------------------------------------------------------
$passed = 0;
$failed = 0;

function test(string $name, bool $condition, string $details = ''): void
{
    global $passed, $failed;
    if ($condition) {
        echo "\033[32m✓ PASS\033[0m $name\n";
        $passed++;
    } else {
        echo "\033[31m✗ FAIL\033[0m $name" . ($details ? " — $details" : '') . "\n";
        $failed++;
    }
}

// =========================================================================
// Plan tests
// =========================================================================
echo "\n=== Plan ===\n";

$plan = new Plan();
$plan->setVar('plan_id',         1);
$plan->setVar('name',            'Pro Monthly');
$plan->setVar('price',           '29.99');
$plan->setVar('billing_cycle',   Plan::CYCLE_MONTHLY);
$plan->setVar('trial_days',      14);
$plan->setVar('discount_annual', '10.00');

test('Plan name is set correctly',           $plan->getVar('name') === 'Pro Monthly');
test('Plan price parses to float correctly', (float)$plan->getVar('price') === 29.99);
test('Billing cycle label: monthly',         $plan->getBillingCycleLabel() === 'Monthly');
test('Trial days are stored correctly',      (int)$plan->getVar('trial_days') === 14);

// Annual price: 29.99 * 12 = 359.88 → 10% off = 323.89
$annualPrice = $plan->getAnnualPrice();
test('Annual price computed with discount',
    abs($annualPrice - 323.89) < 0.01,
    "got {$annualPrice}, expected ~323.89"
);

// Next renewal date
$plan->setVar('billing_cycle', Plan::CYCLE_MONTHLY);
$now    = mktime(0, 0, 0, 1, 15, 2024); // 2024-01-15
$next   = $plan->getNextRenewalDate($now);
$nextDt = date('Y-m-d', $next);
test('Monthly renewal is +1 month', $nextDt === '2024-02-15', "got {$nextDt}");

$plan->setVar('billing_cycle', Plan::CYCLE_ANNUAL);
$nextAnnual = $plan->getNextRenewalDate($now);
test('Annual renewal is +1 year', date('Y', $nextAnnual) === '2025');

$plan->setVar('billing_cycle', Plan::CYCLE_ONE_TIME);
test('One-time plan returns 0 for renewal', $plan->getNextRenewalDate($now) === 0);

// Slug generation tested directly (no DB)
test('Billing cycle QUARTERLY label correct',
    (function() {
        $p = new Plan();
        $p->setVar('billing_cycle', Plan::CYCLE_QUARTERLY);
        return $p->getBillingCycleLabel() === 'Quarterly';
    })()
);

// =========================================================================
// Subscription tests
// =========================================================================
echo "\n=== Subscription ===\n";

$sub = new Subscription();
$sub->setVar('sub_id',               1);
$sub->setVar('user_id',              42);
$sub->setVar('plan_id',              1);
$sub->setVar('status',               Subscription::STATUS_ACTIVE);
$sub->setVar('current_period_end',   time() + 86400 * 10); // expires in 10 days
$sub->setVar('auto_renew',           1);

test('Active subscription isActive() returns true',  $sub->isActive());
test('Status label: active',                          $sub->getStatusLabel() === 'Active');

$sub->setVar('current_period_end', time() - 3600); // expired 1 hour ago
test('Expired subscription isActive() returns false', !$sub->isActive());

$sub->setVar('status',             Subscription::STATUS_ACTIVE);
$sub->setVar('current_period_end', time() - 3600);
test('Grace period (3 days) includes recent expiry', $sub->isInGracePeriod(3));

$sub->setVar('current_period_end', time() - 86400 * 5); // expired 5 days ago
test('Grace period (3 days) excludes old expiry',    !$sub->isInGracePeriod(3));

$sub->setVar('status', Subscription::STATUS_CANCELLED);
test('Cancelled subscription isActive() returns false', !$sub->isActive());

// =========================================================================
// Coupon tests
// =========================================================================
echo "\n=== Coupon ===\n";

$coupon = new Coupon();
$coupon->setVar('coupon_id',      10);
$coupon->setVar('code',           'SAVE20');
$coupon->setVar('discount_type',  Coupon::TYPE_PERCENTAGE);
$coupon->setVar('discount_value', '20.00');
$coupon->setVar('min_amount',     '0.00');
$coupon->setVar('max_uses',       0);
$coupon->setVar('uses_count',     0);
$coupon->setVar('max_uses_per_user', 1);
$coupon->setVar('applies_to',    'all');
$coupon->setVar('plan_ids',       '[]');
$coupon->setVar('valid_from',     0);
$coupon->setVar('valid_until',    0);
$coupon->setVar('is_active',      1);

test('Active coupon isValid() returns true',         $coupon->isValid());
test('20% off $100 = $20 discount',
    abs($coupon->calculateDiscount(100.0) - 20.0) < 0.001
);
test('Coupon applies to all plans',                  $coupon->appliesToPlan(999));

// Fixed discount
$coupon->setVar('discount_type',  Coupon::TYPE_FIXED);
$coupon->setVar('discount_value', '15.00');
test('Fixed $15 off $100 = $15 discount',
    abs($coupon->calculateDiscount(100.0) - 15.0) < 0.001
);
test('Fixed discount capped at price',
    abs($coupon->calculateDiscount(10.0) - 10.0) < 0.001
);

// Inactive coupon
$coupon->setVar('is_active', 0);
test('Inactive coupon isValid() returns false',      !$coupon->isValid());

// Expired coupon
$coupon->setVar('is_active',   1);
$coupon->setVar('valid_until', time() - 3600);
test('Expired coupon (past valid_until) is invalid', !$coupon->isValid());

// Future coupon
$coupon->setVar('valid_until', 0);
$coupon->setVar('valid_from',  time() + 86400);
test('Future coupon (before valid_from) is invalid', !$coupon->isValid());

// Max uses reached
$coupon->setVar('valid_from',  0);
$coupon->setVar('max_uses',    5);
$coupon->setVar('uses_count',  5);
test('Coupon at max_uses is invalid',                !$coupon->isValid());

// Plan restriction
$coupon->setVar('max_uses',    0);
$coupon->setVar('uses_count',  0);
$coupon->setVar('applies_to',  'plan_ids');
$coupon->setVar('plan_ids',    '[1, 2, 3]');
test('Coupon applies to plan 2',                     $coupon->appliesToPlan(2));
test('Coupon does not apply to plan 5',              !$coupon->appliesToPlan(5));

// =========================================================================
// Helper functions
// =========================================================================
echo "\n=== Helper Functions ===\n";

require_once XOOPS_ROOT_PATH . '/include/functions.php';

test('subscriptions_format_money formats correctly',
    Utility::formatMoney(29.99, 'USD') === '$29.99'
);
test('subscriptions_format_money EUR symbol',
    Utility::formatMoney(9.99, 'EUR') === '€9.99'
);
test('subscriptions_currency_symbol USD',
    Utility::currencySymbol('USD') === '$'
);
test('subscriptions_currency_symbol GBP',
    Utility::currencySymbol('GBP') === '£'
);
test('subscriptions_truncate short string unchanged',
    Utility::truncate('Hello', 10) === 'Hello'
);
test('subscriptions_truncate long string truncated',
    mb_strlen(Utility::truncate('Hello World this is a long string', 10)) === 11
);
test('subscriptions_format_date zero returns dash',
    Utility::formatDate(0) === '—'
);
test('subscriptions_format_date non-zero returns date string',
    Utility::formatDate(mktime(0,0,0,1,15,2024)) === '2024-01-15'
);

// CSRF token generation + verification
$token = Utility::generateToken('test_form');
test('CSRF token is a non-empty hex string',
    !empty($token) && ctype_xdigit($token)
);
test('CSRF token verifies correctly',
    Utility::verifyToken($token, 'test_form')
);
test('CSRF token is single-use (fails second time)',
    !Utility::verifyToken($token, 'test_form')
);
test('Wrong CSRF token fails verification',
    !Utility::verifyToken('deadbeef', 'other_form')
);

// =========================================================================
// Summary
// =========================================================================
echo "\n";
echo "Results: \033[32m{$passed} passed\033[0m, ";
echo ($failed > 0 ? "\033[31m" : "\033[32m") . "{$failed} failed\033[0m\n";
exit($failed > 0 ? 1 : 0);
