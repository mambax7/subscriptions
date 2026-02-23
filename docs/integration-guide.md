![alt XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)
# Integrating Your XOOPS Module with Subscriptions

This guide explains how to gate content in your XOOPS module behind Subscriptions
subscription plans. The integration takes about 15 minutes and requires no changes
to Subscriptions itself.

---

## TL;DR — Working in 3 minutes

**Admin side (one time):**
Go to **Subscriptions → Connected Modules → Add Module**, register your module, set at
least one access rule (plan → your module → `full`), and copy the generated API key.

**Code side — add to your module's entry point (e.g. `index.php`):**

```php
if (file_exists(XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
    $sdk = SubscriptionsSDK::getInstance('your_module_dirname');
    $sdk->enforceAccess(isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0);
}
```

That's it. Users without an active subscription are redirected to the plans page.
Read on for quota-based access, metered billing, upgrade prompts, and webhook events.

---

## How it works

Subscriptions acts as the central subscription authority. You register your module
inside the Subscriptions admin panel, configure which plans grant access, and then
call the Subscriptions SDK from your own module code to check whether the current
user is allowed in.

```
[Your Module] ──SDK call──► [Subscriptions] ──checks──► [Subscription DB]
                                   │
                                   └──► returns: allowed / denied / quota
```

---

## Step 1 — Register your module in Subscriptions

Log into the XOOPS admin panel and go to **Subscriptions → Connected Modules → Add Module**.

Fill in:

| Field | Value |
|---|---|
| **Module Dirname** | The exact `dirname` from your module's `xoops_version.php` (e.g. `publisher`) |
| **Display Name** | Human-readable name shown in the admin UI |
| **Description** | Short description of what your module does |
| **Webhook URL** | Optional — URL in your module that receives subscription events (see Step 4) |
| **Active** | Yes |

After saving, Subscriptions generates an **API Key** for your module. Copy it — you
will store it in your module's configuration.

---

## Step 2 — Configure access rules

Still in the Subscriptions admin, go to **Connected Modules → Access Rules** for your
newly registered module. Add one rule per plan that should have access.

Each rule has three settings:

| Setting | Options | Meaning |
|---|---|---|
| **Plan** | Any active plan | Which plan this rule applies to |
| **Access Type** | `full` | Unlimited access — user can use the module freely |
| | `limited` | Quota-based — user gets N units per billing period |
| | `metered` | Pay-per-use — each action is logged and billed |
| **Limit Value** | Integer | For `limited`: the quota (e.g. 50 downloads/month). Ignored for `full`. |
| **XOOPS Group** | Group ID or none | Optional: automatically add the user to this XOOPS group when their subscription becomes active, and remove them when it expires |

A user needs **both** an active subscription **and** a matching access rule to gain
entry. If no rule exists for a plan, that plan does not grant access to your module.

---

## Step 3 — Add the SDK to your module

### 3.1 — Load the SDK

At the top of any file where you want to check access (typically your module's
`index.php` or a preload), add:

```php
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
```

That single `require_once` is all you need. The SDK bootstraps the Subscriptions
autoloader internally.

### 3.2 — Get an SDK instance

```php
$sdk = SubscriptionsSDK::getInstance('your_module_dirname');
```

Replace `your_module_dirname` with the exact dirname you registered in Step 1.
The SDK is a singleton — calling `getInstance()` multiple times is free.

### 3.3 — Check access

**Option A — Check and handle manually:**
```php
$uid = isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;

if (!$sdk->hasAccess($uid)) {
    // Show a "subscribe to access this" message, or redirect
    redirect_header($sdk->getPlansUrl(), 3, 'A membership is required to access this content.');
    exit;
}
```

**Option B — Let Subscriptions handle the redirect automatically:**
```php
$uid = isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
$sdk->enforceAccess($uid);
// Code below this line only runs if access is granted
```

`enforceAccess()` redirects to the Subscriptions plans page automatically with a
message explaining which module requires a subscription. It never returns if access
is denied.

> **Note:** Site administrators always pass the access check regardless of
> subscription status.

---

## Step 4 — Access types in detail

### Full access

The simplest case. The user either has access or they don't.

```php
if ($sdk->hasAccess($uid)) {
    // show content
}
```

