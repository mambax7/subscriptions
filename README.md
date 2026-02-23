![alt XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)
# Subscriptions — Paid Memberships & Service Subscriptions for XOOPS

[![Version](https://img.shields.io/badge/version-1.0.0--beta.1-blue.svg)](CHANGELOG.md)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://php.net)
[![XOOPS](https://img.shields.io/badge/xoops-%3E%3D2.5.12-brightgreen.svg)](https://xoops.org)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](LICENSE)
[![GitHub Actions](https://github.com/mambax7/subscriptions/actions/workflows/tests.yml/badge.svg)](https://github.com/mambax7/subscriptions/actions)

> Transform your XOOPS site into a **membership and subscription platform** — sell access to your
> modules via flexible subscription plans, manage billing and invoices, and let subscribers
> self-manage their accounts from a clean dashboard.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Setup](#quick-setup-5-minutes)
- [Configuration Reference](#configuration-reference)
- [Payment Gateways](#payment-gateways)
- [Integrating Your Module](#integrating-your-module)
- [Blocks](#blocks)
- [Cron Job](#cron-job)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### Subscription Plans
- Monthly, annual, weekly, daily, quarterly, and one-time billing cycles
- Trial periods (configurable days) per plan
- Featured plan highlighting for marketing emphasis
- Per-plan feature lists with bullet descriptions on the checkout page
- Flat-rate, usage-based, and hybrid (base fee + usage) pricing models
- Maximum subscriber cap per plan (0 = unlimited)

### Payment Processing
- **Stripe** — SCA-compliant Checkout Sessions and Payment Intents
- **PayPal** — Payments Standard for one-time and recurring subscriptions
- **Manual / Offline** — Wire transfers, cheques, purchase orders (admin-recorded)
- Pluggable gateway architecture — add custom gateways with minimal code
- HMAC-SHA256 signed webhooks and IPN handling for all gateways

### Invoicing & Billing
- Automatic invoice generation per payment event
- Line-item invoices with configurable tax rate
- Printable HTML invoice view per subscription
- Configurable invoice number prefix (e.g. `INV-0001`)
- Full and partial refund support with audit trail

### Coupons & Discounts
- Percentage-based or fixed-amount discount codes
- Per-coupon and per-user usage limits
- Valid date windows (valid from / valid until)
- Restrict coupons to specific plans or apply globally

### Access Control for Third-Party Modules
- Register any XOOPS module and gate its content behind a subscription
- Three access modes: **full** (unlimited), **limited** (quota per period), **metered** (pay-per-use)
- Optional XOOPS group sync — automatically add/remove users from groups on activation/expiry
- Lightweight one-file SDK (`sdk/SubscriptionsSDK.php`) — see [Integrating Your Module](#integrating-your-module)

### Webhook & Event System
| Event | Fired when |
|---|---|
| `subscription.activated` | New subscription becomes active or trial starts |
| `subscription.renewed` | Subscription successfully renews for a new billing period |
| `subscription.cancelled` | Admin or user cancels a subscription |
| `subscription.expired` | Subscription reaches its end date without renewal |
| `subscription.trial_ending` | 24 hours before a trial expires |
| `payment.completed` | A payment is recorded as successful |
| `payment.refunded` | A payment is refunded |

### User Dashboard
- Active subscription status, next renewal date, and trial countdown
- Full payment history per subscription
- Downloadable invoice list
- Self-service cancellation

### Admin Dashboard
- At-a-glance stats: active subscriptions, trials, plan count, month-to-date revenue
- Full management of plans, subscriptions, payments, coupons, gateways, connected modules, and webhooks
- Module-level access rule configuration (plan → module → access type → XOOPS group)
- Test data import/export for development (toggleable)

---

## Requirements

| Requirement | Minimum |
|---|---|
| XOOPS | 2.5.12 |
| PHP | 8.2 |
| MySQL | 5.7 or MariaDB 10.3 |
| XMF library | Bundled with XOOPS 2.5.12+ |

> **Database charset note:** All 14 module tables are created with `DEFAULT CHARSET=utf8mb4`.
> Your XOOPS database itself does not need to be `utf8mb4`, but the MySQL user must have
> permission to create `utf8mb4` tables.

---

## Installation

### Method 1 — Upload & Install (recommended)

1. Download the latest release ZIP from [GitHub Releases](https://github.com/mambax7/subscriptions/releases).
2. Unzip and upload the `subscriptions/` folder to `<xoops-root>/modules/`.
3. Log into your XOOPS admin panel.
4. Go to **System → Modules** and click **Install** next to *Subscriptions*.
5. After installation completes, go to **Subscriptions → Admin** to begin setup.

### Method 2 — Git Clone

```bash
cd <xoops-root>/modules
git clone https://github.com/mambax7/subscriptions.git subscriptions
```

Then install through XOOPS admin as in step 3 above.

---

## Quick Setup (5 minutes)

Follow these steps after installation to accept your first subscription payment.

### Step 1 — Configure a payment gateway

**Admin → Subscriptions → Gateways**

| Gateway | What to enter |
|---|---|
| **Stripe** | Secret Key + Publishable Key (from [Stripe Dashboard](https://dashboard.stripe.com/apikeys)) |
| **PayPal** | Business email address; enable Sandbox for testing |
| **Manual** | Nothing — admin records payments by hand |

Set your active gateway in **Preferences → Active Gateway**.

### Step 2 — Set currency and tax

**Admin → Subscriptions → Preferences**

| Setting | Example |
|---|---|
| Currency | `USD` |
| Currency symbol | `$` |
| Tax rate | `10` *(for 10%)* |
| Invoice prefix | `INV-` |

### Step 3 — Create your first plan

**Admin → Subscriptions → Plans → Add Plan**

Required fields:

| Field | Example |
|---|---|
| Plan name | *Basic Monthly* |
| Price | *9.99* |
| Billing cycle | *monthly* |
| Active | *Yes* |

Add feature descriptions in the **Plan Features** sub-section (one per line; shown as bullet
points at checkout).

### Step 4 — Test the checkout

Visit `https://yoursite.com/modules/subscriptions/` as a logged-in user, select the plan,
and complete a payment using your gateway's sandbox/test credentials.

### Step 5 — Set up the cron job

See the [Cron Job](#cron-job) section below.

---

## Configuration Reference

**Admin → Subscriptions → Preferences**

| Key | Type | Default | Description |
|---|---|---|---|
| `currency` | select | `USD` | ISO 4217 currency code used in checkout and invoices |
| `currency_symbol` | text | `$` | Symbol displayed next to prices in the UI |
| `tax_rate` | float | `0` | Tax percentage applied at checkout (`10` = 10%) |
| `trial_days` | int | `0` | Default trial days; individual plans override this |
| `invoice_prefix` | text | `INV-` | Prefix for invoice numbers |
| `notify_email` | yesno | `Yes` | Send email notifications on subscription events |
| `active_gateway` | select | `paypal` | Default payment gateway at checkout |
| `allow_multi_subs` | yesno | `No` | Allow one user to hold multiple active subscriptions simultaneously |
| `grace_period_days` | int | `3` | Days after expiry to retry payment before hard cancellation |
| `displaySampleButton` | yesno | `Yes` | Show test-data import/export buttons in admin |
| `displayDeveloperTools` | yesno | `No` | Show debug panels in admin (development use only) |
| `cron_secret` | text | *(auto)* | Token required in the cron URL — auto-generated on install |

---

## Payment Gateways

### Stripe

- Uses **Checkout Sessions** (SCA / 3D Secure compliant)
- Supports one-time payments and recurring subscriptions via `mode=subscription`
- Webhook receiver URL: `https://yoursite.com/modules/subscriptions/ipn.php`
- Register this URL in **Stripe Dashboard → Developers → Webhooks**

### PayPal

- Uses **Payments Standard** (hosted redirect to PayPal)
- Supports one-time (`_xclick`) and recurring (`_xclick_subscriptions`) payments
- IPN receiver URL: `https://yoursite.com/modules/subscriptions/ipn.php`
- Enable IPN in **PayPal Account Settings → Instant Payment Notifications**

### Manual / Offline

- No external API — admin manually creates and marks payments as completed
- Suitable for wire transfers, cheques, or purchase orders
- Users see payment instructions after checkout

### Custom Gateways

To add a custom payment gateway:

1. Create `class/Gateway/YourGateway.php` extending `AbstractGateway` and implementing `GatewayInterface`.
2. Implement: `getIdentifier()`, `getName()`, `initiatePayment()`, `verifyWebhook()`, `supportsRecurring()`.
3. Register it in `GatewayFactory::create()`.
4. Add its configuration form in `admin/gateways.php`.

---

## Integrating Your Module

The **SubscriptionsSDK** enables any XOOPS module to check and enforce subscription access.

### Minimal integration (3 lines)

```php
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
$sdk = SubscriptionsSDK::getInstance('your_module_dirname');
$sdk->enforceAccess(isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0);
```

Wrap in `file_exists()` to keep Subscriptions an **optional** dependency:

```php
if (file_exists(XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
    $sdk = SubscriptionsSDK::getInstance('your_module_dirname');
    $sdk->enforceAccess(isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0);
}
```

### SDK method summary

| Method | Returns | Purpose |
|---|---|---|
| `getInstance(string $dirname)` | `SubscriptionsSDK` | Get the SDK instance for your module |
| `hasAccess(int $uid)` | `bool` | Check whether the user has an active subscription |
| `enforceAccess(int $uid)` | `void` | Redirect to plans page if access denied (never returns on denial) |
| `getAccessType(int $uid)` | `string\|null` | `'full'`, `'limited'`, `'metered'`, or `null` |
| `recordUsage(int $uid, string $event, float $qty)` | `bool` | Log a usage event for metered billing |
| `getRemainingQuota(int $uid, string $event)` | `array\|null` | `['limit', 'used', 'remaining']` for quota-based rules |
| `getSubscription(int $uid)` | `array\|null` | Full subscription details for the user |
| `getPlans()` | `array` | All active plans with checkout URLs |
| `getPlansWithAccess()` | `array` | Plans that grant access to your module |
| `getPlansUrl()` | `string` | URL to the Subscriptions plans page |
| `getDashboardUrl()` | `string` | URL to the user subscription dashboard |
| `verifyWebhook(string $payload, string $sig)` | `bool` | Verify an incoming webhook HMAC signature |

**Full integration guide** (registration, access types, webhooks, quota tracking):
[`docs/integration-guide.md`](docs/integration-guide.md)

---

## Blocks

| Block | Description | Options |
|---|---|---|
| **Featured Plans** | Displays a grid or list of active subscription plans with checkout links | *Items count* (default: 3), *Layout* (grid / list) |

Install via **XOOPS Admin → System → Blocks → Subscriptions → Featured Plans**.

---

## Cron Job

The cron job handles automated subscription lifecycle tasks:

| Task | Description |
|---|---|
| **Expiry check** | Marks subscriptions as `expired` when their period end date has passed |
| **Trial ending alert** | Fires `subscription.trial_ending` event 24 hours before a trial expires |
| **Period-end cancellations** | Finalises subscriptions scheduled to cancel at period end |
| **Group sync** | Removes expired subscribers from their assigned XOOPS groups |

### Setting up the cron

**Recommended: daily at 3 AM**

```cron
0 3 * * * curl -s "https://yoursite.com/modules/subscriptions/cron.php?secret=YOUR_CRON_SECRET" > /dev/null
```

Alternatively, trigger via PHP CLI:

```bash
php /path/to/xoops/modules/subscriptions/cron.php
```

Find your `YOUR_CRON_SECRET` value in **Admin → Subscriptions → Preferences → Cron Secret**.

> **Without a cron job**, subscriptions will not expire automatically and trial-ending
> notifications will not fire. For low-traffic sites the cron can be triggered by a
> page-view-based scheduler if your host does not support cron.

---

## Contributing

Pull requests and issues are welcome.

```bash
# Clone and install dev dependencies
git clone https://github.com/mambax7/subscriptions.git
cd subscriptions
composer install

# Run the test suite
vendor/bin/phpunit

# Run static analysis (PHPStan level 6)
vendor/bin/phpstan analyse

# Check code style (PSR-12)
vendor/bin/php-cs-fixer fix --dry-run
```

Please use the GitHub [issue templates](.github/ISSUE_TEMPLATE/) for bug reports and feature
requests, and the [pull request template](.github/PULL_REQUEST_TEMPLATE.md) for contributions.

---

## License

[GNU General Public License v2 or later](LICENSE)
© 2026 Subscriptions Development Team
