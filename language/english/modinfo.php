<?php
/**
 * Subscriptions Language file - Module info (English)
 *
 * @package    subscriptions
 * @subpackage language
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

define('_MI_SUBSCRIPTIONS_NAME',          'Subscriptions');
define('_MI_SUBSCRIPTIONS_DESC',          'Paid memberships and service subscriptions for XOOPS. Supports multiple pricing models, payment gateways, coupons, trials, and third-party module integrations.');

// Menu items
define('_MI_SUBSCRIPTIONS_MENU_PLANS',     'Plans');
define('_MI_SUBSCRIPTIONS_MENU_DASHBOARD', 'My Membership');
define('_MI_SUBSCRIPTIONS_MENU_INVOICES',  'My Invoices');
define('_MI_SUBSCRIPTIONS_MENU_CLONE',     'Clone Module');

// Config labels
define('_MI_SUBSCRIPTIONS_CURRENCY',            'Currency');
define('_MI_SUBSCRIPTIONS_CURRENCY_DESC',       'Select the default currency for billing.');
define('_MI_SUBSCRIPTIONS_CURRENCY_SYMBOL',     'Currency Symbol');
define('_MI_SUBSCRIPTIONS_CURRENCY_SYMBOL_DESC','The symbol displayed before amounts (e.g. $, €).');
define('_MI_SUBSCRIPTIONS_TAX_RATE',            'Tax Rate (%)');
define('_MI_SUBSCRIPTIONS_TAX_RATE_DESC',       'Enter the tax percentage to apply to all invoices (0 to disable).');
define('_MI_SUBSCRIPTIONS_TRIAL_DAYS',          'Default Trial Days');
define('_MI_SUBSCRIPTIONS_TRIAL_DAYS_DESC',     'Default number of trial days for new plans (can be overridden per plan).');
define('_MI_SUBSCRIPTIONS_INVOICE_PREFIX',      'Invoice Number Prefix');
define('_MI_SUBSCRIPTIONS_INVOICE_PREFIX_DESC', 'Prefix added to all invoice numbers (e.g. INV-).');
define('_MI_SUBSCRIPTIONS_NOTIFY_EMAIL',        'Email Notifications');
define('_MI_SUBSCRIPTIONS_NOTIFY_EMAIL_DESC',   'Send email notifications to users on payment confirmation.');
define('_MI_SUBSCRIPTIONS_ACTIVE_GATEWAY',      'Active Payment Gateway');
define('_MI_SUBSCRIPTIONS_ACTIVE_GATEWAY_DESC', 'Select which payment gateway processes new subscriptions.');
define('_MI_SUBSCRIPTIONS_MULTI_SUB',           'Allow Multiple Subscriptions');
define('_MI_SUBSCRIPTIONS_MULTI_SUB_DESC',      'Allow users to have more than one active subscription at a time.');
define('_MI_SUBSCRIPTIONS_GRACE_PERIOD',        'Grace Period (days)');
define('_MI_SUBSCRIPTIONS_GRACE_PERIOD_DESC',   'Number of days after subscription expiry before access is revoked.');

// Block
define('_MI_SUBSCRIPTIONS_BLOCK_PLANS',         'Subscriptions: Featured Plans');
define('_MI_SUBSCRIPTIONS_BLOCK_PLANS_DESC',    'Displays a list of featured membership plans.');

// Sample / test data buttons (Preferences page labels)
define('_MI_SUBSCRIPTIONS_SHOW_SAMPLE_BUTTON',      'Show Sample Data Buttons?');
define('_MI_SUBSCRIPTIONS_SHOW_SAMPLE_BUTTON_DESC', 'If Yes, Import / Export / Clear sample data buttons are shown in the Admin dashboard. Set to No once you have real data loaded.');

// Developer tools preference (Preferences page labels)
define('_MI_SUBSCRIPTIONS_SHOW_DEV_TOOLS',          'Show Developer Tools?');
define('_MI_SUBSCRIPTIONS_SHOW_DEV_TOOLS_DESC',     'If Yes, additional developer/diagnostic tools are shown in the admin panel.');
