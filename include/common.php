<?php

declare(strict_types=1);

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Module bootstrap — loaded by every front-end and admin page.
 *
 * Registers the PSR-4 autoloader so all classes under
 * XoopsModules\Subscriptions\ are available without manual require calls.
 * The xoops_getModuleHelper() compatibility shim is also pulled in here
 * because it must remain a named global function.
 */
require_once dirname(__DIR__) . '/preloads/autoloader.php';
require_once __DIR__ . '/functions.php';
