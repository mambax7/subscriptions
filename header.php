<?php declare(strict_types=1);

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author     XOOPS Development Team
 */

use XoopsModules\Subscriptions\{
    Helper
};
/** @var Helper $helper */

require dirname(__DIR__, 2) . '/mainfile.php';
require XOOPS_ROOT_PATH . '/header.php';

require __DIR__ . '/preloads/autoloader.php';

$moduleDirName = basename(__DIR__);

$helper = Helper::getInstance();
// Load language files
$helper->loadLanguage('main');

$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
    require $GLOBALS['xoops']->path('class/theme.php');
    $GLOBALS['xoTheme'] = new \xos_opal_Theme();
}

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    require $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new XoopsTpl();
}

// Enqueue module stylesheet and script
$GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/subscriptions/assets/css/admin.css');
$GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/subscriptions/assets/js/subscriptions.js');

// Admin toolbar: visible only to module admins on all frontend pages
$xoopsUser = $GLOBALS['xoopsUser'] ?? null;
$GLOBALS['xoopsTpl']->assign('xm_is_admin', is_object($xoopsUser) && $helper->isUserAdmin());
$GLOBALS['xoopsTpl']->assign('xm_admin_url', XOOPS_URL . '/modules/subscriptions/admin/index.php');

