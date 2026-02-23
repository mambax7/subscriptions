<?php
/**
 * Subscriptions - IPN / Webhook receiver
 *
 * Handles callbacks from payment gateways (PayPal IPN, Stripe Webhook).
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    AccessControl,
    GatewayFactory,
    Helper,
    Payment,
    Subscription,
    Utility,
    Webhook
};

// Note: No XOOPS session/theme needed here — gateway servers POST directly.
// Bootstrap XOOPS core without starting the theme engine.
if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}
require_once XOOPS_ROOT_PATH . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/preloads/autoloader.php';
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/functions.php';
// Load language constants needed for email notifications (no XOOPS header in IPN context)
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/language/english/main.php';

$gatewayId = isset($_GET['gateway']) ? preg_replace('/[^a-z0-9_-]/', '', strtolower($_GET['gateway'])) : '';
if (empty($gatewayId)) {
    http_response_code(400);
    exit('Bad Request');
}

try {
    $gateway = GatewayFactory::create($gatewayId);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    exit('Unknown gateway');
}

// For Stripe: validate signature before reading body
if ($gatewayId === 'stripe') {
    $rawBody  = file_get_contents('php://input');
    $sigHeader= $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    /** @var \XoopsModules\Subscriptions\Gateway\StripeGateway $gateway */
    if (!$gateway->validateWebhookSignature($rawBody, $sigHeader)) {
        http_response_code(401);
        exit('Signature mismatch');
    }
    $data = json_decode($rawBody, true) ?? [];
} else {
    // PayPal IPN
    $data = $_POST;
}

$result = $gateway->handleCallback($data);

if (!$result['success']) {
    http_response_code(200); // Always 200 for IPN to prevent retries on business-logic failures
    exit('IGNORED');
}

$paymentId = (int)($result['payment_id'] ?? 0);
$txnId     = $result['txn_id'] ?? '';
$amount    = (float)($result['amount']   ?? 0);

if ($paymentId <= 0) {
    http_response_code(200);
    exit('NO_PAYMENT_ID');
}

$helper = Helper::getInstance();
/** @var PaymentHandler $paymentHandler */
$paymentHandler = $helper->getHandler('Payment');
$payment        = $paymentHandler->get($paymentId);
if (!$payment) {
    http_response_code(200);
    exit('PAYMENT_NOT_FOUND');
}

// Idempotency: already processed?
if ($payment->getVar('status', 'n') === Payment::STATUS_COMPLETED) {
    http_response_code(200);
    exit('ALREADY_PROCESSED');
}

// Mark payment completed
$payment->markCompleted($txnId, $result);

// Activate subscription
$subscriptionHandler = $helper->getHandler('Subscription');
$sub        = $subscriptionHandler->get((int)$payment->getVar('sub_id', 'n'));
if ($sub) {
    $sub->setVar('status',     Subscription::STATUS_ACTIVE);
    $sub->setVar('updated_at', time());
    $subscriptionHandler->insert($sub);

    // Mark invoice paid
    $invHandler = $helper->getHandler('Invoice');
    $inv        = $invHandler->get((int)$payment->getVar('invoice_id', 'n'));
    if ($inv) {
        $inv->markPaid();
    }

    // Record coupon usage if applicable
    $couponId = (int)$sub->getVar('coupon_id', 'n');
    if ($couponId > 0) {
        $couponUsageHandler = $helper->getHandler('CouponUsage');
        $couponUsageHandler->recordUsage($couponId, (int)$sub->getVar('user_id'), (int)$sub->getVar('sub_id'));
    }

    // Sync XOOPS groups — add user to any groups granted by the plan's access rules
    $accessControl = new AccessControl();
    $accessControl->syncXoopsGroups((int)$sub->getVar('user_id'), true);

    // Fire webhook
    $whHandler = $helper->getHandler('Webhook');
    $whHandler->fire(Webhook::EVENT_SUBSCRIPTION_CREATED, [
        'sub_id'  => (int)$sub->getVar('sub_id'),
        'user_id' => (int)$sub->getVar('user_id'),
        'plan_id' => (int)$sub->getVar('plan_id'),
    ]);
    $whHandler->fire(Webhook::EVENT_PAYMENT_COMPLETED, [
        'payment_id' => $paymentId,
        'sub_id'     => (int)$sub->getVar('sub_id'),
        'user_id'    => (int)$sub->getVar('user_id'),
        'amount'     => $amount,
        'txn_id'     => $txnId,
    ]);

    // Send email notification
    if (Utility::config('notify_email', 1)) {
        subscriptions_send_payment_email($sub, $payment);
    }
}

http_response_code(200);
echo 'OK';

/**
 * Send a payment confirmation email to the subscriber
 *
 * @param Subscription $sub
 * @param Payment      $payment
 */
function subscriptions_send_payment_email(Subscription $sub, Payment $payment): void
{
    $userHandler = xoops_getHandler('user');
    $user        = $userHandler->get((int)$sub->getVar('user_id', 'n'));
    if (!$user) {
        return;
    }
    $plan  = $sub->getPlan();
    $mailer = xoops_getMailer();
    $mailer->setToUsers([$user]);
    $mailer->setFromName($GLOBALS['xoopsConfig']['sitename'] ?? '');
    $mailer->setSubject(_MD_SUBSCRIPTIONS_EMAIL_PAYMENT_SUBJECT);
    $mailer->setBody(sprintf(
        _MD_SUBSCRIPTIONS_EMAIL_PAYMENT_BODY,
        $user->getVar('uname', 'n'),
        $plan ? $plan->getVar('name', 'n') : '',
        Utility::formatMoney((float)$payment->getVar('amount', 'n')),
        Utility::formatDate((int)$sub->getVar('current_period_end'))
    ));
    $mailer->send();
}
