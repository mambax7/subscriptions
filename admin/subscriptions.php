<?php

declare(strict_types=1);
/**
 * Subscriptions Admin Subscriptions management.
 */

use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Utility;
use XoopsModules\Subscriptions\Webhook;

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_admin_subscriptions.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');

/** @var SubscriptionHandler $handler */
$handler = $helper->getHandler('Subscription');

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';
$start = max(0, (int) ($_GET['start'] ?? 0));
$limit = 20;

switch ($op) {
    // ---- Cancel a subscription -------------------------------------------
    case 'cancel':
        $subId = (int) ($_REQUEST['sub_id'] ?? 0);
        if ($subId > 0 && Utility::verifyToken($_REQUEST['token'] ?? '', 'admin_sub_cancel_' . $subId)) {
            $sub = $handler->get($subId);
            if ($sub) {
                $reason = trim($_REQUEST['reason'] ?? 'Admin cancellation');
                $sub->cancel($reason);
                // Fire webhook
                $wh = $helper->getHandler('Webhook');
                $wh->fire(Webhook::EVENT_SUBSCRIPTION_CANCELLED, [
                    'sub_id'  => $subId,
                    'user_id' => (int) $sub->getVar('user_id'),
                    'reason'  => $reason,
                ]);
            }
        }
        redirect_header('subscriptions.php', 2, _AM_SUBSCRIPTIONS_SUB_CANCELLED);

        break;
        // ---- List (default) -------------------------------------------------
    default:
        $statusFilter = $_GET['status'] ?? '';
        $userFilter = max(0, (int) ($_GET['user_id'] ?? 0));
        $criteria = new CriteriaCompo();
        if ($statusFilter !== '' && in_array($statusFilter, ['active', 'trial', 'past_due', 'cancelled', 'expired', 'paused'], true)) {
            $criteria->add(new Criteria('status', $statusFilter));
        }
        if ($userFilter > 0) {
            $criteria->add(new Criteria('user_id', $userFilter));
        }
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        $subs = $handler->getAll($criteria);
        $countCriteria = new CriteriaCompo();
        if ($statusFilter !== '' && in_array($statusFilter, ['active', 'trial', 'past_due', 'cancelled', 'expired', 'paused'], true)) {
            $countCriteria->add(new Criteria('status', $statusFilter));
        }
        if ($userFilter > 0) {
            $countCriteria->add(new Criteria('user_id', $userFilter));
        }
        $total = $handler->getCount($countCriteria);

        $subData = [];
        $userHandler = xoops_getHandler('user');
        $planHandler = $helper->getHandler('Plan');
        foreach ($subs as $sub) {
            $user = $userHandler->get((int) $sub->getVar('user_id'));
            $plan = $planHandler->get((int) $sub->getVar('plan_id'));
            $subData[] = [
                'sub_id'       => $sub->getVar('sub_id'),
                'username'     => $user ? $user->getVar('uname') : '(deleted)',
                'plan_name'    => $plan ? $plan->getVar('name', 'n') : '(deleted)',
                'status'       => $sub->getVar('status', 'n'),
                'period_end'   => Utility::formatDate((int) $sub->getVar('current_period_end')),
                'amount_paid'  => Utility::formatMoney((float) $sub->getVar('amount_paid', 'n')),
                'created_at'   => Utility::formatDate((int) $sub->getVar('created_at')),
                'cancel_token' => Utility::generateToken('admin_sub_cancel_' . $sub->getVar('sub_id')),
            ];
        }

        xoops_load('XoopsPageNav');
        $navExtra = 'op=list&status=' . rawurlencode($statusFilter) . ($userFilter > 0 ? '&user_id=' . $userFilter : '');
        $pagenav = new XoopsPageNav($total, $limit, $start, 'start', $navExtra);

        $xoopsTpl->assign('xm_subs', $subData);
        $xoopsTpl->assign('xm_pagenav', $pagenav->renderNav());
        $xoopsTpl->assign('xm_total', $total);
        $xoopsTpl->assign('xm_status_filter', $statusFilter);
        $xoopsTpl->assign('xm_user_filter', $userFilter);
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
}

require __DIR__ . '/admin_footer.php';
