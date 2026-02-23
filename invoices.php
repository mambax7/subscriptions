<?php
/**
 * Subscriptions - User invoices page
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    Helper,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_invoices.tpl';

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

Utility::requireLogin();
global $xoopsUser;
$userId = (int)$xoopsUser->getVar('uid');

$helper = Helper::getInstance();
$invHandler = $helper->getHandler('Invoice');
$invoices   = $invHandler->getForUser($userId);
$invData    = [];

foreach ($invoices as $inv) {
    $invData[] = [
        'invoice_id'     => (int)$inv->getVar('invoice_id'),
        'invoice_number' => $inv->getVar('invoice_number', 'n'),
        'total'          => Utility::formatMoney((float)$inv->getVar('total', 'n'), $inv->getVar('currency', 'n')),
        'status'         => $inv->getVar('status', 'n'),
        'due_date'       => Utility::formatDate((int)$inv->getVar('due_date')),
        'created_at'     => Utility::formatDate((int)$inv->getVar('created_at')),
        'view_url'       => XOOPS_URL . '/modules/subscriptions/invoice.php?id=' . (int)$inv->getVar('invoice_id'),
    ];
}

$xoopsTpl->assign('xm_invoices',    $invData);
$xoopsTpl->assign('xm_dashboard_url', XOOPS_URL . '/modules/subscriptions/dashboard.php');

require XOOPS_ROOT_PATH . '/footer.php';
