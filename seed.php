<?php

declare(strict_types=1);

/**
 * Subscriptions Faker Seeder
 *
 * Generates randomized, volume-scalable test data using fakerphp/faker.
 * Produces different realistic data on every run. Replaces the static seed.sql approach.
 *
 * Usage (browser):  http://yoursite/modules/subscriptions/seed.php?confirm=yes
 * Usage (CLI):      php seed.php
 * URL parameters:   ?confirm=yes&plans=4&users=10&subs_per_user=2&locale=en_US
 *
 * Requires: composer install --dev (fakerphp/faker must be available)
 *
 * @package    subscriptions
 * @subpackage tools
 */

// ── Bootstrap XOOPS ──────────────────────────────────────────────────────────
$GLOBALS['xoopsOption']['template_main'] = 'system_dummy.tpl';
require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

// ── Security: admin only ──────────────────────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    if (!isset($xoopsUser) || !$xoopsUser || !$xoopsUser->isAdmin()) {
        header('HTTP/1.1 403 Forbidden');
        exit('<h1>403 – Admins only.</h1><p>Please <a href="' . XOOPS_URL . '/user.php">log in</a> as an administrator first.</p>');
    }
}

// ── Load Faker ────────────────────────────────────────────────────────────────
$fakerLoaded = false;
foreach ([
    __DIR__ . '/vendor/autoload.php',
    XOOPS_ROOT_PATH . '/vendor/autoload.php',
    defined('XOOPS_LIB_PATH') ? XOOPS_LIB_PATH . '/vendor/autoload.php' : null,
] as $vendorPath) {
    if ($vendorPath && is_readable($vendorPath)) {
        require_once $vendorPath;
        if (class_exists(\Faker\Factory::class)) {
            $fakerLoaded = true;
            break;
        }
    }
}

if (!$fakerLoaded) {
    exit('<h1>Faker not found</h1><p>Run <code>composer install</code> in the module directory, then try again.</p>');
}

// ── Configuration ─────────────────────────────────────────────────────────────
$cfg = [
    'plans'         => max(2, min((int)($_GET['plans']          ?? 4), 8)),
    'users'         => max(1, min((int)($_GET['users']          ?? 10), 50)),
    'subs_per_user' => max(1, min((int)($_GET['subs_per_user']  ?? 2), 5)),
    'locale'        => preg_replace('/[^a-zA-Z_]/', '', ($_GET['locale'] ?? 'en_US')),
    'currency'      => 'USD',
    'symbol'        => '$',
];
$totalSubs = $cfg['users'] * $cfg['subs_per_user'];