### Limited access (quota)

The user has a fixed number of actions (e.g. downloads) per billing period.
Check and deduct quota together:

```php
$quota = $sdk->getRemainingQuota($uid, 'download');

if ($quota === null || $quota['remaining'] <= 0) {
    // No subscription, or quota exhausted
    echo 'You have used all your downloads for this billing period.';
    exit;
}

// Proceed with the download, then record it
serveFile($fileId);
$sdk->recordUsage($uid, 'download');
```

`getRemainingQuota()` returns an array:

```php
[
    'limit'     => 50,    // total quota for the billing period
    'used'      => 23,    // units consumed so far
    'remaining' => 27,    // units left (never negative)
]
```

Or `null` if the user has no active subscription or the rule is not `limited` type.

### Metered access (pay-per-use)

Every action is logged for billing. Just record each usage event:

```php
// Check they have an active subscription at all
if (!$sdk->hasAccess($uid)) {
    redirect_header($sdk->getPlansUrl(), 2, 'Subscription required.');
    exit;
}

// Do the work
processApiCall($request);

// Log the usage
$sdk->recordUsage($uid, 'api_call');
```

Valid event type strings: `visit`, `download`, `api_call`, or any string that
makes sense for your module. Be consistent — the same string is used in quota
lookups and usage reports.

---

## Step 5 — Show upgrade prompts

When a user is denied, show them what plans would give them access:

```php
if (!$sdk->hasAccess($uid)) {
    $plans = $sdk->getPlansWithAccess();
    // $plans is an array of plans that include access to your module
    foreach ($plans as $plan) {
        echo '<a href="' . $plan['checkout_url'] . '">'
           . 'Subscribe to ' . htmlspecialchars($plan['name'])
           . ' — ' . $plan['price'] . ' ' . $plan['currency']
           . ' / ' . $plan['billing_cycle']
           . '</a>';
    }
}
```

Each plan in the array has:

```php
[
    'plan_id'       => 2,
    'name'          => 'Professional',
    'price'         => 29.99,
    'currency'      => 'USD',
    'billing_cycle' => 'monthly',
    'access_type'   => 'full',
    'checkout_url'  => 'https://example.com/modules/subscriptions/checkout.php?plan=2',
]
```

To show **all** available plans (not just those with access to your module):

```php
$allPlans = $sdk->getPlans();
```

---

## Step 6 — Receive webhook events (optional)

When a subscription is created, renewed, cancelled, or expires, Subscriptions can
POST a JSON payload to your module's webhook URL (the one you set in Step 1).

### 6.1 — Create a webhook endpoint

Create `modules/your_module/webhook.php`:

```php
<?php
// No XOOPS session needed — this is called by Subscriptions server-to-server
define('XOOPS_ROOT_PATH', dirname(dirname(dirname(__FILE__))));
require_once XOOPS_ROOT_PATH . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';

$sdk = SubscriptionsSDK::getInstance('your_module_dirname');

// Read the raw POST body
$payload   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SUBSCRIPTIONS_SIGNATURE'] ?? '';

// Verify the signature using your module's API key
if (!$sdk->verifyWebhook($payload, $signature)) {
    http_response_code(401);
    exit('Invalid signature');
}

$event = json_decode($payload, true);
$eventType = $event['event'] ?? '';

switch ($eventType) {
    case 'subscription.activated':
    case 'subscription.renewed':
        $userId = (int)($event['user_id'] ?? 0);
        // Grant access, send welcome email, provision resources, etc.
        grantModuleAccess($userId);
        break;

    case 'subscription.cancelled':
    case 'subscription.expired':
        $userId = (int)($event['user_id'] ?? 0);
        // Revoke access, archive user data, etc.
        revokeModuleAccess($userId);
        break;
}

http_response_code(200);
echo 'OK';
```

### 6.2 — Verify signatures

The `verifyWebhook()` method computes `sha256=HMAC(payload, api_key)` and compares
it with the `X-Subscriptions-Signature` header using a timing-safe comparison.
**Always verify before trusting the payload.**

### 6.3 — Webhook event payload structure

```json
{
    "event":        "subscription.activated",
    "user_id":      42,
    "sub_id":       7,
    "plan_id":      3,
    "plan_name":    "Professional",
    "status":       "active",
    "period_start": 1704067200,
    "period_end":   1706745600,
    "timestamp":    1704067201
}
```

