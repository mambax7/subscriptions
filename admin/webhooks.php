<?php
/**
 * Subscriptions Admin Webhooks management
 *
 * @package    subscriptions
 * @subpackage admin
 */

use XoopsModules\Subscriptions\{
    Helper,
    Utility,
    Webhook
};

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';

// Determine template BEFORE admin_header / cp_header is included
$GLOBALS['xoopsOption']['template_main'] = ($op === 'edit')
    ? 'subscriptions_admin_webhook_form.tpl'
    : 'subscriptions_admin_webhooks.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');


/** @var WebhookHandler $handler */
$handler = $helper->getHandler('Webhook');

$allEvents = [
    Webhook::EVENT_SUBSCRIPTION_CREATED,
    Webhook::EVENT_SUBSCRIPTION_RENEWED,
    Webhook::EVENT_SUBSCRIPTION_CANCELLED,
    Webhook::EVENT_SUBSCRIPTION_EXPIRED,
    Webhook::EVENT_PAYMENT_COMPLETED,
    Webhook::EVENT_PAYMENT_FAILED,
    Webhook::EVENT_REFUND_COMPLETED,
    Webhook::EVENT_TRIAL_STARTED,
    Webhook::EVENT_TRIAL_ENDING,
];

switch ($op) {
    case 'save':
        if (!Utility::verifyToken($_POST['token'] ?? '', 'admin_webhooks')) {
            redirect_header('webhooks.php', 2, _NOPERM);
        }
        $whId   = (int)($_POST['webhook_id'] ?? 0);
        $wh     = $whId > 0 ? $handler->get($whId) : $handler->create();
        if (!$wh) {
            redirect_header('webhooks.php', 2, _AM_SUBSCRIPTIONS_WEBHOOK_NOT_FOUND);
        }
        $selectedEvents = array_values(array_intersect((array)($_POST['events'] ?? []), $allEvents));
        $wh->setVar('url',        substr(trim($_POST['url'] ?? ''), 0, 500));
        $wh->setVar('events',     json_encode($selectedEvents));
        $wh->setVar('is_active',  (int)($_POST['is_active'] ?? 1));
        if ($whId === 0) {
            $wh->setVar('secret',     bin2hex(random_bytes(32)));
            $wh->setVar('last_fired', 0);
            $wh->setVar('fail_count', 0);
            $wh->setVar('created_at', time());
        }
        if ($handler->insert($wh)) {
            redirect_header('webhooks.php', 2, _AM_SUBSCRIPTIONS_WEBHOOK_SAVED);
        }
        redirect_header('webhooks.php', 2, _AM_SUBSCRIPTIONS_SAVE_ERROR);
        break;

    case 'delete':
        $whId = (int)($_REQUEST['webhook_id'] ?? 0);
        if ($whId > 0 && Utility::verifyToken($_REQUEST['token'] ?? '', 'admin_wh_del_' . $whId)) {
            $handler->delete($handler->get($whId), true);
        }
        redirect_header('webhooks.php', 2, _AM_SUBSCRIPTIONS_WEBHOOK_DELETED);
        break;

    case 'edit':
        $whId = (int)($_REQUEST['webhook_id'] ?? 0);
        $wh   = $whId > 0 ? $handler->get($whId) : $handler->create();
        // Decode events JSON in PHP so the template receives a plain array
        $eventsRaw      = $wh->getVar('events', 'n');
        $selectedEvents = (is_string($eventsRaw) && $eventsRaw !== '')
            ? (json_decode($eventsRaw, true) ?? [])
            : [];
        $xoopsTpl->assign('xm_webhook',         Utility::objVars($wh));
        $xoopsTpl->assign('xm_selected_events', $selectedEvents);
        $xoopsTpl->assign('xm_all_events',      $allEvents);
        $xoopsTpl->assign('xm_token',           Utility::generateToken('admin_webhooks'));
        $xoopsTpl->assign('xm_is_edit',         $whId > 0);
        break;

    default:
        $webhooks = $handler->getAll();
        $whData   = [];
        foreach ($webhooks as $w) {
            $whData[] = array_merge(Utility::objVars($w), [
                'del_token' => Utility::generateToken('admin_wh_del_' . $w->getVar('webhook_id')),
            ]);
        }
        $xoopsTpl->assign('xm_webhooks',  $whData);
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());
        break;
}

require __DIR__ . '/admin_footer.php';
