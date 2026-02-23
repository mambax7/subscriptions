<?php
/**
 * Subscriptions Language file - Main (English)
 *
 * @package    subscriptions
 * @subpackage language
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

// ---- Module info --------------------------------------------------------
define('_MD_SUBSCRIPTIONS_MODULE_NAME', 'Subscriptions');

// ---- Billing cycles ----------------------------------------------------
define('_MD_SUBSCRIPTIONS_CYCLE_ONE_TIME',  'One-Time');
define('_MD_SUBSCRIPTIONS_CYCLE_DAILY',     'Daily');
define('_MD_SUBSCRIPTIONS_CYCLE_WEEKLY',    'Weekly');
define('_MD_SUBSCRIPTIONS_CYCLE_MONTHLY',   'Monthly');
define('_MD_SUBSCRIPTIONS_CYCLE_QUARTERLY', 'Quarterly');
define('_MD_SUBSCRIPTIONS_CYCLE_ANNUAL',    'Annual');

// ---- Subscription statuses ---------------------------------------------
define('_MD_SUBSCRIPTIONS_STATUS_TRIAL',     'Trial');
define('_MD_SUBSCRIPTIONS_STATUS_ACTIVE',    'Active');
define('_MD_SUBSCRIPTIONS_STATUS_PAST_DUE',  'Past Due');
define('_MD_SUBSCRIPTIONS_STATUS_CANCELLED', 'Cancelled');
define('_MD_SUBSCRIPTIONS_STATUS_EXPIRED',   'Expired');
define('_MD_SUBSCRIPTIONS_STATUS_PAUSED',    'Paused');

// ---- Plans page --------------------------------------------------------
define('_MD_SUBSCRIPTIONS_PLANS_TITLE',      'Membership Plans');
define('_MD_SUBSCRIPTIONS_PLAN_NOT_FOUND',   'Plan not found.');
define('_MD_SUBSCRIPTIONS_CHOOSE_PLAN',      'Choose a plan that works for you');
define('_MD_SUBSCRIPTIONS_SUBSCRIBE',        'Subscribe Now');
define('_MD_SUBSCRIPTIONS_FREE_TRIAL',       'Start Free Trial');
define('_MD_SUBSCRIPTIONS_TRIAL_BADGE',      '%d-day free trial');
define('_MD_SUBSCRIPTIONS_FEATURED',         'Most Popular');
define('_MD_SUBSCRIPTIONS_ANNUAL_SAVE',      'Save %s%% annually');
define('_MD_SUBSCRIPTIONS_ALREADY_SUBSCRIBED','You are already subscribed to this plan.');
define('_MD_SUBSCRIPTIONS_SUBSCRIPTION',     'Subscription');

// ---- Checkout ----------------------------------------------------------
define('_MD_SUBSCRIPTIONS_CHECKOUT_TITLE',   'Checkout');
define('_MD_SUBSCRIPTIONS_ORDER_SUMMARY',    'Order Summary');
define('_MD_SUBSCRIPTIONS_COUPON_CODE',      'Coupon Code');
define('_MD_SUBSCRIPTIONS_APPLY_COUPON',     'Apply');
define('_MD_SUBSCRIPTIONS_SUBTOTAL',         'Subtotal');
define('_MD_SUBSCRIPTIONS_DISCOUNT',         'Discount');
define('_MD_SUBSCRIPTIONS_TAX',              'Tax');
define('_MD_SUBSCRIPTIONS_TOTAL',            'Total');
define('_MD_SUBSCRIPTIONS_PROCEED_PAYMENT',  'Proceed to Payment');

// ---- Coupon errors -----------------------------------------------------
define('_MD_SUBSCRIPTIONS_COUPON_NOT_FOUND',   'Coupon code not found.');
define('_MD_SUBSCRIPTIONS_COUPON_INVALID',     'This coupon is no longer valid.');
define('_MD_SUBSCRIPTIONS_COUPON_PLAN_MISMATCH','This coupon does not apply to the selected plan.');
define('_MD_SUBSCRIPTIONS_COUPON_MIN_AMOUNT',  'Minimum order amount not reached for this coupon.');
define('_MD_SUBSCRIPTIONS_COUPON_USER_LIMIT',  'You have already used this coupon.');
define('_MD_SUBSCRIPTIONS_COUPON_APPLIED',     'Coupon applied successfully!');

// ---- Payment -----------------------------------------------------------
define('_MD_SUBSCRIPTIONS_PAYMENT_SUCCESS',   'Your payment was successful! Welcome to your membership.');
define('_MD_SUBSCRIPTIONS_PAYMENT_PENDING',   'Your payment is being processed. You will be notified once confirmed.');
define('_MD_SUBSCRIPTIONS_PAYMENT_FAILED',    'Payment failed. Please try again or contact support.');
define('_MD_SUBSCRIPTIONS_SUB_CREATE_FAILED', 'Could not create subscription. Please try again.');

// ---- Trial -------------------------------------------------------------
define('_MD_SUBSCRIPTIONS_TRIAL_STARTED',     'Your free trial has started! Enjoy exploring the features.');

// ---- Dashboard ---------------------------------------------------------
define('_MD_SUBSCRIPTIONS_DASHBOARD_TITLE',   'My Membership');
define('_MD_SUBSCRIPTIONS_MY_SUBSCRIPTION',   'My Subscription');
define('_MD_SUBSCRIPTIONS_NO_SUBSCRIPTION',   'You do not have an active subscription.');
define('_MD_SUBSCRIPTIONS_RENEWS_ON',         'Renews on');
define('_MD_SUBSCRIPTIONS_EXPIRES_ON',        'Expires on');
define('_MD_SUBSCRIPTIONS_CANCEL_SUBSCRIPTION','Cancel Subscription');
define('_MD_SUBSCRIPTIONS_CANCEL_REASON',     'Reason for cancelling (optional)');
define('_MD_SUBSCRIPTIONS_CONFIRM_CANCEL',    'Are you sure you want to cancel?');
define('_MD_SUBSCRIPTIONS_SUB_CANCELLED',     'Your subscription has been cancelled.');
define('_MD_SUBSCRIPTIONS_AUTO_RENEW_ON',     'Auto-renew: On');
define('_MD_SUBSCRIPTIONS_AUTO_RENEW_OFF',    'Auto-renew: Off');
define('_MD_SUBSCRIPTIONS_SUBSCRIPTION_HISTORY','Subscription History');

// ---- Invoices ----------------------------------------------------------
define('_MD_SUBSCRIPTIONS_INVOICES_TITLE',    'My Invoices');
define('_MD_SUBSCRIPTIONS_INVOICE_NUMBER',    'Invoice #');
define('_MD_SUBSCRIPTIONS_INVOICE_DATE',      'Date');
define('_MD_SUBSCRIPTIONS_INVOICE_DUE',       'Due Date');
define('_MD_SUBSCRIPTIONS_INVOICE_STATUS',    'Status');
define('_MD_SUBSCRIPTIONS_INVOICE_TOTAL',     'Total');
define('_MD_SUBSCRIPTIONS_INVOICE_VIEW',      'View');
define('_MD_SUBSCRIPTIONS_NO_INVOICES',       'No invoices found.');
define('_MD_SUBSCRIPTIONS_INVOICE_PAID',      'Paid');
define('_MD_SUBSCRIPTIONS_INVOICE_OPEN',      'Open');
define('_MD_SUBSCRIPTIONS_INVOICE_VOID',      'Void');
define('_MD_SUBSCRIPTIONS_BILLING_PERIOD',    'Billing Period');
define('_MD_SUBSCRIPTIONS_PRINT_INVOICE',     'Print Invoice');

// ---- Access control ----------------------------------------------------
define('_MD_SUBSCRIPTIONS_ACCESS_DENIED',     'Access Denied. A membership plan is required to access this content.');

// ---- Gateway -----------------------------------------------------------
define('_MD_SUBSCRIPTIONS_GATEWAY_NOT_CONFIGURED', 'Payment gateway is not properly configured. Please contact the administrator.');
define('_MD_SUBSCRIPTIONS_MANUAL_INSTRUCTIONS',    'Please transfer payment to the bank account shown below. Your account will be activated after confirmation.');

// ---- Payment errors ----------------------------------------------------
define('_MD_SUBSCRIPTIONS_PAYMENT_NOT_FOUND',      'Payment record not found.');
define('_MD_SUBSCRIPTIONS_REFUND_NOT_ELIGIBLE',     'This payment is not eligible for a refund.');
define('_MD_SUBSCRIPTIONS_REFUND_EXCEEDS_PAYMENT',  'Refund amount exceeds the original payment amount.');

// ---- Email notifications -----------------------------------------------
define('_MD_SUBSCRIPTIONS_EMAIL_PAYMENT_SUBJECT',  'Payment Confirmation');
define('_MD_SUBSCRIPTIONS_EMAIL_PAYMENT_BODY',
    "Dear %s,\n\nThank you for your payment.\n\nPlan: %s\nAmount: %s\nMembership valid until: %s\n\nThank you for your continued support!\n"
);
define('_MD_SUBSCRIPTIONS_EMAIL_TRIAL_ENDING_SUBJECT', 'Your free trial is ending soon');
define('_MD_SUBSCRIPTIONS_EMAIL_TRIAL_ENDING_BODY',
    "Dear %s,\n\nYour free trial for \"%s\" ends on %s.\n\nTo keep your access, please choose a plan:\n%s\n\nThank you!\n"
);

// ---- Cancel at period end ----------------------------------------------
define('_MD_SUBSCRIPTIONS_CANCEL_WHEN',         'When should the cancellation take effect?');
define('_MD_SUBSCRIPTIONS_CANCEL_AT_PERIOD_END','At the end of the current billing period');
define('_MD_SUBSCRIPTIONS_CANCEL_NOW',          'Cancel immediately (access ends now)');
define('_MD_SUBSCRIPTIONS_CANCEL_SCHEDULED',    'Your subscription will be cancelled at the end of the current period.');
define('_MD_SUBSCRIPTIONS_CANCELS_ON',          'Cancels on');
define('_MD_SUBSCRIPTIONS_TRIAL_ENDS',          'Trial ends');

// ---- Reactivate --------------------------------------------------------
define('_MD_SUBSCRIPTIONS_REACTIVATE',          'Keep Subscription');
define('_MD_SUBSCRIPTIONS_CONFIRM_REACTIVATE',  'Reactivate your subscription?');
define('_MD_SUBSCRIPTIONS_REACTIVATE_DESC',     'Your subscription was scheduled to cancel at the end of the period. Click below to keep your subscription active and re-enable auto-renewal.');
define('_MD_SUBSCRIPTIONS_SUB_REACTIVATED',     'Your subscription has been reactivated.');

// ---- Usage summary on dashboard ----------------------------------------
define('_MD_SUBSCRIPTIONS_USAGE_THIS_PERIOD',   'Usage This Billing Period');
define('_MD_SUBSCRIPTIONS_USAGE_EVENT',         'Event');
define('_MD_SUBSCRIPTIONS_USAGE_QUANTITY',      'Quantity');
define('_MD_SUBSCRIPTIONS_USAGE_COST',          'Cost');
define('_MD_SUBSCRIPTIONS_USAGE_LAST',          'Last Event');
define('_MD_SUBSCRIPTIONS_USAGE_UNBILLED',      'Unbilled Total');

// ---- Coupon recurring ---------------------------------------------------
define('_MD_SUBSCRIPTIONS_COUPON_RECURRING_MONTHS', 'Recurring Months');
define('_MD_SUBSCRIPTIONS_COUPON_RECURRING_HELP',   '0 = first payment only; -1 = all renewals; N = applies to first N billing periods');

// ---- Checkout (enhanced UI) --------------------------------------------
define('_MD_SUBSCRIPTIONS_BACK_TO_PLANS',    '← Back to plans');
define('_MD_SUBSCRIPTIONS_HAVE_COUPON',      'Have a coupon?');
define('_MD_SUBSCRIPTIONS_APPLIED',          'Applied');
define('_MD_SUBSCRIPTIONS_PAYMENT_DETAILS',  'Payment');
define('_MD_SUBSCRIPTIONS_GATEWAY',          'Via');
define('_MD_SUBSCRIPTIONS_SECURE_NOTE',      'Encrypted & secure checkout');
define('_MD_SUBSCRIPTIONS_TRUST_SSL',        'SSL encrypted');
define('_MD_SUBSCRIPTIONS_TRUST_CANCEL',     'Cancel anytime');
define('_MD_SUBSCRIPTIONS_TRUST_INVOICE',    'Invoice emailed');
define('_MD_SUBSCRIPTIONS_TRUST_RENEWS',     'Auto-renews, easy to cancel');
define('_MD_SUBSCRIPTIONS_WHATS_INCLUDED',   "What's included");

// ---- Plans page (enhanced UI) ------------------------------------------
define('_MD_SUBSCRIPTIONS_PLANS_EYEBROW',      'Membership Plans');
define('_MD_SUBSCRIPTIONS_BILLING_MONTHLY',    'Monthly');
define('_MD_SUBSCRIPTIONS_BILLING_ANNUAL',     'Annual');
define('_MD_SUBSCRIPTIONS_SAVE_UP_TO',         'Save up to 20%');
define('_MD_SUBSCRIPTIONS_SETUP_FEE',          'Setup fee');
define('_MD_SUBSCRIPTIONS_LOGIN_TO_SUBSCRIBE', 'Sign in to Subscribe');
define('_MD_SUBSCRIPTIONS_ACCESS_DENIED_TITLE','Subscription Required');

// ---- Dashboard (enhanced UI) -------------------------------------------
define('_MD_SUBSCRIPTIONS_DASHBOARD_SUB',      'Manage your membership and billing');
define('_MD_SUBSCRIPTIONS_VIEW_ALL',           'View all');
define('_MD_SUBSCRIPTIONS_HISTORY_PLAN',       'Plan');
define('_MD_SUBSCRIPTIONS_HISTORY_STARTED',    'Started');
define('_MD_SUBSCRIPTIONS_HISTORY_EXPIRED',    'Ended');

// ---- Index / landing page ----------------------------------------------
define('_MD_SUBSCRIPTIONS_INDEX_HEADLINE',    'Unlock full access to everything');
define('_MD_SUBSCRIPTIONS_INDEX_SUBHEADLINE', 'Choose a membership plan and get instant access. Cancel anytime, no questions asked.');
define('_MD_SUBSCRIPTIONS_INDEX_CTA_PRIMARY', 'See pricing');
define('_MD_SUBSCRIPTIONS_INDEX_CTA_LOGIN',   'Sign in');
define('_MD_SUBSCRIPTIONS_INDEX_PLANS_SUB',   'No hidden fees. Cancel anytime.');
define('_MD_SUBSCRIPTIONS_PROP1_TITLE',       'Instant access');
define('_MD_SUBSCRIPTIONS_PROP1_DESC',        'Your account is activated immediately after payment.');
define('_MD_SUBSCRIPTIONS_PROP2_TITLE',       'Secure checkout');
define('_MD_SUBSCRIPTIONS_PROP2_DESC',        'SSL-encrypted payments processed safely.');
define('_MD_SUBSCRIPTIONS_PROP3_TITLE',       'Cancel anytime');
define('_MD_SUBSCRIPTIONS_PROP3_DESC',        'No lock-in. Manage or cancel from your dashboard.');

// ---- Invoice detail (enhanced UI) --------------------------------------
define('_MD_SUBSCRIPTIONS_INVOICE_OFFICIAL',   'Official Invoice');
define('_MD_SUBSCRIPTIONS_INV_BILLED_TO',      'Billed to');
define('_MD_SUBSCRIPTIONS_INV_DESCRIPTION',    'Description');
define('_MD_SUBSCRIPTIONS_INV_QTY',            'Qty');
define('_MD_SUBSCRIPTIONS_INV_UNIT_PRICE',     'Unit Price');
define('_MD_SUBSCRIPTIONS_INV_NOTES',          'Notes');
define('_MD_SUBSCRIPTIONS_INV_THANKYOU',       'Thank you for your business!');
