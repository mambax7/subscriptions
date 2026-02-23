<?php

declare(strict_types=1);
/**
 * Subscriptions Admin main index.
 */

use XoopsModules\Subscriptions\Common\TestdataButtons;
use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Subscription;
use XoopsModules\Subscriptions\Utility;

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_admin_index.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');

$helper = Helper::getInstance();
$helper->loadLanguage('admin');
$helper->loadLanguage('common');

xoops_cp_header();
Utility::addAdminAssets();

// ------------- Test Data Buttons -------------------------------------------
// displaySampleButton preference is the single source of truth:
//   1 → show 4 action buttons (Import / Export / Clear / Hide)
//   0 → show nothing (whether set via Preferences page or the in-page Hide btn)
//
// The op switch must run before renderButton() so that hide/show redirects
// happen before any output is assigned.
// renderButton() returns the HTML string (do NOT use displayButton() which
// echoes immediately, before the Smarty template renders).
$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : '';
switch ($op) {
    case 'hide_buttons':
        TestdataButtons::hideButtons();   // sets preference to 0, then redirects

        break;
    case 'show_buttons':
        TestdataButtons::showButtons();   // sets preference to 1, then redirects

        break;
}

if ((int) $helper->getConfig('displaySampleButton') === 1) {
    TestdataButtons::loadButtonConfig($adminObject);
    $xoopsTpl->assign('xm_testdata_buttons', $adminObject->renderButton('left', ''));
} else {
    $xoopsTpl->assign('xm_testdata_buttons', '');
}
// ------------- End Test Data Buttons ---------------------------------------

// Handlers
$subscriptionHandler = $helper->getHandler('Subscription');
$planHandler = $helper->getHandler('Plan');
$paymentHandler = $helper->getHandler('Payment');
$couponHandler = $helper->getHandler('Coupon');
$moduleHandler = $helper->getHandler('ConnectedModule');
$webhookHandler = $helper->getHandler('Webhook');

// Subscription stats
$activeCriteria = new CriteriaCompo();
$activeCriteria->add(new Criteria('status', sprintf("('%s','%s')", Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL), 'IN'));
$totalActive = $subscriptionHandler->getCount($activeCriteria);
$totalTrials = $subscriptionHandler->getCount(new Criteria('status', Subscription::STATUS_TRIAL));

// Plan stats
$totalPlans = $planHandler->getCount(new Criteria('is_active', 1));

// Revenue this month
$monthStart = mktime(0, 0, 0, (int) date('n'), 1, (int) date('Y'));
$revCriteria = new CriteriaCompo();
$revCriteria->add(new Criteria('status', 'completed'));
$revCriteria->add(new Criteria('paid_at', $monthStart, '>='));
$payments = $paymentHandler->getAll($revCriteria);
$monthRevenue = 0.0;
foreach ($payments as $p) {
    $monthRevenue += (float) $p->getVar('amount', 'n');
}

// Coupon stats
$totalCoupons = $couponHandler->getCount(new Criteria('is_active', 1));

// Connected module stats
$totalModulesActive = $moduleHandler->getCount(new Criteria('is_active', 1));
$totalModulesInactive = $moduleHandler->getCount(new Criteria('is_active', 0));

// Webhook stats
$totalWebhooks = $webhookHandler->getCount(new Criteria('is_active', 1));

$xoopsTpl->assign('xm_total_active', $totalActive);
$xoopsTpl->assign('xm_total_trials', $totalTrials);
$xoopsTpl->assign('xm_total_plans', $totalPlans);
$xoopsTpl->assign('xm_month_revenue', Utility::formatMoney($monthRevenue));
$xoopsTpl->assign('xm_total_coupons', $totalCoupons);
$xoopsTpl->assign('xm_total_modules_active', $totalModulesActive);
$xoopsTpl->assign('xm_total_modules_inactive', $totalModulesInactive);
$xoopsTpl->assign('xm_total_webhooks', $totalWebhooks);
$xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

// ------------- Configuration Check / Server Status / Cron tip --------------
$xoopsTpl->assign('xm_cron_tip', Utility::getCronTip());
$xoopsTpl->assign('xm_config_check', Utility::getConfigCheck($helper));
$xoopsTpl->assign('xm_server_stats', Utility::getServerStats());
// ---------------------------------------------------------------------------

require __DIR__ . '/admin_footer.php';
