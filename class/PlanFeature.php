<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Plan class
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * class PlanFeature
 */
class PlanFeature extends \XoopsObject
{
    public function __construct()
    {
        $this->initVar('feature_id',  XOBJ_DTYPE_INT,    null, false);
        $this->initVar('plan_id',     XOBJ_DTYPE_INT,    0,    true);
        $this->initVar('feature',     XOBJ_DTYPE_TXTBOX, '',   true, 255);
        $this->initVar('sort_order',  XOBJ_DTYPE_INT,    0,    false);
    }
}