// ── Confirmation gate ─────────────────────────────────────────────────────────
$confirmed = (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') || PHP_SAPI === 'cli';

if (!$confirmed) {
    $selfUrl = XOOPS_URL . '/modules/subscriptions/seed.php?confirm=yes'
        . '&plans='         . $cfg['plans']
        . '&users='         . $cfg['users']
        . '&subs_per_user=' . $cfg['subs_per_user'];

    echo <<<HTML
    <!doctype html><html lang="en"><head><meta charset="utf-8">
    <title>Subscriptions – Faker Seeder</title>
    <style>
      body{font-family:system-ui,sans-serif;max-width:740px;margin:60px auto;padding:0 20px;color:#333}
      h1{color:#1a6b3c}
      .warn{background:#fff3cd;border:1px solid #ffc107;padding:14px 18px;border-radius:6px;margin:20px 0}
      .config{background:#f0f9ff;border:1px solid #bae6fd;padding:14px 18px;border-radius:6px;margin:20px 0}
      .config table{border-collapse:collapse;width:100%}
      .config td{padding:4px 12px 4px 0;font-size:.9em}
      .config td:first-child{font-weight:600;width:180px}
      .config input[type=number]{width:70px;padding:3px 6px;border:1px solid #ccc;border-radius:4px}
      a.btn{display:inline-block;padding:12px 28px;background:#1a6b3c;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;margin-top:10px}
      a.cancel{color:#666;margin-left:16px;font-size:.9em}
    </style></head><body>
    <h1>Subscriptions Faker Seeder</h1>
    <p>Generates randomized demo data using <strong>fakerphp/faker</strong>.
       Different data is produced on every run.</p>
    <p>Will create:</p>
    <ul>
      <li><strong>{$cfg['plans']}</strong> subscription plans (all billing cycles + pricing models)</li>
      <li><strong>~{$totalSubs}</strong> subscriptions spread across <strong>{$cfg['users']}</strong> users (all lifecycle states)</li>
      <li>Invoices, payments, and refunds for each subscription</li>
      <li>5 coupons (mix of active, expired, disabled)</li>
      <li>Gateway configs (PayPal, Stripe, Manual — placeholder credentials)</li>
      <li>2 connected modules with access rules</li>
      <li>Usage logs for metered subscriptions</li>
      <li>3 webhooks (active and disabled)</li>
    </ul>
    <div class="warn">⚠️ <strong>Warning:</strong> This permanently removes all existing Subscriptions data.
    Use on development/demo installations only.</div>
    <div class="config">
      <strong>Configuration</strong> — adjust via URL parameters:<br><br>
      <table>
        <tr><td>plans</td><td>Number of plans (2–8): <code>?plans={$cfg['plans']}</code></td></tr>
        <tr><td>users</td><td>Users to spread across (1–50): <code>?users={$cfg['users']}</code></td></tr>
        <tr><td>subs_per_user</td><td>Max subscriptions per user (1–5): <code>?subs_per_user={$cfg['subs_per_user']}</code></td></tr>
        <tr><td>locale</td><td>Faker locale: <code>?locale=en_US</code> / <code>fr_FR</code> / <code>de_DE</code> / etc.</td></tr>
      </table>
    </div>
    <a class="btn" href="{$selfUrl}">Yes, seed demo data now</a>
    <a class="cancel" href="javascript:history.back()">Cancel</a>
    </body></html>
    HTML;
    exit;
}

// ── Seeder ────────────────────────────────────────────────────────────────────
$fake   = \Faker\Factory::create($cfg['locale']);
$db     = \XoopsDatabaseFactory::getDatabaseConnection();
$now    = time();
$counts = [];
$errors = [];

/**
 * Build and execute an INSERT; return the new auto_increment ID (0 on failure).
 */
$ins = static function (string $table, array $row) use ($db, &$counts, &$errors): int {
    $cols = implode(', ', array_map(fn($c) => "`{$c}`", array_keys($row)));
    $vals = implode(', ', array_map(static function ($v) use ($db) {
        if ($v === null)                    return 'NULL';
        if (is_int($v) || is_float($v))    return (string) $v;
        return "'" . $db->escape((string) $v) . "'";
    }, array_values($row)));

    $sql = "INSERT INTO `{$table}` ({$cols}) VALUES ({$vals})";

    if ($db->queryF($sql) === false) {
        $errors[] = $db->error() . "\n  SQL: " . substr($sql, 0, 250);
        return 0;
    }
    $shortName = preg_replace('/^.*subscriptions_/', '', $table);
    $counts[$shortName] = ($counts[$shortName] ?? 0) + 1;
    return (int) $db->getInsertId();
};

/**
 * Delete all rows and reset AUTO_INCREMENT to 1.
 */
$wipe = static function (string $table) use ($db): void {
    $db->queryF("DELETE FROM `{$table}`");
    $db->queryF("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
};

// ── Wipe all Subscriptions tables (dependency order) ──────────────────────────
foreach ([
    'subscriptions_module_access_rules',
    'subscriptions_connected_modules',
    'subscriptions_usage_logs',
    'subscriptions_coupon_usage',
    'subscriptions_coupons',
    'subscriptions_webhooks',
    'subscriptions_gateway_configs',
    'subscriptions_refunds',
    'subscriptions_invoice_items',
    'subscriptions_invoices',
    'subscriptions_payments',
    'subscriptions_subscriptions',
    'subscriptions_plan_features',
    'subscriptions_plans',
] as $tbl) {
    $wipe($db->prefix($tbl));
}

// ── Get real XOOPS user IDs ───────────────────────────────────────────────────
$res     = $db->queryF('SELECT uid FROM `' . $db->prefix('users') . '` WHERE uid > 0 ORDER BY uid LIMIT 100');
$allUids = [];
while ($row = $db->fetchArray($res)) {
    $allUids[] = (int) $row['uid'];
}
if (empty($allUids)) {
    $allUids = [1];
}

// Cycle through available UIDs to fill requested user count
$userIds = [];
for ($i = 0; $i < $cfg['users']; $i++) {
    $userIds[] = $allUids[$i % count($allUids)];
}
$userIds = array_unique($userIds);
if (empty($userIds)) {
    $userIds = [1];
}

// ── Plans ─────────────────────────────────────────────────────────────────────
$tierNames     = ['Starter', 'Basic', 'Professional', 'Business', 'Enterprise', 'Plus', 'Premium', 'Ultimate'];
$billingCycles = ['monthly', 'monthly', 'annual', 'monthly', 'annual', 'one_time', 'quarterly', 'monthly'];
$pricingModels = ['flat', 'flat', 'flat', 'hybrid', 'flat', 'flat', 'usage', 'flat'];
$basePrices    = [4.99, 9.99, 19.99, 29.99, 49.99, 79.99, 99.99, 149.99];

// Feature lists that rotate across plans
$featureSets = [
    ['Up to 3 projects', '5 GB storage', 'Email support (48h SLA)', 'Basic API access', '14-day free trial'],
    ['Unlimited projects', '50 GB storage', 'Priority support', 'Full API access', 'Webhook integrations'],
    ['Everything in Professional', '200 GB storage', 'Phone support', 'Custom integrations', 'Team management'],
    ['Enterprise licensing', 'Unlimited storage', 'Dedicated account manager', '99.9% uptime SLA', '24/7 support'],
    ['Core module access', 'Community forum', 'Monthly newsletter', 'Basic analytics', '7-day trial'],
    ['Advanced reporting', 'White-label options', 'API priority tier', 'Audit logs', 'SSO support'],
    ['Pay-as-you-go access', 'No subscription required', 'Usage dashboard', 'Per-action billing'],
    ['Team workspace (10 seats)', 'Shared projects', 'Admin controls', 'Role-based permissions', 'Activity logs'],
];

// plan_id => [price, cycle, model]
$planMeta = [];

for ($p = 0; $p < $cfg['plans']; $p++) {
    $cycle  = $billingCycles[$p % count($billingCycles)];
    $model  = $pricingModels[$p % count($pricingModels)];
    $price  = $basePrices[$p % count($basePrices)];
    $name   = $tierNames[$p % count($tierNames)];

    // Annual plans are priced as ~10 months
    if ($cycle === 'annual') {
        $price = round($price * 10, 2);
    }

    $planId = $ins($db->prefix('subscriptions_plans'), [
        'name'            => $name,
        'slug'            => strtolower($name) . '-' . $cycle,
        'description'     => $fake->sentence(12),
        'price'           => $price,
        'currency'        => $cfg['currency'],
        'billing_cycle'   => $cycle,
        'trial_days'      => $p === 0 ? 14 : ($p === 1 ? 7 : 0),
        'setup_fee'       => $p === 2 ? 49.99 : 0.00,
        'discount_annual' => $cycle === 'monthly' ? $fake->numberBetween(10, 25) : 0,
        'pricing_model'   => $model,
        'usage_unit'      => in_array($model, ['hybrid', 'usage']) ? 'api_call' : '',
        'usage_price'     => in_array($model, ['hybrid', 'usage']) ? 0.005 : 0.0,
        'is_featured'     => $p === 1 ? 1 : 0,
        'is_active'       => 1,
        'sort_order'      => ($p + 1) * 10,
        'max_users'       => $p === $cfg['plans'] - 1 ? 500 : 0,
        'created_at'      => $now - $fake->numberBetween(30, 90) * 86400,
        'updated_at'      => $now,
    ]);

    $planMeta[$planId] = ['price' => $price, 'cycle' => $cycle, 'model' => $model, 'name' => $name];

    foreach ($featureSets[$p % count($featureSets)] as $sort => $feature) {
        $ins($db->prefix('subscriptions_plan_features'), [
            'plan_id'    => $planId,
            'feature'    => $feature,
            'sort_order' => $sort,
        ]);
    }
}

$planIds = array_keys($planMeta);

// ── Subscriptions ─────────────────────────────────────────────────────────────
// Statuses weighted toward active; include all lifecycle states
$statusPool = ['active', 'active', 'active', 'trial', 'past_due', 'cancelled', 'expired'];
$gatewayPool = ['paypal', 'paypal', 'stripe', 'stripe', 'manual'];

// Collected sub data for invoice/payment generation
$subs = [];

foreach ($userIds as $uid) {
    $numSubs  = $fake->numberBetween(1, $cfg['subs_per_user']);
    $pickedPlans = $fake->randomElements($planIds, min($numSubs, count($planIds)));

    foreach ($pickedPlans as $planId) {
        $meta    = $planMeta[$planId];
        $status  = $fake->randomElement($statusPool);
        $gateway = $fake->randomElement($gatewayPool);

        // Build date windows appropriate to each lifecycle state
        switch ($status) {
            case 'active':
                $startedAt   = $now - $fake->numberBetween(30, 120) * 86400;
                $periodStart = $now - $fake->numberBetween(1, 28) * 86400;
                $periodEnd   = $now + $fake->numberBetween(3, 30) * 86400;
                $trialEnd    = 0;
                $cancelledAt = 0;
                $cancelReason = '';
                break;

            case 'trial':
                $startedAt    = $now - $fake->numberBetween(0, 10) * 86400;
                $trialEnd     = $startedAt + 14 * 86400;
                $periodStart  = $startedAt;
                $periodEnd    = $trialEnd;
                $cancelledAt  = 0;
                $cancelReason = '';
                break;

            case 'past_due':
                $startedAt    = $now - $fake->numberBetween(60, 180) * 86400;
                $periodStart  = $now - $fake->numberBetween(30, 60) * 86400;
                $periodEnd    = $now - $fake->numberBetween(1, 10) * 86400;
                $trialEnd     = 0;
                $cancelledAt  = 0;
                $cancelReason = '';
                break;

            case 'cancelled':
                $startedAt    = $now - $fake->numberBetween(60, 180) * 86400;
                $periodStart  = $now - $fake->numberBetween(30, 60) * 86400;
                $periodEnd    = $now + $fake->numberBetween(1, 15) * 86400; // access continues to period end
                $trialEnd     = 0;
                $cancelledAt  = $now - $fake->numberBetween(1, 30) * 86400;
                $cancelReason = $fake->randomElement([
                    'Switching to a higher tier',
                    'No longer need the service',
                    'Too expensive',
                    'Found a better alternative',
                    'Temporary budget freeze',
                ]);
                break;

            default: // expired
                $startedAt    = $now - $fake->numberBetween(120, 365) * 86400;
                $periodStart  = $now - $fake->numberBetween(60, 120) * 86400;
                $periodEnd    = $now - $fake->numberBetween(1, 60) * 86400;
                $trialEnd     = 0;
                $cancelledAt  = 0;
                $cancelReason = '';
                break;
        }

        $subId = $ins($db->prefix('subscriptions_subscriptions'), [
            'user_id'              => $uid,
            'plan_id'              => $planId,
            'status'               => $status,
            'billing_cycle'        => $meta['cycle'],
            'started_at'           => $startedAt,
            'trial_ends_at'        => $trialEnd,
            'current_period_start' => $periodStart,
            'current_period_end'   => $periodEnd,
            'cancelled_at'         => $cancelledAt,
            'cancel_reason'        => $cancelReason,
            'gateway'              => $gateway,
            'gateway_sub_id'       => strtoupper($gateway) . '-' . strtoupper($fake->bothify('??-####-??????')),
            'coupon_id'            => 0,
            'discount_amount'      => 0.00,
            'amount_paid'          => $meta['price'],
            'auto_renew'           => $cancelledAt ? 0 : 1,
            'created_at'           => $startedAt,
            'updated_at'           => $now,
        ]);

        $subs[] = [
            'sub_id'      => $subId,
            'user_id'     => $uid,
            'plan_id'     => $planId,
            'plan_name'   => $meta['name'],
            'price'       => $meta['price'],
            'cycle'       => $meta['cycle'],
            'model'       => $meta['model'],
            'status'      => $status,
            'gateway'     => $gateway,
            'periodStart' => $periodStart,
            'periodEnd'   => $periodEnd,
            'startedAt'   => $startedAt,
        ];
    }
}

// ── Invoices, Payments & Refunds ──────────────────────────────────────────────
$invoiceSeq = 1;

foreach ($subs as $sub) {
    // Trials have no invoices yet; skip
    if ($sub['status'] === 'trial') {
        continue;
    }

    // Decide how many billing periods to show in history
    $numPeriods = match ($sub['status']) {
        'active'    => $fake->numberBetween(1, 3),
        'expired'   => $fake->numberBetween(2, 4),
        'cancelled' => $fake->numberBetween(1, 2),
        default     => 1,
    };

    for ($period = 0; $period < $numPeriods; $period++) {
        $isLatest    = ($period === $numPeriods - 1);
        $periodOffset = ($numPeriods - 1 - $period) * 30 * 86400;
        $invCreated  = $sub['periodStart'] - $periodOffset;

        // Invoice status: past invoices are always paid; latest may be open for past_due
        $invStatus  = ($sub['status'] === 'past_due' && $isLatest) ? 'open' : 'paid';

        $taxRate    = $fake->randomElement([0.0, 0.0, 0.0, 8.5, 10.0, 20.0]);
        $subtotal   = $sub['price'];
        $taxAmt     = round($subtotal * $taxRate / 100, 2);
        $total      = round($subtotal + $taxAmt, 2);
        $paidAt     = $invStatus === 'paid' ? ($invCreated + $fake->numberBetween(0, 3600)) : 0;
        $invNum     = 'INV-' . date('Ymd', $invCreated) . '-' . sprintf('%04d', $invoiceSeq++);

        $invId = $ins($db->prefix('subscriptions_invoices'), [
            'invoice_number'  => $invNum,
            'user_id'         => $sub['user_id'],
            'sub_id'          => $sub['sub_id'],
            'status'          => $invStatus,
            'subtotal'        => $subtotal,
            'tax_rate'        => $taxRate,
            'tax_amount'      => $taxAmt,
            'discount_amount' => 0.00,
            'total'           => $total,
            'currency'        => $cfg['currency'],
            'due_date'        => $invCreated + 14 * 86400,
            'paid_at'         => $paidAt,
            'period_start'    => $invCreated,
            'period_end'      => $invCreated + 30 * 86400,
            'notes'           => '',
            'created_at'      => $invCreated,
            'updated_at'      => $invCreated,
        ]);

        // Invoice line item — plan subscription fee
        $ins($db->prefix('subscriptions_invoice_items'), [
            'invoice_id'  => $invId,
            'description' => $sub['plan_name'] . ' — ' . ucfirst($sub['cycle']) . ' Subscription',
            'quantity'    => 1.00,
            'unit_price'  => $subtotal,
            'amount'      => $subtotal,
        ]);

        // Tax line item if applicable
        if ($taxAmt > 0.0) {
            $ins($db->prefix('subscriptions_invoice_items'), [
                'invoice_id'  => $invId,
                'description' => 'Tax (' . rtrim(rtrim(number_format($taxRate, 1), '0'), '.') . '%)',
                'quantity'    => 1.00,
                'unit_price'  => $taxAmt,
                'amount'      => $taxAmt,
            ]);
        }

        // Payment record
        if ($invStatus === 'paid') {
            $payStatus = $fake->randomElement(['completed', 'completed', 'completed', 'partially_refunded']);

            $payId = $ins($db->prefix('subscriptions_payments'), [
                'sub_id'         => $sub['sub_id'],
                'user_id'        => $sub['user_id'],
                'invoice_id'     => $invId,
                'gateway'        => $sub['gateway'],
                'gateway_txn_id' => strtoupper($sub['gateway'][0]) . 'TXN-' . strtoupper($fake->bothify('????-########')),
                'amount'         => $total,
                'currency'       => $cfg['currency'],
                'status'         => $payStatus,
                'payment_method' => $sub['gateway'] === 'stripe' ? 'card' : $sub['gateway'],
                'payment_data'   => json_encode([
                    'payer_email' => $fake->safeEmail(),
                    'last4'       => $fake->numerify('####'),
                    'brand'       => $fake->randomElement(['Visa', 'Mastercard', 'Amex', 'Discover']),
                    'country'     => $fake->countryCode(),
                ]),
                'ip_address'     => $fake->ipv4(),
                'paid_at'        => $paidAt,
                'created_at'     => $paidAt,
            ]);

            // Partial refund for ~25% of partially_refunded payments
            if ($payStatus === 'partially_refunded' && $fake->boolean(75)) {
                $refundAmt = round($total * $fake->randomFloat(2, 0.1, 0.5), 2);
                $ins($db->prefix('subscriptions_refunds'), [
                    'payment_id'        => $payId,
                    'sub_id'            => $sub['sub_id'],
                    'user_id'           => $sub['user_id'],
                    'amount'            => $refundAmt,
                    'reason'            => $fake->randomElement([
                        'Pro-rated refund for unused portion',
                        'Billing error — duplicate charge',
                        'Customer request — service issue',
                        'Goodwill refund',
                        'Cancelled within refund window',
                    ]),
                    'status'            => 'completed',
                    'gateway_refund_id' => 'REF-' . strtoupper($fake->bothify('??-######')),
                    'processed_by'      => 1,
                    'created_at'        => $paidAt + $fake->numberBetween(86400, 604800),
                ]);
            }
        } else {
            // Open invoice → failed payment attempt
            $ins($db->prefix('subscriptions_payments'), [
                'sub_id'         => $sub['sub_id'],
                'user_id'        => $sub['user_id'],
                'invoice_id'     => $invId,
                'gateway'        => $sub['gateway'],
                'gateway_txn_id' => '',
                'amount'         => $total,
                'currency'       => $cfg['currency'],
                'status'         => 'failed',
                'payment_method' => $sub['gateway'] === 'stripe' ? 'card' : $sub['gateway'],
                'payment_data'   => json_encode([
                    'error'        => 'card_declined',
                    'decline_code' => $fake->randomElement(['insufficient_funds', 'do_not_honor', 'expired_card', 'lost_card']),
                    'message'      => 'Your card was declined.',
                ]),
                'ip_address'     => $fake->ipv4(),
                'paid_at'        => 0,
                'created_at'     => $invCreated,
            ]);
        }
    }
}

// ── Coupons ───────────────────────────────────────────────────────────────────
$couponCodes = ['WELCOME20', 'SAVE10', 'LAUNCH30', 'HOLIDAY15', 'OLDPROMO'];

foreach ($couponCodes as $idx => $code) {
    $type      = $fake->randomElement(['percentage', 'percentage', 'fixed']);
    $value     = $type === 'percentage'
        ? (float) $fake->randomElement([10, 15, 20, 25, 30])
        : (float) $fake->randomElement([5, 10, 15, 20]);
    $isExpired  = ($idx === 3);
    $isDisabled = ($idx === 4);

    $ins($db->prefix('subscriptions_coupons'), [
        'code'              => $code,
        'name'              => $fake->catchPhrase(),
        'discount_type'     => $type,
        'discount_value'    => $value,
        'min_amount'        => $type === 'fixed' ? round($value * 1.5, 2) : 0.00,
        'max_uses'          => $fake->randomElement([0, 50, 100, 250]),
        'uses_count'        => $fake->numberBetween(0, 150),
        'max_uses_per_user' => 1,
        'applies_to'        => $fake->randomElement(['all', 'all', 'plan_ids']),
        'plan_ids'          => '[]',
        'valid_from'        => $now - $fake->numberBetween(1, 60) * 86400,
        'valid_until'       => $isExpired
            ? ($now - $fake->numberBetween(1, 30) * 86400)
            : ($now + $fake->numberBetween(30, 180) * 86400),
        'is_active'         => $isDisabled ? 0 : 1,
        'created_at'        => $now - $fake->numberBetween(30, 180) * 86400,
    ]);
}

// ── Gateway configurations (placeholder credentials) ──────────────────────────
foreach ([
    ['paypal', 'business_email', 'sandbox-demo@subscriptions.example.com'],
    ['paypal', 'sandbox_mode',   '1'],
    ['paypal', 'currency',       $cfg['currency']],
    ['stripe', 'publishable_key', 'pk_test_DEMO_FAKER_SEEDER_placeholder'],
    ['stripe', 'secret_key',      'sk_test_DEMO_FAKER_SEEDER_placeholder'],
    ['stripe', 'sandbox_mode',    '1'],
    ['stripe', 'currency',        $cfg['currency']],
    ['manual', 'instructions',    "Transfer to:\nBank: Demo National Bank\nAccount: DEMO-" . $fake->bankAccountNumber() . "\nRef: your username"],
    ['manual', 'currency',        $cfg['currency']],
] as [$gw, $key, $val]) {
    $ins($db->prefix('subscriptions_gateway_configs'), [
        'gateway'    => $gw,
        'config_key' => $key,
        'config_val' => $val,
        'is_active'  => 1,
    ]);
}

// ── Connected modules ─────────────────────────────────────────────────────────
$modId1 = $ins($db->prefix('subscriptions_connected_modules'), [
    'dirname'     => 'premdownloads',
    'name'        => 'Premium Downloads',
    'description' => 'Members-only file download module. Starter+ subscribers get limited access; Professional+ get full access.',
    'owner_uid'   => 1,
    'is_active'   => 1,
    'webhook_url' => XOOPS_URL . '/modules/premdownloads/webhook.php',
    'api_key'     => bin2hex(random_bytes(32)),
    'config'      => '{}',
    'created_at'  => $now,
]);

$modId2 = $ins($db->prefix('subscriptions_connected_modules'), [
    'dirname'     => 'memforum',
    'name'        => 'Members Forum',
    'description' => 'Exclusive community discussion forum. Professional and above subscribers gain posting rights.',
    'owner_uid'   => 1,
    'is_active'   => 1,
    'webhook_url' => XOOPS_URL . '/modules/memforum/webhook.php',
    'api_key'     => bin2hex(random_bytes(32)),
    'config'      => '{}',
    'created_at'  => $now,
]);

// Access rules: tier-based access
foreach ($planIds as $idx => $planId) {
    // First plan: limited downloads; remaining plans: full access
    $ins($db->prefix('subscriptions_module_access_rules'), [
        'module_id'   => $modId1,
        'plan_id'     => $planId,
        'access_type' => $idx === 0 ? 'limited' : 'full',
        'limit_value' => $idx === 0 ? 10 : 0,
        'xoops_group' => 0,
    ]);
    // Forum: Professional+ (index > 0) only
    if ($idx > 0) {
        $ins($db->prefix('subscriptions_module_access_rules'), [
            'module_id'   => $modId2,
            'plan_id'     => $planId,
            'access_type' => 'full',
            'limit_value' => 0,
            'xoops_group' => 0,
        ]);
    }
}

// ── Usage logs (for metered/hybrid subs) ──────────────────────────────────────
foreach ($subs as $sub) {
    if (!in_array($sub['status'], ['active', 'expired', 'past_due'], true)) {
        continue;
    }
    $numEntries = $fake->numberBetween(3, 12);
    for ($l = 0; $l < $numEntries; $l++) {
        $loggedAt = $fake->numberBetween($sub['periodStart'], $now);
        $ins($db->prefix('subscriptions_usage_logs'), [
            'sub_id'     => $sub['sub_id'],
            'user_id'    => $sub['user_id'],
            'module_id'  => $fake->randomElement([$modId1, $modId2]),
            'event_type' => $fake->randomElement(['api_call', 'api_call', 'download', 'visit']),
            'quantity'   => $fake->randomFloat(2, 1, 50),
            'unit_price' => 0.005,
            'billed'     => ($loggedAt < $sub['periodEnd'] - 86400) ? 1 : 0,
            'created_at' => $loggedAt,
        ]);
    }
}

// ── Webhooks ──────────────────────────────────────────────────────────────────
$allEvents = '["subscription.activated","subscription.renewed","subscription.cancelled","subscription.expired","payment.completed","payment.refunded","subscription.trial_ending"]';

$ins($db->prefix('subscriptions_webhooks'), [
    'url'        => 'https://webhook.site/' . $fake->uuid(),
    'events'     => $allEvents,
    'secret'     => bin2hex(random_bytes(20)),
    'is_active'  => 1,
    'last_fired' => $now - $fake->numberBetween(3600, 86400),
    'fail_count' => 0,
    'created_at' => $now - $fake->numberBetween(86400, 604800),
]);

$ins($db->prefix('subscriptions_webhooks'), [
    'url'        => 'https://accounting.example.com/api/subscriptions-events',
    'events'     => '["payment.completed","payment.refunded"]',
    'secret'     => bin2hex(random_bytes(20)),
    'is_active'  => 1,
    'last_fired' => $now - $fake->numberBetween(3600, 86400),
    'fail_count' => $fake->numberBetween(0, 2),
    'created_at' => $now - $fake->numberBetween(86400, 604800),
]);

$ins($db->prefix('subscriptions_webhooks'), [
    'url'        => 'https://old-crm.example.com/hooks/membership',
    'events'     => '["subscription.activated","subscription.cancelled"]',
    'secret'     => bin2hex(random_bytes(20)),
    'is_active'  => 0,
    'last_fired' => $now - $fake->numberBetween(604800, 2592000),
    'fail_count' => $fake->numberBetween(5, 15),
    'created_at' => $now - $fake->numberBetween(2592000, 7776000),
]);

// ── Build summary counts ───────────────────────────────────────────────────────
$statusCounts = array_count_values(array_column($subs, 'status'));

// ── Output ────────────────────────────────────────────────────────────────────
$hasErrors = !empty($errors);
$totalInserts = array_sum($counts);

if (PHP_SAPI === 'cli') {
    echo "\n=== Subscriptions Faker Seeder ===\n";
    echo "Locale: {$cfg['locale']} | Plans: {$cfg['plans']} | Users: " . count($userIds) . " | Subs: " . count($subs) . "\n";
    printf("%d total rows inserted across %d tables.\n", $totalInserts, count($counts));
    if ($hasErrors) {
        echo "ERRORS:\n";
        foreach ($errors as $e) {
            echo "  - $e\n";
        }
        exit(1);
    }
    foreach ($counts as $tbl => $n) {
        echo "  $tbl: $n rows\n";
    }
    echo "\nDone. Run again for fresh randomized data.\n\n";
    exit(0);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Subscriptions – Faker Seeder</title>
  <style>
    *{box-sizing:border-box}
    body{font-family:system-ui,sans-serif;max-width:900px;margin:40px auto;padding:0 24px;color:#1f2937;line-height:1.6}
    h1{color:<?= $hasErrors ? '#b91c1c' : '#1a6b3c' ?>;margin-bottom:4px}
    h2{color:#374151;font-size:1.1rem;margin:28px 0 8px;border-bottom:1px solid #e5e7eb;padding-bottom:4px}
    .meta{color:#6b7280;font-size:.9em;margin-bottom:20px}
    .banner{padding:14px 20px;border-radius:8px;margin:20px 0;border-left:5px solid;font-weight:500}
    .ok{background:#dcfce7;border-color:#16a34a;color:#14532d}
    .err{background:#fee2e2;border-color:#dc2626;color:#7f1d1d}
    .grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin:20px 0}
    .card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px}
    .card h3{margin:0 0 8px;font-size:.95rem;color:#0f172a}
    .card .num{font-size:2rem;font-weight:700;color:#1a6b3c;line-height:1}
    .card .label{font-size:.8rem;color:#6b7280}
    .errs{background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;padding:12px 16px;margin:8px 0}
    .errs pre{margin:0;font-size:.8rem;white-space:pre-wrap;word-break:break-all;color:#991b1b}
    table{width:100%;border-collapse:collapse;font-size:.88rem}
    th{background:#f1f5f9;text-align:left;padding:6px 10px;color:#374151;font-weight:600}
    td{padding:5px 10px;border-bottom:1px solid #e5e7eb}
    .badge{display:inline-block;padding:1px 7px;border-radius:9999px;font-size:.75rem;font-weight:600}
    .bg-green{background:#dcfce7;color:#15803d}
    .bg-yellow{background:#fef9c3;color:#a16207}
    .bg-red{background:#fee2e2;color:#b91c1c}
    .bg-blue{background:#dbeafe;color:#1e40af}
    .bg-gray{background:#f3f4f6;color:#6b7280}
    a.btn{display:inline-block;padding:10px 22px;border-radius:6px;text-decoration:none;font-weight:600;margin-right:10px;margin-top:16px}
    .btn-primary{background:#1a6b3c;color:#fff}.btn-primary:hover{background:#145230}
    .btn-sec{background:#e5e7eb;color:#374151}.btn-sec:hover{background:#d1d5db}
    footer{margin-top:36px;color:#9ca3af;font-size:.8rem;border-top:1px solid #e5e7eb;padding-top:14px}
  </style>
</head>
<body>

<h1><?= $hasErrors ? '&#9888; Seeded with errors' : '&#10003; Faker seed completed' ?></h1>
<div class="meta">
  Locale: <strong><?= htmlspecialchars($cfg['locale']) ?></strong> &nbsp;|&nbsp;
  <?= $totalInserts ?> rows inserted &nbsp;|&nbsp;
  <?= $hasErrors ? count($errors) . ' error(s)' : 'No errors' ?>
</div>

<?php if ($hasErrors): ?>
<div class="banner err">Some statements failed. Data may be partially seeded.</div>
<?php foreach ($errors as $e): ?>
<div class="errs"><pre><?= htmlspecialchars($e, ENT_QUOTES) ?></pre></div>
<?php endforeach; ?>
<?php else: ?>
<div class="banner ok">Database populated with randomized demo data. Every section of the module is now fully showcased.</div>
<?php endif; ?>

<h2>Summary</h2>
<div class="grid">
  <div class="card">
    <div class="num"><?= count($planIds) ?></div>
    <div class="label">Plans seeded</div>
  </div>
  <div class="card">
    <div class="num"><?= count($subs) ?></div>
    <div class="label">Subscriptions (<?= count($userIds) ?> users)</div>
  </div>
  <div class="card">
    <div class="num"><?= $counts['invoices'] ?? 0 ?></div>
    <div class="label">Invoices + <?= $counts['invoice_items'] ?? 0 ?> line items</div>
  </div>
  <div class="card">
    <div class="num"><?= $counts['payments'] ?? 0 ?></div>
    <div class="label">Payments</div>
  </div>
  <div class="card">
    <div class="num"><?= $counts['refunds'] ?? 0 ?></div>
    <div class="label">Refunds</div>
  </div>
  <div class="card">
    <div class="num"><?= $counts['usage_logs'] ?? 0 ?></div>
    <div class="label">Usage log entries</div>
  </div>
</div>

<h2>Subscription lifecycle states</h2>
<table>
  <tr><th>Status</th><th>Count</th><th></th></tr>
  <?php foreach (['active' => 'bg-green', 'trial' => 'bg-blue', 'past_due' => 'bg-yellow', 'cancelled' => 'bg-gray', 'expired' => 'bg-red'] as $s => $cls): ?>
  <?php if (!empty($statusCounts[$s])): ?>
  <tr>
    <td><span class="badge <?= $cls ?>"><?= $s ?></span></td>
    <td><?= $statusCounts[$s] ?></td>
    <td style="color:#9ca3af;font-size:.8em"><?= round($statusCounts[$s] / count($subs) * 100) ?>% of total</td>
  </tr>
  <?php endif; ?>
  <?php endforeach; ?>
</table>

<h2>Row counts per table</h2>
<table>
  <tr><th>Table</th><th>Rows</th></tr>
  <?php foreach ($counts as $tbl => $n): ?>
  <tr><td><?= htmlspecialchars($tbl) ?></td><td><?= $n ?></td></tr>
  <?php endforeach; ?>
</table>

<h2>Next steps</h2>
<ul>
  <li><a href="<?= XOOPS_URL ?>/modules/subscriptions/">Plans page</a></li>
  <li><a href="<?= XOOPS_URL ?>/modules/subscriptions/dashboard.php">User dashboard</a></li>
  <li><a href="<?= XOOPS_URL ?>/modules/subscriptions/invoices.php">Invoice list</a></li>
  <li><a href="<?= XOOPS_URL ?>/modules/subscriptions/admin/">Admin panel</a></li>
  <li>Try coupon codes: <strong>WELCOME20</strong>, <strong>SAVE10</strong>, <strong>LAUNCH30</strong></li>
</ul>
<p style="color:#6b7280;font-size:.88rem">
  Gateway credentials in the database are placeholders — replace with real sandbox credentials before testing payments.
</p>

<a class="btn btn-primary" href="<?= XOOPS_URL ?>/modules/subscriptions/admin/">Admin Panel</a>
<a class="btn btn-sec"     href="<?= XOOPS_URL ?>/modules/subscriptions/plans.php">View Plans</a>
<a class="btn btn-sec"     href="?confirm=yes&plans=<?= $cfg['plans'] ?>&users=<?= $cfg['users'] ?>&subs_per_user=<?= $cfg['subs_per_user'] ?>">Re-seed</a>

<footer>
  Subscriptions Faker Seeder &mdash; locale: <?= htmlspecialchars($cfg['locale']) ?> &mdash;
  seed.sql is now deprecated; this file is the authoritative seeder.
</footer>
</body>
</html>
