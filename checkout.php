<?php
/**
 * Subscriptions - Checkout page
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    Helper,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_checkout.tpl';

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

Utility::requireLogin();
global $xoopsUser;
$userId = (int)$xoopsUser->getVar('uid');

$helper      = Helper::getInstance();
$planHandler = $helper->getHandler('Plan');
$planId      = (int)($_GET['plan'] ?? 0);
$plan        = $planId > 0 ? $planHandler->get($planId) : null;

if (!$plan || !(int)$plan->getVar('is_active')) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/plans.php', 2, _MD_SUBSCRIPTIONS_PLAN_NOT_FOUND);
}

// Check if user already has active subscription
$subscriptionHandler  = $helper->getHandler('Subscription');
$existingSub = $subscriptionHandler->getActiveForUser($userId, $planId);
if ($existingSub && $existingSub->isActive()) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/dashboard.php', 2, _MD_SUBSCRIPTIONS_ALREADY_SUBSCRIBED);
}

$price    = (float)$plan->getVar('price');
$discount = 0.0;
$coupon   = null;
$error    = '';

// Handle POST (apply coupon or proceed to payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Utility::verifyToken($_POST['token'] ?? '', 'checkout_' . $planId)) {
        redirect_header(XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . $planId, 2, _NOPERM);
    }
    $couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));
    if ($couponCode !== '') {
        $couponHandler = $helper->getHandler('Coupon');
        $result        = $couponHandler->validate($couponCode, $userId, $planId, $price);
        if ($result['valid']) {
            $discount = $result['discount'];
            $coupon   = $result['coupon'];
        } else {
            $error = $result['error'];
        }
    }
}

$gatewayId = Helper::getInstance()->getConfig('active_gateway') ?? 'paypal';
$finalPrice  = max(0, $price - $discount);
$taxRate     = (float)Utility::config('tax_rate', 0);
$taxAmount   = round($finalPrice * $taxRate / 100, 2);
$totalAmount = $finalPrice + $taxAmount;

$xoopsTpl->assign('xm_plan',         Utility::objVars($plan));
$xoopsTpl->assign('xm_plan_name',    $plan->getVar('name', 'n'));
$planFeatures = $plan->getFeatures();
$planFeatList = [];
foreach ($planFeatures as $pf) { $planFeatList[] = $pf->getVar('feature', 'n'); }
$xoopsTpl->assign('xm_plan_features', $planFeatList);
$xoopsTpl->assign('xm_price',        Utility::formatMoney($price, $plan->getVar('currency', 'n')));
$xoopsTpl->assign('xm_discount',     Utility::formatMoney($discount, $plan->getVar('currency', 'n')));
$xoopsTpl->assign('xm_tax',          Utility::formatMoney($taxAmount, $plan->getVar('currency', 'n')));
$xoopsTpl->assign('xm_total',        Utility::formatMoney($totalAmount, $plan->getVar('currency', 'n')));
$xoopsTpl->assign('xm_total_raw',    $totalAmount);
$xoopsTpl->assign('xm_coupon_code',  $coupon ? $coupon->getVar('code', 'n') : '');
$xoopsTpl->assign('xm_discount_raw', $discount);
$xoopsTpl->assign('xm_coupon_id',    $coupon ? (int)$coupon->getVar('coupon_id') : 0);
$xoopsTpl->assign('xm_gateway',      $gatewayId);
$trialDays = (int)$plan->getVar('trial_days');
$xoopsTpl->assign('xm_trial_days',   $trialDays);
$xoopsTpl->assign('xm_trial_badge',  $trialDays > 0 ? sprintf(_MD_SUBSCRIPTIONS_TRIAL_BADGE, $trialDays) : '');
$xoopsTpl->assign('xm_error',        $error);
$xoopsTpl->assign('xm_token',        Utility::generateToken('checkout_' . $planId));
$xoopsTpl->assign('xm_pay_token',    Utility::generateToken('pay_' . $planId));

require XOOPS_ROOT_PATH . '/footer.php';