Available event types:

| Event | Fired when |
|---|---|
| `subscription.activated` | A new subscription becomes active (after payment or trial start) |
| `subscription.renewed` | A subscription successfully renews for a new billing period |
| `subscription.cancelled` | Admin or user cancels a subscription |
| `subscription.expired` | A subscription reaches its end date without renewal |
| `payment.completed` | A payment is recorded as successful |
| `payment.refunded` | A payment is refunded |

---

## Step 7 — Get subscription info for display

Show the current user their plan name, expiry date, and remaining quota in your
module's UI:

```php
$sub = $sdk->getSubscription($uid);

if ($sub) {
    echo 'Plan: '    . htmlspecialchars($sub['plan_name']);
    echo 'Status: '  . htmlspecialchars($sub['status']);
    echo 'Renews: '  . date('Y-m-d', $sub['period_end']);
    if ($sub['is_trial']) {
        echo ' (Trial)';
    }
} else {
    echo 'No active subscription. <a href="' . $sdk->getPlansUrl() . '">See plans</a>';
}
```

`getSubscription()` returns:

```php
[
    'sub_id'        => 7,
    'status'        => 'active',       // active | trial | past_due | cancelled | expired
    'plan_id'       => 3,
    'plan_name'     => 'Professional',
    'billing_cycle' => 'monthly',
    'period_start'  => 1704067200,     // Unix timestamp
    'period_end'    => 1706745600,     // Unix timestamp
    'auto_renew'    => true,
    'is_trial'      => false,
]
```

---

## Complete minimal example

`modules/wf_downloads/index.php` (abbreviated):

```php
<?php
require_once XOOPS_ROOT_PATH . '/include/cp_header.php'; // or your module bootstrap

// ── Subscriptions integration ───────────────────────────────────────────
if (file_exists(XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
    $sdk = SubscriptionsSDK::getInstance('wf_downloads');
    $uid = isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
    $sdk->enforceAccess($uid); // redirects if no active subscription
}
// ── end Subscriptions ───────────────────────────────────────────────────

// ... rest of your module
```

Wrapping in `file_exists()` makes Subscriptions an **optional** dependency — your
module continues to work for sites that don't have Subscriptions installed.

---

## SDK method reference

| Method | Returns | Description |
|---|---|---|
| `getInstance(string $dirname)` | `SubscriptionsSDK` | Get/create the SDK instance for your module |
| `hasAccess(int $uid)` | `bool` | `true` if user has an active subscription with an access rule for your module |
| `enforceAccess(int $uid)` | `void` | Redirects to plans page if access denied. Never returns on denial. |
| `getAccessType(int $uid)` | `string\|null` | `'full'`, `'limited'`, `'metered'`, or `null` if no access |
| `recordUsage(int $uid, string $event, float $qty)` | `bool` | Log a usage event for billing |
| `getRemainingQuota(int $uid, string $event)` | `array\|null` | `['limit', 'used', 'remaining']` for limited rules |
| `getSubscription(int $uid)` | `array\|null` | Subscription details for the user |
| `getPlans()` | `array` | All active plans with checkout URLs |
| `getPlansWithAccess()` | `array` | Only plans that grant access to your module |
| `getPlansUrl()` | `string` | URL to the Subscriptions plans page |
| `getDashboardUrl()` | `string` | URL to the user subscription dashboard |
| `verifyWebhook(string $payload, string $sig)` | `bool` | Verify an incoming webhook signature |

---

## Checklist

- [ ] Module registered in Subscriptions admin (Connected Modules)
- [ ] API key copied into your module config
- [ ] At least one access rule created (plan → your module)
- [ ] `require_once` for the SDK added to your module's entry point(s)
- [ ] `hasAccess()` or `enforceAccess()` called before serving protected content
- [ ] For `limited` access: `getRemainingQuota()` checked and `recordUsage()` called after each action
- [ ] Webhook endpoint created and URL set in Subscriptions (if using events)
- [ ] Webhook signature verified with `verifyWebhook()` before trusting payload
- [ ] SDK `require_once` wrapped in `file_exists()` to keep Subscriptions optional
