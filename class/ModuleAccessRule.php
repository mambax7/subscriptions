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
 * class ModuleAccessRule
 */
class ModuleAccessRule extends \XoopsObject
{
    public function __construct()
    {
        $this->initVar('rule_id',     XOBJ_DTYPE_INT,    null, false);
        $this->initVar('module_id',   XOBJ_DTYPE_INT,    0,    true);
        $this->initVar('plan_id',     XOBJ_DTYPE_INT,    0,    true);
        $this->initVar('access_type', XOBJ_DTYPE_TXTBOX, 'full', false);
        $this->initVar('limit_value', XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('xoops_group', XOBJ_DTYPE_INT,    0,    false);
    }
}
