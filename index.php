<?php

declare(strict_types=1);
/**
 * Subscriptions - Main module entry point.
 */

use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\PlanHandler;
use XoopsModules\Subscriptions\Utility;

/** @var Helper $helper */

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_index.tpl';
require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

$helper = Helper::getInstance();

/** @var PlanHandler $planHandler */
$planHandler = $helper->getHandler('Plan');
$plans = $planHandler->getActivePlans();
$planData = [];

foreach ($plans as $plan) {
    $features = $plan->getFeatures();
    $featList = [];
    foreach ($features as $f) {
        $featList[] = $f->getVar('feature', 'n');
    }
    $trialDays = (int) $plan->getVar('trial_days');
    $planData[] = [
        'plan_id'       => (int) $plan->getVar('plan_id'),
        'name'          => $plan->getVar('name', 'n'),
        'description'   => $plan->getVar('description', 'n'),
        'price'         => Utility::formatMoney((float) $plan->getVar('price'), $plan->getVar('currency', 'n')),
        'price_raw'     => (float) $plan->getVar('price'),
        'billing_cycle' => $plan->getBillingCycleLabel(),
        'trial_days'    => $trialDays,
        'trial_badge'   => $trialDays > 0 ? sprintf(_MD_SUBSCRIPTIONS_TRIAL_BADGE, $trialDays) : '',
        'is_featured'   => (bool) (int) $plan->getVar('is_featured'),
        'features'      => $featList,
        'checkout_url'  => XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . (int) $plan->getVar('plan_id'),
    ];
}

// Check current user subscription
$currentSub = null;
global $xoopsUser;
if (is_object($xoopsUser)) {
    $subscriptionHandler = $helper->getHandler('Subscription');
    $sub = $subscriptionHandler->getActiveForUser((int) $xoopsUser->getVar('uid'));
    if ($sub && $sub->isActive()) {
        $subPlan = $sub->getPlan();
        $currentSub = [
            'status'        => $sub->getStatusLabel(),
            'plan_name'     => $subPlan ? $subPlan->getVar('name', 'n') : '',
            'period_end'    => Utility::formatDate((int) $sub->getVar('current_period_end')),
            'dashboard_url' => XOOPS_URL . '/modules/subscriptions/dashboard.php',
        ];
    }
}

$xoopsTpl->assign('xm_plans', $planData);
$xoopsTpl->assign('xm_current_sub', $currentSub);
$xoopsTpl->assign('xm_is_logged_in', is_object($xoopsUser));

// require_once XOOPS_ROOT_PATH . '/include/footer.php';
require XOOPS_ROOT_PATH . '/footer.php';
