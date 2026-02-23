<?php

declare(strict_types=1);

/**
 * Admin bootstrap — included at the top of every admin page.
 *
 * Loads the autoloader, XOOPS cp_header, and language files.
 * Call Utility::addAdminAssets() immediately after xoops_cp_header()
 * in each admin page to register the module CSS and TableSorter.
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Module\Admin;
use XoopsModules\Subscriptions\Helper;

require \dirname(__DIR__) . '/preloads/autoloader.php';
require \dirname(__DIR__, 3) . '/include/cp_header.php';
require \dirname(__DIR__) . '/include/common.php';

$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

$adminObject = Admin::getInstance();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}
