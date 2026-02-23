<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Coupon class
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');



/**
 * class CouponUsageHandler
 */
class CouponUsageHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_coupon_usage', CouponUsage::class, 'usage_id', 'usage_id');
    }

    /**
     * Count usages for a specific user and coupon
     *
     * @param int $userId
     * @param int $couponId
     * @return int
     */
    public function countForUserAndCoupon(int $userId, int $couponId): int
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('user_id',  $userId));
        $criteria->add(new \Criteria('coupon_id',$couponId));
        return $this->getCount($criteria);
    }

    /**
     * Record coupon usage
     *
     * @param int $couponId
     * @param int $userId
     * @param int $subId
     * @return bool
     */
    public function recordUsage(int $couponId, int $userId, int $subId): bool
    {
        /** @var CouponUsage $usage */
        $usage = $this->create();
        $usage->setVar('coupon_id', $couponId);
        $usage->setVar('user_id',   $userId);
        $usage->setVar('sub_id',    $subId);
        $usage->setVar('used_at',   time());
        if (!$this->insert($usage)) {
            return false;
        }
        $helper = Helper::getInstance();
        // Increment uses_count on the coupon record
        $couponHandler = $helper->getHandler('Coupon');
        $coupon        = $couponHandler->get($couponId);
        if ($coupon) {
            $coupon->setVar('uses_count', (int)$coupon->getVar('uses_count') + 1);
            $couponHandler->insert($coupon);
        }
        return true;
    }
}


