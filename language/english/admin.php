<?php
/**
 * Subscriptions Language file - Admin (English)
 *
 * @package    subscriptions
 * @subpackage language
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

// ---- Admin navigation --------------------------------------------------
define('_AM_SUBSCRIPTIONS_DASHBOARD',     'Dashboard');
define('_AM_SUBSCRIPTIONS_PLANS',         'Plans');
define('_AM_SUBSCRIPTIONS_SUBSCRIPTIONS', 'Subscriptions');
define('_AM_SUBSCRIPTIONS_PAYMENTS',      'Payments');
define('_AM_SUBSCRIPTIONS_COUPONS',       'Coupons');
define('_AM_SUBSCRIPTIONS_MODULES',       'Connected Modules');
define('_AM_SUBSCRIPTIONS_GATEWAYS',      'Payment Gateways');
define('_AM_SUBSCRIPTIONS_REPORTS',       'Reports');
define('_AM_SUBSCRIPTIONS_WEBHOOKS',      'Webhooks');
define('_AM_SUBSCRIPTIONS_ABOUT',         'About');

// ---- Blocks admin ------------------------------------------------------
define('_AM_SUBSCRIPTIONS_BLOCKSADMIN',             'Blocks Admin');
define('_AM_SUBSCRIPTIONS_BLOCKS_ERROR403',         'You do not have permission to manage blocks for this module.');
define('_AM_SUBSCRIPTIONS_BLOCKS_DELETE_CONFIRM',   'Are you sure you want to delete this block? This cannot be undone.');
define('_AM_SUBSCRIPTIONS_BLOCKS_CONFIRM',          'Confirm');

// ---- Clone -------------------------------------------------------------
define('_AM_SUBSCRIPTIONS_CLONE_TITLE',       'Clone "%s"');
define('_AM_SUBSCRIPTIONS_CLONE_NAME',        'New module directory name');
define('_AM_SUBSCRIPTIONS_CLONE_NAME_DSC',    'Lowercase letters, digits, underscores and hyphens only. This becomes the folder name under /modules/.');
define('_AM_SUBSCRIPTIONS_CLONE_INVALIDNAME', 'Invalid name: "%s". Use only lowercase letters, digits, underscores and hyphens.');
define('_AM_SUBSCRIPTIONS_CLONE_EXISTS',      'A module named "%s" already exists. Choose a different name.');
define('_AM_SUBSCRIPTIONS_CLONE_CONGRAT',     'Module cloned successfully. Go to %s in Module Admin to install it.');
define('_AM_SUBSCRIPTIONS_CLONE_IMAGEFAIL',   'The module logo could not be updated (GD library or source image missing) — replace it manually.');
define('_AM_SUBSCRIPTIONS_CLONE_FAIL',        'Cloning failed. Check that the web server has write permission to the /modules/ directory.');


// ---- Plans -------------------------------------------------------------
define('_AM_SUBSCRIPTIONS_PLAN_LIST',     'Manage Plans');
define('_AM_SUBSCRIPTIONS_PLAN_ADD',      'Add New Plan');
define('_AM_SUBSCRIPTIONS_PLAN_EDIT',     'Edit Plan');
define('_AM_SUBSCRIPTIONS_PLAN_NAME',     'Plan Name');
define('_AM_SUBSCRIPTIONS_PLAN_DESC',     'Description');
define('_AM_SUBSCRIPTIONS_PLAN_PRICE',    'Price');
define('_AM_SUBSCRIPTIONS_PLAN_CYCLE',    'Billing Cycle');
define('_AM_SUBSCRIPTIONS_PLAN_TRIAL',    'Trial Days');
define('_AM_SUBSCRIPTIONS_PLAN_SETUP',    'Setup Fee');
define('_AM_SUBSCRIPTIONS_PLAN_DISCOUNT', 'Annual Discount (%)');
define('_AM_SUBSCRIPTIONS_PLAN_FEATURES', 'Features (one per line)');
define('_AM_SUBSCRIPTIONS_PLAN_FEATURED', 'Mark as Featured');
define('_AM_SUBSCRIPTIONS_PLAN_ACTIVE',   'Active');
define('_AM_SUBSCRIPTIONS_PLAN_ORDER',    'Sort Order');
define('_AM_SUBSCRIPTIONS_PLAN_MAX_USERS','Max Users (0 = unlimited)');
define('_AM_SUBSCRIPTIONS_PLAN_MODEL',    'Pricing Model');
define('_AM_SUBSCRIPTIONS_PLAN_USAGE_UNIT','Usage Unit');
define('_AM_SUBSCRIPTIONS_PLAN_USAGE_PRICE','Price per Usage Unit');
define('_AM_SUBSCRIPTIONS_PLAN_SAVED',    'Plan saved successfully.');
define('_AM_SUBSCRIPTIONS_PLAN_DELETED',  'Plan deleted.');
define('_AM_SUBSCRIPTIONS_PLAN_NOT_FOUND','Plan not found.');

// ---- Subscriptions -----------------------------------------------------
define('_AM_SUBSCRIPTIONS_SUB_LIST',      'All Subscriptions');
define('_AM_SUBSCRIPTIONS_SUB_CANCEL',    'Cancel');
define('_AM_SUBSCRIPTIONS_SUB_CANCELLED', 'Subscription cancelled.');
define('_AM_SUBSCRIPTIONS_FILTER_STATUS', 'Filter by Status');
define('_AM_SUBSCRIPTIONS_FILTER_USER',   'Filtered by user');
define('_AM_SUBSCRIPTIONS_FILTER_CLEAR',  '✕ Clear filter');

// ---- Payments ----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_PAYMENT_LIST',  'Payment History');
define('_AM_SUBSCRIPTIONS_REFUND_AMOUNT', 'Refund Amount');
define('_AM_SUBSCRIPTIONS_REFUND_REASON', 'Refund Reason');
define('_AM_SUBSCRIPTIONS_ISSUE_REFUND',  'Issue Refund');
define('_AM_SUBSCRIPTIONS_REFUND_SUCCESS','Refund processed successfully.');
define('_AM_SUBSCRIPTIONS_REFUND_FAILED', 'Refund failed. Please check gateway settings.');
define('_AM_SUBSCRIPTIONS_USER_HISTORY',  'History');

// ---- Coupons -----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_COUPON_LIST',   'Manage Coupons');
define('_AM_SUBSCRIPTIONS_COUPON_ADD',    'Add New Coupon');
define('_AM_SUBSCRIPTIONS_COUPON_CODE',   'Code');
define('_AM_SUBSCRIPTIONS_COUPON_NAME',   'Name');
define('_AM_SUBSCRIPTIONS_COUPON_TYPE',   'Discount Type');
define('_AM_SUBSCRIPTIONS_COUPON_VALUE',  'Discount Value');
define('_AM_SUBSCRIPTIONS_COUPON_MIN',    'Minimum Order Amount');
define('_AM_SUBSCRIPTIONS_COUPON_USES',   'Max Uses');
define('_AM_SUBSCRIPTIONS_COUPON_VALID',  'Valid Period');
define('_AM_SUBSCRIPTIONS_COUPON_SAVED',  'Coupon saved.');
define('_AM_SUBSCRIPTIONS_COUPON_DELETED','Coupon deleted.');
define('_AM_SUBSCRIPTIONS_COUPON_NOT_FOUND','Coupon not found.');

// ---- Modules -----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_MODULE_LIST',   'Connected Modules');
define('_AM_SUBSCRIPTIONS_MODULE_ADD',    'Add Module Connection');
define('_AM_SUBSCRIPTIONS_MODULE_DIRNAME','Module Directory Name');
define('_AM_SUBSCRIPTIONS_MODULE_NAME',   'Module Name');
define('_AM_SUBSCRIPTIONS_MODULE_API_KEY','API Key');
define('_AM_SUBSCRIPTIONS_MODULE_WEBHOOK','Webhook URL');
define('_AM_SUBSCRIPTIONS_MODULE_RULES',  'Access Rules');
define('_AM_SUBSCRIPTIONS_MODULE_SAVED',  'Module connection saved.');
define('_AM_SUBSCRIPTIONS_MODULE_DELETED','Module connection deleted.');
define('_AM_SUBSCRIPTIONS_MODULE_NOT_FOUND','Module connection not found.');

// ---- Gateways ----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_GATEWAY_LIST',  'Payment Gateway Settings');
define('_AM_SUBSCRIPTIONS_GATEWAY_PAYPAL','PayPal Settings');
define('_AM_SUBSCRIPTIONS_GATEWAY_STRIPE','Stripe Settings');
define('_AM_SUBSCRIPTIONS_GATEWAY_MANUAL','Manual/Offline Payment');
define('_AM_SUBSCRIPTIONS_GATEWAY_SAVED', 'Gateway settings saved.');
define('_AM_SUBSCRIPTIONS_SANDBOX_MODE',  'Sandbox / Test Mode');
define('_AM_SUBSCRIPTIONS_API_KEY',       'API Key / Secret Key');
define('_AM_SUBSCRIPTIONS_BUSINESS_EMAIL','Business Email');

// ---- Reports -----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_REPORT_REVENUE','Revenue Report');
define('_AM_SUBSCRIPTIONS_REPORT_SUBS',   'Subscription Report');
define('_AM_SUBSCRIPTIONS_THIS_MONTH',    'This Month');
define('_AM_SUBSCRIPTIONS_LAST_MONTH',    'Last Month');
define('_AM_SUBSCRIPTIONS_ALL_TIME',      'All Time');

// ---- Webhooks ----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_WEBHOOK_LIST',  'Webhooks');
define('_AM_SUBSCRIPTIONS_WEBHOOK_ADD',   'Add Webhook');
define('_AM_SUBSCRIPTIONS_WEBHOOK_URL',   'Endpoint URL');
define('_AM_SUBSCRIPTIONS_WEBHOOK_EVENTS','Subscribe to Events');
define('_AM_SUBSCRIPTIONS_WEBHOOK_SECRET','Secret Key');
define('_AM_SUBSCRIPTIONS_WEBHOOK_SAVED', 'Webhook saved.');
define('_AM_SUBSCRIPTIONS_WEBHOOK_DELETED','Webhook deleted.');
define('_AM_SUBSCRIPTIONS_WEBHOOK_NOT_FOUND','Webhook not found.');

// ---- General -----------------------------------------------------------
define('_AM_SUBSCRIPTIONS_SAVE',          'Save');
define('_AM_SUBSCRIPTIONS_CANCEL',        'Cancel');
define('_AM_SUBSCRIPTIONS_DELETE',        'Delete');
define('_AM_SUBSCRIPTIONS_EDIT',          'Edit');
define('_AM_SUBSCRIPTIONS_ACTIONS',       'Actions');
define('_AM_SUBSCRIPTIONS_SAVE_ERROR',    'An error occurred. Please try again.');
define('_AM_SUBSCRIPTIONS_CONFIRM_DELETE','Are you sure you want to delete this item?');

// ---- Configuration Check & Server Status --------------------------------
define('_AM_SUBSCRIPTIONS_CONFIG_CHECK',  'Configuration Check');
define('_AM_SUBSCRIPTIONS_SERVER_STATUS', 'Server Status');

// ---- Dashboard stats ---------------------------------------------------
define('_AM_SUBSCRIPTIONS_ACTIVE_SUBS',       'Active Subscriptions');
define('_AM_SUBSCRIPTIONS_TRIAL_SUBS',        'In Trial');
define('_AM_SUBSCRIPTIONS_ACTIVE_PLANS',      'Active Plans');
define('_AM_SUBSCRIPTIONS_MONTH_REVENUE',     'Revenue This Month');
define('_AM_SUBSCRIPTIONS_ACTIVE_COUPONS',    'Active Coupons');
define('_AM_SUBSCRIPTIONS_CONNECTED_MODULES', 'Connected Modules (Active)');
define('_AM_SUBSCRIPTIONS_MODULES_INACTIVE',  'inactive');
define('_AM_SUBSCRIPTIONS_INACTIVE',          'inactive');
define('_AM_SUBSCRIPTIONS_ACTIVE_WEBHOOKS',   'Active Webhooks');
define('_AM_SUBSCRIPTIONS_INTEGRATIONS',      'Integrations & Configuration');
define('_AM_SUBSCRIPTIONS_VIEW_ALL',          'View all →');
