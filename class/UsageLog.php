<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Access Control
 *
 * Enforces subscription-based access to connected modules.
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');



/**
 * class UsageLog
 */
class UsageLog extends \XoopsObject
{
    public function __construct()
    {
        $this->initVar('log_id',     XOBJ_DTYPE_INT,   null,  false);
        $this->initVar('sub_id',     XOBJ_DTYPE_INT,   0,     true);
        $this->initVar('user_id',    XOBJ_DTYPE_INT,   0,     true);
        $this->initVar('module_id',  XOBJ_DTYPE_INT,   0,     false);
        $this->initVar('event_type', XOBJ_DTYPE_TXTBOX,'visit',false);
        $this->initVar('quantity',   XOBJ_DTYPE_OTHER, '1.00',false);
        $this->initVar('unit_price', XOBJ_DTYPE_OTHER, '0.000000', false);
        $this->initVar('billed',     XOBJ_DTYPE_INT,   0,     false);
        $this->initVar('created_at', XOBJ_DTYPE_INT,   0,     false);
    }
}
