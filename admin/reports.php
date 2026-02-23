<?php
/**
 * Subscriptions Admin Reports
 *
 * @package    subscriptions
 * @subpackage admin
 */

use XoopsModules\Subscriptions\{
    Helper,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_admin_reports.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();


$helper = Helper::getInstance();
$helper->loadLanguage('admin');



$paymentHandler = $helper->getHandler('Payment');
$subscriptionHandler     = $helper->getHandler('Subscription');

$period = isset($_GET['period']) ? htmlspecialchars(trim($_GET['period']), ENT_QUOTES) : 'this_month';

$now        = time();
$monthStart = mktime(0, 0, 0, (int)date('n'), 1, (int)date('Y'));
$lastMonthStart = mktime(0, 0, 0, (int)date('n') - 1, 1, (int)date('Y'));
$lastMonthEnd   = $monthStart - 1;

switch ($period) {
    case 'last_month':
        $from = $lastMonthStart;
        $to   = $lastMonthEnd;
        break;
    case 'all_time':
        $from = 0;
        $to   = $now;
        break;
    default: // this_month
        $from = $monthStart;
        $to   = $now;
        break;
}

// Revenue in period
$revCriteria = new \CriteriaCompo();
$revCriteria->add(new \Criteria('status', 'completed'));
if ($from > 0) {
    $revCriteria->add(new \Criteria('paid_at', $from, '>='));
}
$revCriteria->add(new \Criteria('paid_at', $to, '<='));
$payments    = $paymentHandler->getAll($revCriteria);
$revenue     = 0.0;
$payCount    = 0;
foreach ($payments as $p) {
    $revenue  += (float)$p->getVar('amount', 'n');
    $payCount++;
}

// Active subscriptions
$activeCriteria = new \Criteria('status', "('active','trial')", 'IN');
$activeSubs     = $subscriptionHandler->getCount($activeCriteria);

// New subscriptions in period
$newSubCriteria = new \CriteriaCompo();
if ($from > 0) {
    $newSubCriteria->add(new \Criteria('created_at', $from, '>='));
}
$newSubCriteria->add(new \Criteria('created_at', $to, '<='));
$newSubs = $subscriptionHandler->getCount($newSubCriteria);

$xoopsTpl->assign('xm_revenue',     Utility::formatMoney($revenue));
$xoopsTpl->assign('xm_pay_count',   $payCount);
$xoopsTpl->assign('xm_active_subs', $activeSubs);
$xoopsTpl->assign('xm_new_subs',    $newSubs);
$xoopsTpl->assign('xm_period',      $period);
$xoopsTpl->assign('xm_admin_nav',   Utility::adminNav());

require __DIR__ . '/admin_footer.php';
