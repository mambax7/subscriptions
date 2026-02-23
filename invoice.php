<?php
/**
 * Subscriptions - Invoice detail page
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    Helper,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_invoice.tpl';

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

Utility::requireLogin();
global $xoopsUser;
$userId = (int)$xoopsUser->getVar('uid');

$invId      = (int)($_GET['id'] ?? 0);
$helper = Helper::getInstance();
$invHandler = $helper->getHandler('Invoice');
$invoice    = $invId > 0 ? $invHandler->get($invId) : null;

if (!$invoice || (int)$invoice->getVar('user_id', 'n') !== $userId) {
    redirect_header(XOOPS_URL . '/modules/subscriptions/invoices.php', 2, _NOPERM);
}

$items   = $invoice->getItems();
$itemData= [];
foreach ($items as $item) {
    $itemData[] = [
        'description' => $item->getVar('description', 'n'),
        'quantity'    => (float)$item->getVar('quantity', 'n'),
        'unit_price'  => Utility::formatMoney((float)$item->getVar('unit_price', 'n'), $invoice->getVar('currency', 'n')),
        'amount'      => Utility::formatMoney((float)$item->getVar('amount', 'n'), $invoice->getVar('currency', 'n')),
    ];
}

$siteConfig = $GLOBALS['xoopsConfig']['sitename'] ?? '';

$discountRaw = (float)$invoice->getVar('discount_amount', 'n');
$xoopsTpl->assign('xm_invoice', [
    'invoice_number'      => $invoice->getVar('invoice_number', 'n'),
    'status'              => $invoice->getVar('status', 'n'),
    'subtotal'            => Utility::formatMoney((float)$invoice->getVar('subtotal', 'n'), $invoice->getVar('currency', 'n')),
    'tax_rate'            => (float)$invoice->getVar('tax_rate', 'n'),
    'tax_amount'          => Utility::formatMoney((float)$invoice->getVar('tax_amount', 'n'), $invoice->getVar('currency', 'n')),
    'discount_amount'     => Utility::formatMoney($discountRaw, $invoice->getVar('currency', 'n')),
    'discount_amount_raw' => $discountRaw,
    'total'               => Utility::formatMoney((float)$invoice->getVar('total', 'n'), $invoice->getVar('currency', 'n')),
    'due_date'            => Utility::formatDate((int)$invoice->getVar('due_date')),
    'paid_at'             => Utility::formatDate((int)$invoice->getVar('paid_at')),
    'period_start'        => Utility::formatDate((int)$invoice->getVar('period_start')),
    'period_end'          => Utility::formatDate((int)$invoice->getVar('period_end')),
    'created_at'          => Utility::formatDate((int)$invoice->getVar('created_at')),
    'notes'               => $invoice->getVar('notes', 'n'),
]);
$xoopsTpl->assign('xm_items',       $itemData);
$xoopsTpl->assign('xm_site_name',   $siteConfig);
$xoopsTpl->assign('xm_username',    $xoopsUser->getVar('uname', 'n'));

require XOOPS_ROOT_PATH . '/footer.php';
