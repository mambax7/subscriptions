<?php

declare(strict_types=1);

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Compatibility shim: xoops_getModuleHelper() is not part of the XOOPS core.
 *
 * Must stay a named global function — cannot be a static method — because
 * XOOPS and third-party code may call it by name at any point.
 *
 * For all module-specific helpers use XoopsModules\Subscriptions\Utility directly.
 */
if (!function_exists('xoops_getModuleHelper')) {
    function xoops_getModuleHelper(string $dirname)
    {
        if ($dirname === 'subscriptions') {
            return \XoopsModules\Subscriptions\Helper::getInstance();
        }
        if (class_exists('\Xmf\Module\Helper', true)) {
            return \Xmf\Module\Helper::getHelper($dirname);
        }
        return null;
    }
}
