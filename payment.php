<?php
/**
 * Subscriptions - Payment processing (redirect to gateway)
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    GatewayFactory,
    Helper,
    Utility,
    Webhook
};

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

Utility::requireLogin();
global $xoopsUser;
$userId = (int)$xoopsUser->getVar('uid');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_header(XOOPS_URL . '/modules/subscriptions/plans.php', 2, _NOPERM);
}

$planId   = (int)($_POST['plan_id'] ?? 0);
$couponId = (int)($_POST['coupon_id'] ?? 0);
$discount = (float)($_POST['discount_raw'] ?? 0);
$token    = $_POST['token'] ?? '';

if (!Utility::verifyToken($token, 'pay_' . $planId)) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/plans.php', 2, _NOPERM);
}

$helper = Helper::getInstance();
$planHandler = $helper->getHandler('Plan');
$plan        = $planHandler->get($planId);
if (!$plan || !(int)$plan->getVar('is_active')) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/plans.php', 2, _MD_SUBSCRIPTIONS_PLAN_NOT_FOUND);
}

$gatewayId = $helper->getConfig('active_gateway') ?: 'paypal';
$currency  = $plan->getVar('currency', 'n') ?: 'USD';
$taxRate   = (float)($helper->getConfig('tax_rate') ?: 0);
$basePrice = (float)$plan->getVar('price');
$netPrice  = max(0, $basePrice - $discount);
$taxAmt    = round($netPrice * $taxRate / 100, 2);
$total     = $netPrice + $taxAmt;

// Create the subscription record (pending/trial)
$subscriptionHandler  = $helper->getHandler('Subscription');
$sub         = $subscriptionHandler->createSubscription($userId, $plan, $gatewayId, $couponId, $discount);
if (!$sub) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/plans.php', 2, _MD_SUBSCRIPTIONS_SUB_CREATE_FAILED);
}

// Create invoice
$invHandler = $helper->getHandler('Invoice');
$invoice    = $invHandler->createForSubscription(
    $userId,
    (int)$sub->getVar('sub_id'),
    $basePrice,
    $taxRate,
    $discount,
    $currency,
    (int)$sub->getVar('current_period_start'),
    (int)$sub->getVar('current_period_end')
);
$itemHandler = $helper->getHandler('InvoiceItem');
$itemHandler->addItem(
    (int)$invoice->getVar('invoice_id'),
    $plan->getVar('name', 'n'),
    1,
    $basePrice
);

// Create pending payment record
$payHandler = $helper->getHandler('Payment');
$payment    = $payHandler->createPending(
    $userId,
    (int)$sub->getVar('sub_id'),
    $total,
    $gatewayId,
    $currency,
    (int)$invoice->getVar('invoice_id')
);

// For trial plans with $0 charge, activate immediately
if ((int)$plan->getVar('trial_days') > 0 && $total <= 0) {
    $payment->markCompleted('TRIAL-' . time(), ['trial' => true]);
    // Record coupon usage
    if ($couponId > 0) {
        $couponUsageHandler = $helper->getHandler('CouponUsage');
        $couponUsageHandler->recordUsage($couponId, $userId, (int)$sub->getVar('sub_id'));
    }
    // Fire webhook
    $whHandler = $helper->getHandler('Webhook');
    $whHandler->fire(Webhook::EVENT_TRIAL_STARTED, [
        'sub_id'  => (int)$sub->getVar('sub_id'),
        'user_id' => $userId,
    ]);
    redirect_header(XOOPS_URL . '/modules/subscriptions/dashboard.php', 2, _MD_SUBSCRIPTIONS_TRIAL_STARTED);
}

// Redirect to payment gateway
try {
    $gateway = GatewayFactory::create($gatewayId);
    $result  = $gateway->initiatePayment($payment, [
        'item_name'  => $plan->getVar('name', 'n'),
        'return_url' => XOOPS_URL . '/modules/subscriptions/payment_return.php',
        'cancel_url' => XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . $planId,
        'notify_url' => XOOPS_URL . '/modules/subscriptions/ipn.php?gateway=' . rawurlencode($gatewayId),
    ]);
    if (!empty($result['error'])) {
        redirect_header(XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . $planId, 2, $result['error']);
    }
    header('Location: ' . $result['redirect_url']);
    exit;
} catch (Exception $e) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . $planId, 2, $e->getMessage());
}
