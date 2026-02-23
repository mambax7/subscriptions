<?php
/**
 * Subscriptions - User subscription dashboard
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    AccessControl,
    Helper,
    Subscription,
    Utility,
    Webhook
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_dashboard.tpl';

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

Utility::requireLogin();
global $xoopsUser;
$userId = (int)$xoopsUser->getVar('uid');

$helper              = Helper::getInstance();
$subscriptionHandler = $helper->getHandler('Subscription');
$invoiceHandler      = $helper->getHandler('Invoice');
$paymentHandler      = $helper->getHandler('Payment');
$usageLogHandler     = $helper->getHandler('UsageLog');
$whHandler           = $helper->getHandler('Webhook');

// -----------------------------------------------------------------------
// Handle POST actions BEFORE building template data (may redirect)
// -----------------------------------------------------------------------
$op = isset($_POST['op']) ? htmlspecialchars(trim($_POST['op']), ENT_QUOTES) : '';
$sub = $subscriptionHandler->getActiveForUser($userId);

if ($op === 'cancel' && $sub) {
    $cancelToken = $_POST['token'] ?? '';
    if (Utility::verifyToken($cancelToken, 'cancel_sub_' . $sub->getVar('sub_id'))) {
        $reason      = trim($_POST['reason'] ?? '');
        $atPeriodEnd = (isset($_POST['cancel_when']) && $_POST['cancel_when'] === 'period_end');

        $sub->cancel($reason, $atPeriodEnd);

        if (!$atPeriodEnd) {
            // Immediate cancellation: remove from XOOPS groups and fire webhook
            $accessControl = new AccessControl();
            $accessControl->syncXoopsGroups($userId, false);
            $whHandler->fire(Webhook::EVENT_SUBSCRIPTION_CANCELLED, [
                'sub_id'  => (int)$sub->getVar('sub_id'),
                'user_id' => $userId,
                'reason'  => $reason,
            ]);
            redirect_header(XOOPS_URL . '/modules/subscriptions/dashboard.php', 2, _MD_SUBSCRIPTIONS_SUB_CANCELLED);
        } else {
            // Scheduled: user stays active until period end — no group removal yet
            redirect_header(XOOPS_URL . '/modules/subscriptions/dashboard.php', 2, _MD_SUBSCRIPTIONS_CANCEL_SCHEDULED);
        }
    }
}

if ($op === 'reactivate' && $sub) {
    $reactivateToken = $_POST['token'] ?? '';
    if (Utility::verifyToken($reactivateToken, 'reactivate_sub_' . $sub->getVar('sub_id'))) {
        $sub->reactivate();
        redirect_header(XOOPS_URL . '/modules/subscriptions/dashboard.php', 2, _MD_SUBSCRIPTIONS_SUB_REACTIVATED);
    }
}

// -----------------------------------------------------------------------
// Reload subscription after any writes
// -----------------------------------------------------------------------
$sub = $subscriptionHandler->getActiveForUser($userId);

// -----------------------------------------------------------------------
// Build subscription data for template
// -----------------------------------------------------------------------
$subData = null;
if ($sub) {
    $plan    = $sub->getPlan();
    $subData = [
        'sub_id'               => (int)$sub->getVar('sub_id'),
        'plan_name'            => $plan ? $plan->getVar('name', 'n') : '',
        'status'               => $sub->getStatusLabel(),
        'status_raw'           => $sub->getVar('status', 'n'),
        'billing_cycle'        => $plan ? $plan->getBillingCycleLabel() : '',
        'period_end'           => Utility::formatDate((int)$sub->getVar('current_period_end')),
        'period_end_ts'        => (int)$sub->getVar('current_period_end'),
        'auto_renew'           => (bool)(int)$sub->getVar('auto_renew'),
        'is_trial'             => $sub->getVar('status', 'n') === Subscription::STATUS_TRIAL,
        'trial_ends_at'        => Utility::formatDate((int)$sub->getVar('trial_ends_at')),
        'amount_paid'          => Utility::formatMoney((float)$sub->getVar('amount_paid', 'n')),
        'cancel_at_period_end' => $sub->isCancelAtPeriodEnd(),
        'cancel_token'         => Utility::generateToken('cancel_sub_' . $sub->getVar('sub_id')),
        'reactivate_token'     => Utility::generateToken('reactivate_sub_' . $sub->getVar('sub_id')),
    ];

    // -----------------------------------------------------------------------
    // Usage summary for current billing period (Features #3)
    // -----------------------------------------------------------------------
    $periodStart = (int)$sub->getVar('current_period_start');
    $periodEnd   = (int)$sub->getVar('current_period_end');
    $subId       = (int)$sub->getVar('sub_id');

    $usageRows   = $usageLogHandler->getSummaryForPeriod($subId, $periodStart, $periodEnd ?: time());
    $subData['usage_summary']        = $usageRows;
    $subData['usage_unbilled_total'] = $usageLogHandler->getUnbilledTotal($subId);
}

// -----------------------------------------------------------------------
// All subscriptions history
// -----------------------------------------------------------------------
$allSubs    = $subscriptionHandler->getAllForUser($userId);
$subHistory = [];
foreach ($allSubs as $s) {
    $p            = $s->getPlan();
    $subHistory[] = [
        'plan_name'  => $p ? $p->getVar('name', 'n') : '',
        'status'     => $s->getStatusLabel(),
        'started_at' => Utility::formatDate((int)$s->getVar('started_at')),
        'period_end' => Utility::formatDate((int)$s->getVar('current_period_end')),
    ];
}

// -----------------------------------------------------------------------
// Recent invoices
// -----------------------------------------------------------------------
$invoices    = $invoiceHandler->getForUser($userId);
$invoiceData = [];
foreach (array_slice($invoices, 0, 10) as $inv) {
    $invoiceData[] = [
        'invoice_number' => $inv->getVar('invoice_number', 'n'),
        'total'          => Utility::formatMoney((float)$inv->getVar('total', 'n'), $inv->getVar('currency', 'n')),
        'status'         => $inv->getVar('status', 'n'),
        'created_at'     => Utility::formatDate((int)$inv->getVar('created_at')),
        'view_url'       => XOOPS_URL . '/modules/subscriptions/invoice.php?id=' . (int)$inv->getVar('invoice_id'),
    ];
}

// -----------------------------------------------------------------------
// Assign to template
// -----------------------------------------------------------------------
$xoopsTpl->assign('xm_subscription',  $subData);
$xoopsTpl->assign('xm_sub_history',   $subHistory);
$xoopsTpl->assign('xm_invoices',      $invoiceData);
$xoopsTpl->assign('xm_plans_url',     XOOPS_URL . '/modules/subscriptions/plans.php');
$xoopsTpl->assign('xm_invoices_url',  XOOPS_URL . '/modules/subscriptions/invoices.php');

require XOOPS_ROOT_PATH . '/footer.php';
