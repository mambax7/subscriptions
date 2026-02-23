<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

/**
 * Subscriptions Payment Gateway abstraction.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class GatewayConfig - ORM for gateway_configs table.
 */
class GatewayConfig extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('config_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gateway', XOBJ_DTYPE_TXTBOX, '', true);
        $this->initVar('config_key', XOBJ_DTYPE_TXTBOX, '', true);
        $this->initVar('config_val', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('is_active', XOBJ_DTYPE_INT, 1, false);
    }
}
