<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Refund class
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class Refund
 */
class Refund extends \XoopsObject
{
    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';

    public function __construct()
    {
        $this->initVar('refund_id',         XOBJ_DTYPE_INT,    null, false);
        $this->initVar('payment_id',        XOBJ_DTYPE_INT,    0,    true);
        $this->initVar('sub_id',            XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('user_id',           XOBJ_DTYPE_INT,    0,    true);
        $this->initVar('amount',            XOBJ_DTYPE_OTHER,  '0.00', true);
        $this->initVar('reason',            XOBJ_DTYPE_TXTBOX, '',   false, 255);
        $this->initVar('status',            XOBJ_DTYPE_TXTBOX, self::STATUS_PENDING, false);
        $this->initVar('gateway_refund_id', XOBJ_DTYPE_TXTBOX, '',   false);
        $this->initVar('processed_by',      XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('created_at',        XOBJ_DTYPE_INT,    0,    false);
    }
}
