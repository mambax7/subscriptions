<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

/**
 * Subscriptions Coupon class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class CouponUsage.
 */
class CouponUsage extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('usage_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('coupon_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('user_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('sub_id', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('used_at', XOBJ_DTYPE_INT, 0, false);
    }
}
