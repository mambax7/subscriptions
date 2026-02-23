<?php

declare(strict_types=1);
/**
 * Subscriptions Admin Payments management.
 */

use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Utility;

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_admin_payments.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');

/** @var PaymentHandler $paymentHandler */
$paymentHandler = $helper->getHandler('Payment');
/** @var RefundHandler $refundHandler */
$refundHandler = $helper->getHandler('Refund');

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';
$start = max(0, (int) ($_GET['start'] ?? 0));
$limit = 20;

switch ($op) {
    case 'refund':
        if (! Utility::verifyToken($_POST['token'] ?? '', 'admin_refund')) {
            redirect_header('payments.php', 2, _NOPERM);
        }
        $paymentId = (int) ($_POST['payment_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $adminUid = (int) $GLOBALS['xoopsUser']->getVar('uid');
        $result = $refundHandler->processRefund($paymentId, $amount, $reason, $adminUid);
        if ($result['success']) {
            redirect_header('payments.php', 2, _AM_SUBSCRIPTIONS_REFUND_SUCCESS);
        }
        redirect_header('payments.php', 2, $result['error'] ?? _AM_SUBSCRIPTIONS_REFUND_FAILED);

        break;
    default:
        $criteria = new CriteriaCompo();
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        $payments = $paymentHandler->getAll($criteria);
        $total = $paymentHandler->getCount();
        $userHandler = xoops_getHandler('user');
        $payData = [];
        foreach ($payments as $p) {
            $user = $userHandler->get((int) $p->getVar('user_id'));
            $payData[] = [
                'payment_id'   => $p->getVar('payment_id'),
                'user_id'      => (int) $p->getVar('user_id'),
                'username'     => $user ? $user->getVar('uname') : '(deleted)',
                'gateway'      => $p->getVar('gateway', 'n'),
                'amount'       => Utility::formatMoney((float) $p->getVar('amount', 'n'), $p->getVar('currency', 'n')),
                'status'       => $p->getVar('status', 'n'),
                'paid_at'      => Utility::formatDate((int) $p->getVar('paid_at')),
                'refund_token' => Utility::generateToken('admin_refund'),
            ];
        }
        xoops_load('XoopsPageNav');
        $pagenav = new XoopsPageNav($total, $limit, $start, 'start', 'op=list');
        $xoopsTpl->assign('xm_payments', $payData);
        $xoopsTpl->assign('xm_pagenav', $pagenav->renderNav());
        $xoopsTpl->assign('xm_total', $total);
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
}

require __DIR__ . '/admin_footer.php';
