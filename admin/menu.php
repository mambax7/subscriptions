<?php

declare(strict_types=1);

/**
 * Subscriptions Admin menu.
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Module\Admin;
use XoopsModules\Subscriptions\Helper;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require dirname(__DIR__) . '/preloads/autoloader.php';

// menu.php is loaded by the XOOPS admin framework which only auto-loads modinfo.php.
// We must explicitly load the admin language file for our _AM_* constants.
$helper = Helper::getInstance();
$helper->loadLanguage('admin');

$pathIcon32 = Admin::menuIconPath('');
$pathModIcon32 = $helper->url('assets/images/icons/32/');

$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_DASHBOARD,     'link' => 'admin/index.php',         'icon' => $pathIcon32 . '/home.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_PLANS,         'link' => 'admin/plans.php',         'icon' => $pathIcon32 . '/event.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_SUBSCRIPTIONS, 'link' => 'admin/subscriptions.php', 'icon' => $pathIcon32 . '/home.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_PAYMENTS,      'link' => 'admin/payments.php',      'icon' => $pathIcon32 . '/cash_stack.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_COUPONS,       'link' => 'admin/coupons.php',       'icon' => $pathIcon32 . '/discount.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_MODULES,       'link' => 'admin/modules.php',       'icon' => $pathIcon32 . '/groupmod.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_GATEWAYS,      'link' => 'admin/gateways.php',      'icon' => $pathIcon32 . '/cart_add.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_REPORTS,       'link' => 'admin/reports.php',       'icon' => $pathIcon32 . '/fileshare.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_WEBHOOKS,      'link' => 'admin/webhooks.php',      'icon' => $pathIcon32 . '/link_break.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_BLOCKSADMIN,   'link' => 'admin/blocksadmin.php',   'icon' => $pathIcon32 . '/block.png'];
$adminmenu[] = ['title' => _MI_SUBSCRIPTIONS_MENU_CLONE,    'link' => 'admin/clone.php',         'icon' => $pathModIcon32 . 'editcopy.png'];
$adminmenu[] = ['title' => _AM_SUBSCRIPTIONS_ABOUT,         'link' => 'admin/about.php',         'icon' => $pathIcon32 . '/about.png'];
