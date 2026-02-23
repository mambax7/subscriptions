<?php

declare(strict_types=1);
/**
 * Subscriptions Admin Gateway configuration.
 */

use XoopsModules\Subscriptions\GatewayFactory;
use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Utility;

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_admin_gateways.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');

/** @var GatewayConfigHandler $configHandler */
$configHandler = $helper->getHandler('GatewayConfig');

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';
$gateway = isset($_REQUEST['gateway']) ? htmlspecialchars(trim($_REQUEST['gateway']), ENT_QUOTES) : '';

switch ($op) {
    case 'save':
        if (! Utility::verifyToken($_POST['token'] ?? '', 'admin_gateways')) {
            redirect_header('gateways.php', 2, _NOPERM);
        }
        $gwId = htmlspecialchars(trim($_POST['gateway'] ?? ''), ENT_QUOTES);
        if (in_array($gwId, GatewayFactory::getAvailable(), true)) {
            // Save each config key for this gateway
            foreach ($_POST as $k => $v) {
                if (strpos($k, 'gw_') === 0) {
                    $key = substr($k, 3); // strip 'gw_' prefix
                    $configHandler->saveConfig($gwId, $key, trim((string) $v));
                }
            }
        }
        redirect_header('gateways.php?gateway=' . rawurlencode($gwId), 2, _AM_SUBSCRIPTIONS_GATEWAY_SAVED);

        break;
    default:
        // Show configuration form for the selected gateway
        $availableGateways = GatewayFactory::getAvailable();
        if (! $gateway || ! in_array($gateway, $availableGateways, true)) {
            $gateway = 'paypal';
        }
        // Load existing config
        $rows = $configHandler->getAll(new Criteria('gateway', $gateway));
        $config = [];
        foreach ($rows as $row) {
            $config[$row->getVar('config_key', 'n')] = $row->getVar('config_val', 'n');
        }
        $xoopsTpl->assign('xm_gateway', $gateway);
        $xoopsTpl->assign('xm_config', $config);
        $xoopsTpl->assign('xm_gateways', $availableGateways);
        $xoopsTpl->assign('xm_token', Utility::generateToken('admin_gateways'));
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
}

require __DIR__ . '/admin_footer.php';
