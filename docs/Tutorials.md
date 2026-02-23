![alt XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)
# Subscriptions — Tutorials

> **Version:** 1.0.0 · **Applies to:** XOOPS 2.5.12+, PHP 8.2+
>
> Features marked **[Future]** are planned but not yet implemented in v1.0.0.

---

## Who should read what

| You are… | Read… |
|---|---|
| A subscriber buying or managing a plan | [Part A — End User Guide](#part-a--end-user-guide) |
| A site owner setting up the module | [Part B — Site Owner Guide](#part-b--site-owner-admin-guide) |
| A developer adding subscriptions to a new module | [Part C — Module Developer Guide](#part-c--module-developer-guide) |
| A developer retrofitting an existing module | [Part C3 — Retrofitting an existing module](#c3--retrofitting-an-existing-module) |

---

# Part A — End User Guide

## A1) Browsing plans

**URL:** `https://yoursite.com/modules/subscriptions/`

The plans page lists all active subscription tiers side by side. Each plan card shows:

- Plan name and description
- Price and billing cycle (monthly, annual, one-time, etc.)
- Trial period badge (if the plan includes a free trial)
- Feature list — bullet points describing what you get
- **Subscribe Now** button

> **Tip:** Plans marked *Featured* are highlighted and typically represent the best value.
> If you are already subscribed to a plan, a badge on that card confirms your current plan.

---

## A2) Checkout

**How to reach it:** Click **Subscribe Now** on any plan card.

The checkout page shows:

1. **Plan summary** — name, price, billing cycle, and included features
2. **Coupon code field** — enter a discount code if you have one (see A3)
3. **Price breakdown** — base price, discount (if coupon applied), tax (if configured), and final total
4. **Trial notice** — if the plan has a free trial, the checkout confirms when your first charge will occur
5. **Payment gateway selection** — choose Stripe (card), PayPal, or Manual/Offline
6. **Proceed to Payment** button

After clicking **Proceed to Payment** you are redirected to the selected payment gateway
(Stripe Checkout or PayPal). Complete payment there and you will be returned to the site
with your subscription active.

> **Note:** Your card details are entered directly on Stripe's or PayPal's hosted page —
> never on this site. This keeps your payment data secure and PCI-compliant.

---

## A3) Using a coupon code

On the checkout page, enter your code in the **Coupon Code** field and click **Apply**.

The system validates the code and, if valid, shows:
- The discount amount or percentage
- The updated price breakdown

Coupon codes may be:
- **Percentage discounts** — e.g. `WELCOME20` = 20% off
- **Fixed-amount discounts** — e.g. `SAVE5` = $5 off
- **Plan-specific** — only valid on certain plans (an error message will tell you if your code
  doesn't apply to the selected plan)
- **Limited use** — if a code has reached its maximum number of uses, it will be rejected

---

## A4) My subscription dashboard

**URL:** `https://yoursite.com/modules/subscriptions/dashboard.php`

After subscribing, this is your home base. It shows:

### Active subscription card
| Field | Description |
|---|---|
| Plan name | The plan you are subscribed to |
| Status | `active`, `trial`, `past due`, `cancelled`, or `expired` |
| Renewal date | When your next payment is due (or when access ends if cancelled) |
| Trial end date | Shown only if you are in a trial period |
| Cancel button | Cancel your subscription (see A6) |

### Payment history table
A list of all payments made, with date, amount, gateway used, status, and a link to the
invoice for each payment.

### Invoice list
All invoices for your account. Each row has a **View** button to open the invoice detail page.

---

## A5) Invoices and receipts

**URL:** `https://yoursite.com/modules/subscriptions/invoices.php`

Your invoice list shows all invoices in reverse-chronological order with:
- Invoice number (e.g. `INV-0001`)
- Date issued
- Total amount
- Status: `paid`, `open`, `void`
- **View** button

Clicking **View** opens the invoice detail page, which shows:
- Your name and email (Bill To)
- Line items (plan name, price, tax)
- Total paid
- Payment status badge

Use your browser's **Print** button (or Ctrl+P / Cmd+P) to save or print the invoice as PDF.
The invoice page includes print-optimised CSS that hides navigation and UI chrome.

---

## A6) Cancelling a subscription

1. Go to your dashboard (`dashboard.php`).
2. Click **Cancel Subscription** in the active subscription card.
3. Confirm the cancellation when prompted.

**What happens after cancellation:**

- Your subscription is scheduled to cancel at the **end of the current billing period**.
- You keep access until that date — you are not cut off immediately.
- Your subscription status changes to `cancelled`; the renewal date shown is now your
  last day of access.
- No further charges will be made.

> **Need a refund?** Contact the site administrator. Refunds are managed by the admin in
> the Payments section and processed through the original payment gateway.

---

# Part B — Site Owner (Admin) Guide

## B1) First-time setup checklist

After installing the module, work through these steps in order:

- [ ] **B2** Configure at least one payment gateway
- [ ] **B3** Set currency, tax rate, and invoice prefix in Preferences
- [ ] **B4** Create at least one subscription plan
- [ ] **B5** (Optional) Register a connected module and create access rules
- [ ] **B6** Test the checkout flow as a regular user
- [ ] **B7** Set up the cron job for automated expiry handling

---

## B2) Configure payment gateways

**Admin → Subscriptions → Gateways**

### Stripe
1. Create a Stripe account at [stripe.com](https://stripe.com) if you don't have one.
2. Go to **Stripe Dashboard → Developers → API Keys** and copy your **Secret key** and
   **Publishable key**.
3. In the Subscriptions gateway form, paste both keys.
4. Set **Mode** to *Test* while testing; switch to *Live* when ready.
5. In your Stripe Dashboard, go to **Developers → Webhooks → Add endpoint** and set
   the URL to: `https://yoursite.com/modules/subscriptions/ipn.php`

### PayPal
1. Enter the **Business email** address of your PayPal account.
2. Enable **Sandbox mode** for testing (uses `sandbox.paypal.com`).
3. In your PayPal account, go to **Account Settings → Instant Payment Notifications**
   and enable IPN with the URL: `https://yoursite.com/modules/subscriptions/ipn.php`

### Manual / Offline
No configuration needed. When a user checks out with Manual, they see payment instructions
(e.g. bank transfer details) and wait for you to manually mark the payment as completed in
**Admin → Subscriptions → Payments**.

### Active gateway
In **Preferences → Active Gateway**, select which gateway is shown by default at checkout.
All enabled gateways remain available — this setting only controls the default selection.

---

## B3) Module preferences

**Admin → Subscriptions → Preferences** (XOOPS module preferences)

| Setting | Recommendation |
|---|---|
| **Currency** | Your ISO 4217 code, e.g. `USD`, `EUR`, `GBP` |
| **Currency symbol** | Matching symbol, e.g. `$`, `€`, `£` |
| **Tax rate** | Enter `0` for no tax; enter `10` for 10% |
| **Trial days** | Default trial for new plans; set `0` to disable. Individual plans can override this. |
| **Invoice prefix** | e.g. `INV-` produces invoice numbers like `INV-0001` |
| **Email notifications** | Keep `Yes` — users receive emails on activation, renewal, and cancellation |
| **Allow multiple subscriptions** | `No` recommended unless your plans are designed to be stacked |
| **Grace period days** | `3` is a safe default — gives failed payments a retry window before hard cancellation |
| **Cron secret** | Auto-generated; copy it for use in the cron URL (see B7) |
| **Sample data buttons** | Set to `No` on a production site after finishing setup |

---

## B4) Create and manage plans

**Admin → Subscriptions → Plans → Add Plan**

### Required fields
| Field | Notes |
|---|---|
| **Plan name** | Shown everywhere (plan cards, invoices, dashboard). Keep it short and clear. |
| **Slug** | URL-safe identifier, auto-generated from name. |
| **Price** | Decimal, e.g. `9.99`. Enter `0` for a free plan. |
| **Billing cycle** | `monthly`, `annual`, `weekly`, `daily`, `quarterly`, or `one_time` |
| **Active** | Set to `Yes` to make the plan visible on the public plans page. |

### Optional fields
| Field | Notes |
|---|---|
| **Description** | Displayed under the plan name on the plans page. |
| **Trial days** | Override the global default. `0` = no trial for this plan. |
| **Setup fee** | One-time fee charged at first payment in addition to the recurring price. |
| **Pricing model** | `flat` (fixed price), `usage` (pay-per-use), or `hybrid` (base + usage). |
| **Featured** | Highlights this plan visually on the plans listing. |
| **Sort order** | Controls display order. Lower numbers appear first. |
| **Max users** | Subscriber cap. `0` = unlimited. |

### Adding plan features
In the **Plan Features** sub-section (below the main plan form), add bullet-point descriptions
of what the plan includes. These are displayed at checkout. Examples:
- *500 downloads per month*
- *Full access to Publisher Pro*
- *Priority support*

### Editing and deactivating plans
- **Edit** any plan from the plan list. Changes to price or billing cycle take effect for
  **new** subscriptions only — existing subscribers keep their original terms.
- **Deactivate** a plan (set Active = No) to hide it from new subscribers without affecting
  existing ones.

---

## B5) Connect a module (access control)

This section allows you to gate access to any other installed XOOPS module behind a subscription.

**Admin → Subscriptions → Connected Modules → Add Module**

### Step 1 — Register the module
| Field | Notes |
|---|---|
| **Module Dirname** | Exact `dirname` from the target module's `xoops_version.php` (e.g. `publisher`) |
| **Display Name** | Human-readable name shown in admin UI |
| **Description** | What this module does (for your own reference) |
| **Webhook URL** | Optional — a URL in the target module that receives subscription events |
| **Active** | Set to `Yes` |

After saving, copy the **API Key** shown — the module developer needs this.

### Step 2 — Add access rules

In **Connected Modules → Access Rules** for the module you just registered, add one rule
per plan that should grant access:

| Setting | Options |
|---|---|
| **Plan** | Any active plan |
| **Access Type** | `full` — unlimited access |
| | `limited` — quota per billing period (e.g. 50 downloads/month) |
| | `metered` — every action is logged for billing |
| **Limit Value** | For `limited` only: the quota amount (e.g. `50`) |
| **XOOPS Group** | Optional: auto-assign users to this group when active; remove on expiry |

> A user needs both an **active subscription** and a **matching access rule** to gain entry.
> If you create a plan but forget to add an access rule, users on that plan will be denied.

---

## B6) Monitor and manage subscriptions

**Admin → Subscriptions → Subscriptions**

The subscription list shows every user subscription with:
- User name (links to their subscription history)
- Plan name
- Status badge (`active`, `trial`, `past_due`, `cancelled`, `expired`)
- Period start and end dates
- Gateway used

### Filtering
Use the filters at the top to narrow by status, plan, user, or date range.
Appending `?user_id=N` to the URL shows all subscriptions for a specific user.

### Actions
- **View** — full subscription detail
- **Edit** — change status, dates, or plan (for manual corrections)
- **Cancel** — immediately cancel or schedule cancel-at-period-end

---

## B7) Manage payments and refunds

**Admin → Subscriptions → Payments**

The payment log shows every transaction across all gateways with amount, status, and gateway
transaction ID.

### Issuing a refund
1. Find the payment in the list and click **View**.
2. Click **Issue Refund**.
3. Enter the refund amount (full or partial) and a reason note.
4. Save — the refund is recorded in the `subscriptions_refunds` table.
5. Process the actual money return in your Stripe or PayPal dashboard separately.

> **Note:** The module records the refund for bookkeeping purposes. The actual payment
> reversal must be initiated in the gateway's own dashboard (Stripe or PayPal).

---

## B8) Manage coupons

**Admin → Subscriptions → Coupons → Add Coupon**

| Field | Notes |
|---|---|
| **Code** | What users type at checkout, e.g. `SUMMER25` |
| **Discount type** | `percentage` or `fixed` |
| **Discount value** | e.g. `25` for 25% off, or `5.00` for $5 off |
| **Minimum order** | Minimum checkout total before the coupon applies |
| **Max uses (total)** | `0` = unlimited |
| **Max uses per user** | `1` = single use per account |
| **Valid from / until** | Optional date window |
| **Applicable plans** | Leave blank for all plans, or select specific plan IDs |

The **Usage** column on the coupon list shows how many times each code has been redeemed.

---

## B9) Configure webhooks

**Admin → Subscriptions → Webhooks → Add Webhook**

Webhooks send an HTTP POST to an external URL whenever a subscription event occurs.
Use this to notify third-party services, trigger emails, or sync external systems.

| Field | Notes |
|---|---|
| **URL** | The endpoint that will receive the POST |
| **Events** | Checkboxes for each event type you want to receive |
| **Secret** | A signing secret — the receiver uses this to verify the payload signature |
| **Active** | `Yes` to enable |

After saving, the webhook list shows **Last fired** timestamp and **Fail count**.
Use the **Test** button to send a sample payload and verify your endpoint is working.

Available events: `subscription.activated`, `subscription.renewed`, `subscription.cancelled`,
`subscription.expired`, `subscription.trial_ending`, `payment.completed`, `payment.refunded`.

---

## B10) Cron job (automated lifecycle management)

Without a cron job, subscriptions will not expire automatically and trial-ending notifications
will not fire.

### What the cron does
| Task | Description |
|---|---|
| Expiry check | Marks subscriptions `expired` when their `period_end` date has passed |
| Trial ending | Fires `subscription.trial_ending` events 24 hours before trial expiry |
| Period-end cancellations | Finalises subscriptions scheduled to cancel at period end |
| Group sync | Removes expired subscribers from their assigned XOOPS groups |

### Setting up (recommended: daily at 3 AM)

**Option 1 — HTTP (via crontab):**
```cron
0 3 * * * curl -s "https://yoursite.com/modules/subscriptions/cron.php?secret=YOUR_CRON_SECRET" > /dev/null
```

**Option 2 — PHP CLI:**
```bash
0 3 * * * php /var/www/html/modules/subscriptions/cron.php
```

Find `YOUR_CRON_SECRET` in **Admin → Subscriptions → Preferences → Cron Secret**.

---

## B11) Reports

**Admin → Subscriptions → Reports**

The reports page provides a summary view of:
- Active subscriptions by plan
- Revenue by month
- Trial conversion counts
- Coupon usage summary

---

# Part C — Module Developer Guide

## C1) How the integration works

```
[Your Module] ──SDK call──► [Subscriptions] ──checks──► [Subscription DB]
                                   │
                                   └──► returns: allowed / denied / quota
```

Your module never queries the subscriptions database directly. It calls the
**SubscriptionsSDK** — a single PHP file that handles all access checks, usage logging,
and webhook verification for you.

**Three things you need before writing any code:**
1. Register your module in the Subscriptions admin (see B5 above).
2. Create at least one access rule (plan → your module).
3. Copy the generated API key into your module's configuration.

**Full SDK reference:** [`integration-guide.md`](integration-guide.md)

---

## C2) New module — integrating from the start

When building a new XOOPS module that requires Subscriptions, include the integration
in your `index.php` (and any other protected entry points) from the beginning.

### Recommended pattern — optional dependency

Wrap the SDK in `file_exists()` so your module works on sites that do not have
Subscriptions installed:

```php
<?php
// modules/mymodule/index.php

require_once XOOPS_ROOT_PATH . '/include/cp_header.php';

// ── Subscriptions access check ──────────────────────────────────────────────
if (file_exists(XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
    $sdk = SubscriptionsSDK::getInstance('mymodule');
    $uid = isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
    $sdk->enforceAccess($uid); // redirects to plans page if no active subscription
}
// ── end Subscriptions ────────────────────────────────────────────────────────

// ... rest of your module code
```

**Why `file_exists()`?**
- Sites without Subscriptions continue to work normally.
- You can distribute your module independently of Subscriptions.
- Subscriptions becomes an optional premium add-on for your module's users.

### Module entry points to protect
Protect the user-facing entry points, not every PHP file. Typically:

| File | Protect? |
|---|---|
| `index.php` | Yes — main landing page |
| `download.php` | Yes — if downloads are the gated resource |
| `view.php`, `detail.php` | Yes — content detail pages |
| `admin/*.php` | No — admin pages are already XOOPS-admin-protected |
| `include/functions.php` | No — library file, not a request handler |
| `preloads/*.php` | No — bootstrapped automatically, not a request entry point |

---

## C3) Retrofitting an existing module

Adding Subscriptions access control to an existing XOOPS module requires changes only to
its **entry point files** — not to every PHP file in the module.

### Step 1 — Identify entry points

Entry points are PHP files that users reach via a browser URL. Find them in your module root:

```bash
# Entry points are typically in the module root, not in subdirectories
ls modules/yourmodule/*.php
```

Common entry points: `index.php`, `download.php`, `view.php`, `search.php`.

### Step 2 — Add the access check

At the top of each entry point, after XOOPS initialises but before any content output:

```php
// After: require_once 'include/cp_header.php'; or equivalent XOOPS init

if (file_exists(XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
    $sdk = SubscriptionsSDK::getInstance('yourmodule');
    $uid = isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;

    if (!$sdk->hasAccess($uid)) {
        // Show existing module "access denied" page, or redirect
        // Option A — use Subscriptions redirect:
        $sdk->enforceAccess($uid);
        // Option B — show your own message with a link to plans:
        // redirect_header($sdk->getPlansUrl(), 3, 'A subscription is required.');
    }
}
```

### Step 3 — Replace existing membership checks (if any)

If your module already has a `$xoopsUser` logged-in check or group check, replace or wrap it:

```php
// BEFORE (existing group check):
if (!$xoopsUser || !$xoopsUser->isMemberOf(5)) {
    redirect_header(XOOPS_URL, 3, 'Members only.');
    exit;
}

// AFTER (Subscriptions check with group check as fallback):
$hasAccess = false;
if (file_exists(XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
    $sdk = SubscriptionsSDK::getInstance('yourmodule');
    $uid = isset($xoopsUser) ? (int)$xoopsUser->getVar('uid') : 0;
    $hasAccess = $sdk->hasAccess($uid);
} elseif ($xoopsUser && $xoopsUser->isMemberOf(5)) {
    // Subscriptions not installed — fall back to group check
    $hasAccess = true;
}

if (!$hasAccess) {
    redirect_header(XOOPS_URL, 3, 'A subscription or membership is required.');
    exit;
}
```

### Step 4 — Show upgrade prompts on existing "access denied" pages

If your module currently shows a "you need to be a member" page, enhance it to show
which subscription plans grant access:

```php
if (!$hasAccess && isset($sdk)) {
    $plans = $sdk->getPlansWithAccess();
    // $plans = array of plans that include access to your module
    // Pass $plans to your Smarty template to display checkout links
    $xoopsTpl->assign('subscription_plans', $plans);
    $xoopsTpl->assign('plans_url', $sdk->getPlansUrl());
}
```

In your template:
```smarty
{if $subscription_plans}
  <p>Subscribe to access this content:</p>
  <ul>
    {foreach $subscription_plans as $plan}
      <li>
        <a href="{$plan.checkout_url}">
          {$plan.name} — {$plan.price} {$plan.currency} / {$plan.billing_cycle}
        </a>
      </li>
    {/foreach}
  </ul>
{else}
  <p><a href="{$plans_url}">View available plans</a></p>
{/if}
```

---

## C4) Access types in practice

### Full access
The user either has an active subscription or they don't.

```php
$sdk->enforceAccess($uid); // simplest — auto-redirect if denied
```

### Limited access (quota per billing period)
Check and deduct in the same operation:

```php
$quota = $sdk->getRemainingQuota($uid, 'download');
// Returns: ['limit' => 50, 'used' => 23, 'remaining' => 27]
// Returns: null if user has no subscription or rule is not 'limited' type

if ($quota === null || $quota['remaining'] <= 0) {
    // No subscription or quota exhausted
    echo 'You have used all your downloads for this billing period.';
    echo ' <a href="' . $sdk->getPlansUrl() . '">Upgrade your plan</a> for more.';
    exit;
}

// Serve the resource
serveDownload($fileId);

// Record the usage after successful delivery
$sdk->recordUsage($uid, 'download');
```

> **Important:** Record usage *after* the action completes successfully, not before.
> Use the same event string (`'download'`) consistently across your module — it is
> used for both quota lookups and usage reports.

### Metered access (pay-per-use)
Log every action for billing:

```php
// Check for any active subscription first
if (!$sdk->hasAccess($uid)) {
    redirect_header($sdk->getPlansUrl(), 2, 'Subscription required.');
    exit;
}

// Perform the action
processApiCall($request);

// Log the billable usage event (quantity defaults to 1.0; pass float for fractional units)
$sdk->recordUsage($uid, 'api_call');
// Or with custom quantity: $sdk->recordUsage($uid, 'api_call', 2.5);
```

### Displaying subscription status in your module's UI

```php
$sub = $sdk->getSubscription($uid);
// Returns: ['sub_id', 'status', 'plan_id', 'plan_name', 'billing_cycle',
//           'period_start', 'period_end', 'auto_renew', 'is_trial']
// Returns: null if user has no active subscription

if ($sub) {
    $statusText = 'Plan: ' . htmlspecialchars($sub['plan_name'])
                . ' · Renews: ' . date('Y-m-d', $sub['period_end']);
    if ($sub['is_trial']) {
        $statusText .= ' (Trial ends ' . date('Y-m-d', $sub['period_end']) . ')';
    }
} else {
    $statusText = '<a href="' . $sdk->getPlansUrl() . '">Subscribe for access</a>';
}
```

---

## C5) Receiving webhook events

When a subscription is activated, renewed, cancelled, or expires, Subscriptions can POST
a signed JSON payload to your module's webhook URL.

### Create a webhook endpoint

Create `modules/yourmodule/webhook.php`:

```php
<?php
// No XOOPS session required — this is a server-to-server call
define('XOOPS_ROOT_PATH', dirname(dirname(dirname(__FILE__))));
require_once XOOPS_ROOT_PATH . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';

$sdk       = SubscriptionsSDK::getInstance('yourmodule');
$payload   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SUBSCRIPTIONS_SIGNATURE'] ?? '';

// Always verify before trusting the payload
if (!$sdk->verifyWebhook($payload, $signature)) {
    http_response_code(401);
    exit('Invalid signature');
}

$event = json_decode($payload, true);

switch ($event['event'] ?? '') {
    case 'subscription.activated':
    case 'subscription.renewed':
        $userId = (int)($event['user_id'] ?? 0);
        // Grant access, send welcome email, provision resources
        grantYourModuleAccess($userId);
        break;

    case 'subscription.cancelled':
    case 'subscription.expired':
        $userId = (int)($event['user_id'] ?? 0);
        // Revoke access, archive user data, send expiry notice
        revokeYourModuleAccess($userId);
        break;

    case 'subscription.trial_ending':
        $userId = (int)($event['user_id'] ?? 0);
        // Send "your trial ends tomorrow" reminder email
        sendTrialEndingReminder($userId, $event['period_end']);
        break;
}

http_response_code(200);
echo 'OK';
```

Then register the URL in **Admin → Subscriptions → Connected Modules → [Your Module] → Webhook URL**.

### Event payload structure

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
    "is_trial":     false,
    "timestamp":    1704067201
}
```

### All event types

| Event | Fired when |
|---|---|
| `subscription.activated` | New subscription becomes active (after payment or trial start) |
| `subscription.renewed` | Subscription successfully renews for a new billing period |
| `subscription.cancelled` | Admin or user cancels a subscription |
| `subscription.expired` | Subscription reaches its end date without renewal |
| `subscription.trial_ending` | 24 hours before a trial expires (requires cron) |
| `payment.completed` | A payment is recorded as successful |
| `payment.refunded` | A payment is refunded |

---

## C6) Integration checklist

Copy this checklist into your module's pull request or release notes:

- [ ] Module registered in **Subscriptions → Connected Modules**
- [ ] At least one access rule created (plan → module → access type)
- [ ] API key stored in module configuration
- [ ] `require_once` for SDK wrapped in `file_exists()` (optional dependency)
- [ ] `hasAccess()` or `enforceAccess()` called before serving protected content
- [ ] For `limited` access: `getRemainingQuota()` checked and `recordUsage()` called after each action
- [ ] For `metered` access: `recordUsage()` called after each billable event
- [ ] Upgrade prompts shown when user is denied (link to `getPlansUrl()` or `getPlansWithAccess()`)
- [ ] Webhook endpoint created and URL registered (if using subscription events)
- [ ] Webhook signature verified with `verifyWebhook()` before trusting payload
- [ ] Integration tested with a real test subscription (Stripe test mode or Manual gateway)

---

# Part D — Best-Practice Design Notes

These principles apply whether you are setting up the module or building an integration.

## D1) Treat webhooks as the source of truth for billing state

Do not rely on "the user returned to the site after paying" to activate a subscription.
The payment gateway may redirect back before the payment is fully confirmed. Always sync
from webhook/IPN events fired by `ipn.php` or from the cron job.

## D2) Separate "billing" from "entitlements"

Billing answers: *"Is it paid? When is the next charge?"*
Entitlements answer: *"What can this user do right now?"*

The Subscriptions module handles billing. The access rules and SDK handle entitlements.
Keep these concerns separate in your module code — do not mix payment status checks with
feature-flag logic.

## D3) Keep PCI scope small

Subscriptions uses Stripe Checkout and PayPal hosted pages, so card details never pass
through your XOOPS server. Do not add any form fields that capture card numbers or CVVs
— always redirect to the gateway's hosted payment page.

## D4) Use `file_exists()` — make Subscriptions an optional dependency

Modules that gate content with `file_exists()` protection continue to work on sites
without Subscriptions installed. This keeps your module independently installable and
allows you to sell the Subscriptions integration as a premium feature.

## D5) Make plans easy to understand at a glance

The best subscription plans have:
- A clear name (*Basic*, *Professional*, *Enterprise*)
- A short description (one sentence: *"Full access to all downloads"*)
- A specific, numbered feature list (*"500 downloads per month"*, not *"lots of downloads"*)
- An obvious price displayed before the checkout button

Plan features are configurable in **Admin → Subscriptions → Plans → [Edit Plan] → Plan Features**.

---

## Future features [Planned, not in v1.0.0]

The following capabilities are on the roadmap but not yet available:

| Feature | Notes |
|---|---|
| Plan upgrade/downgrade with proration | Automatic prorated billing when changing plans mid-cycle |
| Subscription pause | Temporarily freeze a subscription without cancelling |
| Credit notes / store credit | Issue credits against invoices |
| Usage alerts | 80%/100% quota threshold notifications to subscribers |
| Multi-vendor marketplace | Vendor profiles, revenue share, vendor analytics dashboard |
| Annual savings display | Automatic "save X%" badge when monthly + annual plans are both active |
| First-payment-only coupons | Coupons that discount only the first charge in a recurring subscription |

These features are documented in the original vision document and will be added in future versions.
