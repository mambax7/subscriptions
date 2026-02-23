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
 * class CouponHandler
 */
class CouponHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_coupons', Coupon::class, 'coupon_id', 'code');
    }

    /**
     * Look up a coupon by its code
     *
     * @param string $code
     * @return Coupon|null
     */
    public function getByCode(string $code): ?Coupon
    {
        $criteria = new \Criteria('code', strtoupper(trim($code)), '=');
        $results  = $this->getAll($criteria);
        return !empty($results) ? reset($results) : null;
    }

    /**
     * Validate a coupon code for a given user and plan
     *
     * @param string $code
     * @param int    $userId
     * @param int    $planId
     * @param float  $price
     * @return array ['valid' => bool, 'coupon' => Coupon|null, 'error' => string, 'discount' => float]
     */
    public function validate(string $code, int $userId, int $planId, float $price): array
    {
        $coupon = $this->getByCode($code);
        if (!$coupon) {
            return ['valid' => false, 'coupon' => null, 'error' => _MD_SUBSCRIPTIONS_COUPON_NOT_FOUND, 'discount' => 0.0];
        }
        if (!$coupon->isValid()) {
            return ['valid' => false, 'coupon' => $coupon, 'error' => _MD_SUBSCRIPTIONS_COUPON_INVALID, 'discount' => 0.0];
        }
        if (!$coupon->appliesToPlan($planId)) {
            return ['valid' => false, 'coupon' => $coupon, 'error' => _MD_SUBSCRIPTIONS_COUPON_PLAN_MISMATCH, 'discount' => 0.0];
        }
        $minAmount = (float)$coupon->getVar('min_amount');
        if ($minAmount > 0 && $price < $minAmount) {
            return ['valid' => false, 'coupon' => $coupon, 'error' => _MD_SUBSCRIPTIONS_COUPON_MIN_AMOUNT, 'discount' => 0.0];
        }
        // Check per-user usage
        $maxPerUser = (int)$coupon->getVar('max_uses_per_user');
        $helper = Helper::getInstance();
        if ($maxPerUser > 0) {
            $usageHandler = $helper->getHandler('CouponUsage');
            $usageCount   = $usageHandler->countForUserAndCoupon($userId, $coupon->getVar('coupon_id'));
            if ($usageCount >= $maxPerUser) {
                return ['valid' => false, 'coupon' => $coupon, 'error' => _MD_SUBSCRIPTIONS_COUPON_USER_LIMIT, 'discount' => 0.0];
            }
        }
        $discount = $coupon->calculateDiscount($price);
        return ['valid' => true, 'coupon' => $coupon, 'error' => '', 'discount' => $discount];
    }
}
