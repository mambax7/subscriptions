<?php

declare(strict_types=1);
/**
 * Subscriptions - Paid Memberships & Service Subscriptions for XOOPS.
 *
 * @author     Subscriptions Development Team
 * @copyright  Copyright (c) 2024 XOOPS Project
 * @license    GNU General Public License v2 or later
 *
 * @link       https://github.com/mambax7/subscriptions
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

// -------------------------------------------------------------------------
// Module basic information
// -------------------------------------------------------------------------
$modversion['version'] = '1.0.0';
$modversion['module_status'] = 'Beta.1';
$modversion['release_date'] = '2026/02/23';
$modversion['name'] = _MI_SUBSCRIPTIONS_NAME;
$modversion['description'] = _MI_SUBSCRIPTIONS_DESC;
$modversion['credits'] = 'XOOPS Project';
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL v2 or later';
$modversion['license_url'] = 'https://www.gnu.org/licenses/gpl-2.0.html';
$modversion['official'] = 0;
$modversion['image'] = 'assets/images/logoModule.png';
$modversion['dirname'] = 'subscriptions';
$modversion['modactivate'] = 1;
$modversion['author'] = 'Subscriptions Team';
$modversion['nickname'] = 'subscriptions';
$modversion['author_mail'] = 'admin@example.com';
$modversion['author_website'] = 'https://github.com/mambax7/subscriptions';
$modversion['module_website_url'] = 'github.com/mambax7/subscriptions';
$modversion['module_website_name'] = 'Subscriptions on GitHub';

// -------------------------------------------------------------------------
// Minimum requirements
// -------------------------------------------------------------------------
$modversion['min_php'] = '8.2';
$modversion['min_xoops'] = '2.5.12';

// -------------------------------------------------------------------------
// Admin things
// -------------------------------------------------------------------------
$modversion['system_menu'] = 1;
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// -------------------------------------------------------------------------
// Main menu
// -------------------------------------------------------------------------
$modversion['hasMain'] = 1;

$modversion['sub'][1]['name'] = _MI_SUBSCRIPTIONS_MENU_PLANS;
$modversion['sub'][1]['url'] = 'plans.php';

$modversion['sub'][2]['name'] = _MI_SUBSCRIPTIONS_MENU_DASHBOARD;
$modversion['sub'][2]['url'] = 'dashboard.php';

$modversion['sub'][3]['name'] = _MI_SUBSCRIPTIONS_MENU_INVOICES;
$modversion['sub'][3]['url'] = 'invoices.php';

// -------------------------------------------------------------------------
// Database tables
// -------------------------------------------------------------------------
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

$modversion['tables'][] = 'subscriptions_plans';
$modversion['tables'][] = 'subscriptions_plan_features';
$modversion['tables'][] = 'subscriptions_subscriptions';
$modversion['tables'][] = 'subscriptions_payments';
$modversion['tables'][] = 'subscriptions_invoices';
$modversion['tables'][] = 'subscriptions_invoice_items';
$modversion['tables'][] = 'subscriptions_coupons';
$modversion['tables'][] = 'subscriptions_coupon_usage';
$modversion['tables'][] = 'subscriptions_connected_modules';
$modversion['tables'][] = 'subscriptions_module_access_rules';
$modversion['tables'][] = 'subscriptions_usage_logs';
$modversion['tables'][] = 'subscriptions_webhooks';
$modversion['tables'][] = 'subscriptions_refunds';
$modversion['tables'][] = 'subscriptions_gateway_configs';

// -------------------------------------------------------------------------
// Templates
// -------------------------------------------------------------------------
$modversion['templates'][1]['file'] = 'subscriptions_index.tpl';
$modversion['templates'][1]['description'] = 'Main index page';

$modversion['templates'][2]['file'] = 'subscriptions_plans.tpl';
$modversion['templates'][2]['description'] = 'Plans listing page';

$modversion['templates'][3]['file'] = 'subscriptions_dashboard.tpl';
$modversion['templates'][3]['description'] = 'User subscription dashboard';

$modversion['templates'][4]['file'] = 'subscriptions_invoice.tpl';
$modversion['templates'][4]['description'] = 'Invoice detail page';

$modversion['templates'][5]['file'] = 'subscriptions_invoices.tpl';
$modversion['templates'][5]['description'] = 'Invoice listing page';

$modversion['templates'][6]['file'] = 'subscriptions_checkout.tpl';
$modversion['templates'][6]['description'] = 'Checkout page';

$modversion['templates'][7]['file'] = 'subscriptions_payment_return.tpl';
$modversion['templates'][7]['description'] = 'Payment return page';

$modversion['templates'][8]['file'] = 'subscriptions_admin_plans.tpl';
$modversion['templates'][8]['description'] = 'Admin plans management';

$modversion['templates'][9]['file'] = 'subscriptions_admin_subscriptions.tpl';
$modversion['templates'][9]['description'] = 'Admin subscriptions management';

$modversion['templates'][10]['file'] = 'subscriptions_admin_coupons.tpl';
$modversion['templates'][10]['description'] = 'Admin coupons management';

$modversion['templates'][11]['file'] = 'subscriptions_admin_gateways.tpl';
$modversion['templates'][11]['description'] = 'Admin gateway configuration';

$modversion['templates'][12]['file'] = 'subscriptions_admin_modules.tpl';
$modversion['templates'][12]['description'] = 'Admin connected modules';

$modversion['templates'][13]['file'] = 'subscriptions_admin_index.tpl';
$modversion['templates'][13]['description'] = 'Admin dashboard';

$modversion['templates'][14]['file'] = 'subscriptions_admin_payments.tpl';
$modversion['templates'][14]['description'] = 'Admin payments list';

$modversion['templates'][15]['file'] = 'subscriptions_admin_webhooks.tpl';
$modversion['templates'][15]['description'] = 'Admin webhooks list';

$modversion['templates'][16]['file'] = 'subscriptions_admin_coupon_form.tpl';
$modversion['templates'][16]['description'] = 'Admin coupon add/edit form';

$modversion['templates'][17]['file'] = 'subscriptions_admin_module_form.tpl';
$modversion['templates'][17]['description'] = 'Admin connected module add/edit form';

$modversion['templates'][18]['file'] = 'subscriptions_admin_webhook_form.tpl';
$modversion['templates'][18]['description'] = 'Admin webhook add/edit form';

$modversion['templates'][19]['file'] = 'subscriptions_admin_plan_form.tpl';
$modversion['templates'][19]['description'] = 'Admin plan add/edit form';

$modversion['templates'][20]['file'] = 'subscriptions_admin_reports.tpl';
$modversion['templates'][20]['description'] = 'Admin reports dashboard';

$modversion['templates'][21]['file'] = 'subscriptions_block_plans.tpl';
$modversion['templates'][21]['description'] = 'Featured plans block';

// -------------------------------------------------------------------------
// Module config options
// -------------------------------------------------------------------------
$i = 0;

$modversion['config'][++$i] = [
    'name'        => 'currency',
    'title'       => '_MI_SUBSCRIPTIONS_CURRENCY',
    'description' => '_MI_SUBSCRIPTIONS_CURRENCY_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'USD',
    'options'     => ['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'CAD' => 'CAD', 'AUD' => 'AUD'],
];

$modversion['config'][++$i] = [
    'name'        => 'currency_symbol',
    'title'       => '_MI_SUBSCRIPTIONS_CURRENCY_SYMBOL',
    'description' => '_MI_SUBSCRIPTIONS_CURRENCY_SYMBOL_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '$',
];

$modversion['config'][++$i] = [
    'name'        => 'tax_rate',
    'title'       => '_MI_SUBSCRIPTIONS_TAX_RATE',
    'description' => '_MI_SUBSCRIPTIONS_TAX_RATE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'float',
    'default'     => 0.0,
];

$modversion['config'][++$i] = [
    'name'        => 'trial_days',
    'title'       => '_MI_SUBSCRIPTIONS_TRIAL_DAYS',
    'description' => '_MI_SUBSCRIPTIONS_TRIAL_DAYS_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][++$i] = [
    'name'        => 'invoice_prefix',
    'title'       => '_MI_SUBSCRIPTIONS_INVOICE_PREFIX',
    'description' => '_MI_SUBSCRIPTIONS_INVOICE_PREFIX_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'INV-',
];

$modversion['config'][++$i] = [
    'name'        => 'notify_email',
    'title'       => '_MI_SUBSCRIPTIONS_NOTIFY_EMAIL',
    'description' => '_MI_SUBSCRIPTIONS_NOTIFY_EMAIL_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][++$i] = [
    'name'        => 'active_gateway',
    'title'       => '_MI_SUBSCRIPTIONS_ACTIVE_GATEWAY',
    'description' => '_MI_SUBSCRIPTIONS_ACTIVE_GATEWAY_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'paypal',
    'options'     => ['paypal' => 'PayPal', 'stripe' => 'Stripe', 'manual' => 'Manual/Offline'],
];

$modversion['config'][++$i] = [
    'name'        => 'allow_multi_subs',
    'title'       => '_MI_SUBSCRIPTIONS_MULTI_SUB',
    'description' => '_MI_SUBSCRIPTIONS_MULTI_SUB_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][++$i] = [
    'name'        => 'grace_period_days',
    'title'       => '_MI_SUBSCRIPTIONS_GRACE_PERIOD',
    'description' => '_MI_SUBSCRIPTIONS_GRACE_PERIOD_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 3,
];

$modversion['config'][++$i] = [
    'name'        => 'displaySampleButton',
    'title'       => '_MI_SUBSCRIPTIONS_SHOW_SAMPLE_BUTTON',
    'description' => '_MI_SUBSCRIPTIONS_SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][++$i] = [
    'name'        => 'displayDeveloperTools',
    'title'       => '_MI_SUBSCRIPTIONS_SHOW_DEV_TOOLS',
    'description' => '_MI_SUBSCRIPTIONS_SHOW_DEV_TOOLS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

// -------------------------------------------------------------------------
// Blocks
// -------------------------------------------------------------------------
$modversion['blocks'][1]['file'] = 'blocks/subscriptions_blocks.php';
$modversion['blocks'][1]['name'] = _MI_SUBSCRIPTIONS_BLOCK_PLANS;
$modversion['blocks'][1]['description'] = _MI_SUBSCRIPTIONS_BLOCK_PLANS_DESC;
$modversion['blocks'][1]['show_func'] = 'b_subscriptions_plans_show';
$modversion['blocks'][1]['edit_func'] = 'b_subscriptions_plans_edit';
$modversion['blocks'][1]['options'] = '5|1';
$modversion['blocks'][1]['template'] = 'subscriptions_block_plans.tpl';
$modversion['blocks'][1]['can_clone'] = 1;
