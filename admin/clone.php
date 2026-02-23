<?php

declare(strict_types=1);

/**
 * Clone — copies this module to a new directory with a new name.
 *
 * All occurrences of the source dirname (in all three case variants) are
 * replaced with the target dirname throughout every text file in the tree.
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Request;
use XoopsModules\Subscriptions\Cloner;
use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Utility;

require __DIR__ . '/admin_header.php';
xoops_cp_header();
Utility::addAdminAssets();

$adminObject->displayNavigation(basename(__FILE__));

$helper = Helper::getInstance();
$dirname = $helper->getDirname();

// ------------- Clone form / action -------------------------------------------

if ('submit' === Request::getString('op', '', 'POST')) {
    if (! $GLOBALS['xoopsSecurity']->check()) {
        redirect_header('clone.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $clone = Request::getString('clone', '', 'POST');

    if (empty($clone) || preg_match('/[^a-zA-Z0-9_\-]/', $clone)) {
        redirect_header('clone.php', 3, sprintf(_AM_SUBSCRIPTIONS_CLONE_INVALIDNAME, $clone));
    }

    if (is_dir($GLOBALS['xoops']->path('modules/' . $clone))) {
        redirect_header('clone.php', 3, sprintf(_AM_SUBSCRIPTIONS_CLONE_EXISTS, $clone));
    }

    // Build replacement map: lower, UPPER, and Ucfirst variants
    $patKeys = [mb_strtolower($dirname),   mb_strtoupper($dirname),   ucfirst(mb_strtolower($dirname))];
    $patValues = [mb_strtolower($clone),     mb_strtoupper($clone),     ucfirst(mb_strtolower($clone))];

    Cloner::cloneFileFolder($helper->path('/'));
    $logocreated = Cloner::createLogo(mb_strtolower($clone));

    if (is_dir($GLOBALS['xoops']->path('modules/' . mb_strtolower($clone)))) {
        $moduleAdminUrl = XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin';
        $link = '<a href="' . $moduleAdminUrl . '">' . ucfirst(mb_strtolower($clone)) . '</a>';
        $msg = sprintf(_AM_SUBSCRIPTIONS_CLONE_CONGRAT, $link);
        if (! $logocreated) {
            $msg .= '<br>' . _AM_SUBSCRIPTIONS_CLONE_IMAGEFAIL;
        }
    } else {
        $msg = _AM_SUBSCRIPTIONS_CLONE_FAIL;
    }
    echo $msg;
} else {
    require_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

    $form = new XoopsThemeForm(
        sprintf(_AM_SUBSCRIPTIONS_CLONE_TITLE, $helper->getModule()->getVar('name', 'E')),
        'clone',
        'clone.php',
        'post',
        true
    );
    $field = new XoopsFormText(_AM_SUBSCRIPTIONS_CLONE_NAME, 'clone', 20, 20, '');
    $field->setDescription(_AM_SUBSCRIPTIONS_CLONE_NAME_DSC);
    $form->addElement($field, true);
    $form->addElement(new XoopsFormHidden('op', 'submit'));
    $form->addElement(new XoopsFormButton('', '', _SUBMIT, 'submit'));
    $form->display();
}

require __DIR__ . '/admin_footer.php';
