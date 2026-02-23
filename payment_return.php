<?php
/**
 * Subscriptions - Payment return page (after gateway redirect)
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    Helper,
    Payment,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_payment_return.tpl';

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

Utility::requireLogin();
global $xoopsUser;

// Determine payment status by checking the payment record
$paymentId = (int)($_GET['payment_id'] ?? 0);
$success   = false;
$failed    = false;
$message   = _MD_SUBSCRIPTIONS_PAYMENT_PENDING; // safe default

$helper = Helper::getInstance();
if ($paymentId > 0) {
    $paymentHandler = $helper->getHandler('Payment');
    $payment        = $paymentHandler->get($paymentId);
    if ($payment && (int)$payment->getVar('user_id', 'n') === (int)$xoopsUser->getVar('uid')) {
        $status  = $payment->getVar('status', 'n');
        $success = ($status === Payment::STATUS_COMPLETED);
        $failed  = ($status === Payment::STATUS_FAILED);
        if ($success) {
            $message = _MD_SUBSCRIPTIONS_PAYMENT_SUCCESS;
        } elseif ($failed) {
            $message = _MD_SUBSCRIPTIONS_PAYMENT_FAILED;
        } else {
            $message = _MD_SUBSCRIPTIONS_PAYMENT_PENDING;
        }
    }
}

$xoopsTpl->assign('xm_success',      $success);
$xoopsTpl->assign('xm_failed',       $failed);
$xoopsTpl->assign('xm_message',      $message);
$xoopsTpl->assign('xm_dashboard_url',XOOPS_URL . '/modules/subscriptions/dashboard.php');
$xoopsTpl->assign('xm_plans_url',    XOOPS_URL . '/modules/subscriptions/plans.php');

require XOOPS_ROOT_PATH . '/footer.php';
