<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

use function in_array;
use function is_array;

/**
 * Subscriptions Coupon class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class Coupon.
 */
class Coupon extends XoopsObject
{
    public const TYPE_PERCENTAGE = 'percentage';

    public const TYPE_FIXED = 'fixed';

    public function __construct()
    {
        $this->initVar('coupon_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('code', XOBJ_DTYPE_TXTBOX, '', true, 50);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, '', true, 150);
        $this->initVar('discount_type', XOBJ_DTYPE_TXTBOX, self::TYPE_PERCENTAGE, false);
        $this->initVar('discount_value', XOBJ_DTYPE_OTHER, '0.00', true);
        $this->initVar('min_amount', XOBJ_DTYPE_OTHER, '0.00', false);
        $this->initVar('max_uses', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('uses_count', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('max_uses_per_user', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('applies_to', XOBJ_DTYPE_TXTBOX, 'all', false);
        $this->initVar('plan_ids', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('recurring_months', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('valid_from', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('valid_until', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('is_active', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('created_at', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Check if this coupon is currently valid (ignoring per-user usage).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (! (int) $this->getVar('is_active')) {
            return false;
        }
        $now = time();
        $from = (int) $this->getVar('valid_from');
        $until = (int) $this->getVar('valid_until');
        $maxUses = (int) $this->getVar('max_uses');
        $usedCount = (int) $this->getVar('uses_count');

        if ($from > 0 && $now < $from) {
            return false;
        }
        if ($until > 0 && $now > $until) {
            return false;
        }

        return ! ($maxUses > 0 && $usedCount >= $maxUses);
    }

    /**
     * Calculate the discount amount for a given price.
     *
     * @param float $price
     *
     * @return float
     */
    public function calculateDiscount(float $price): float
    {
        $value = (float) $this->getVar('discount_value');
        if ($this->getVar('discount_type') === self::TYPE_PERCENTAGE) {
            return round($price * $value / 100, 2);
        }

        return min($value, $price);
    }

    /**
     * Check if this coupon applies to a given plan.
     *
     * @param int $planId
     *
     * @return bool
     */
    public function appliesToPlan(int $planId): bool
    {
        if ($this->getVar('applies_to') === 'all') {
            return true;
        }
        $planIds = json_decode($this->getVar('plan_ids', 'n'), true);

        return is_array($planIds) && in_array($planId, $planIds, true);
    }

    /**
     * Get applicable plan IDs.
     *
     * @return array
     */
    public function getPlanIds(): array
    {
        $raw = $this->getVar('plan_ids', 'n');
        if (empty($raw)) {
            return [];
        }

        return json_decode($raw, true) ?? [];
    }

    /**
     * How many months this coupon discount recurs on renewals.
     * 0 = first payment only (one-time discount at checkout).
     * N = applies to first N billing periods.
     * -1 = applies forever (every renewal).
     *
     * @return int
     */
    public function getRecurringMonths(): int
    {
        return (int) $this->getVar('recurring_months');
    }

    /**
     * Check whether the discount should apply to the given renewal invoice.
     * The first checkout is renewal_count = 1.
     *
     * @param int $renewalCount 1-based count of billing periods (1 = initial checkout)
     *
     * @return bool
     */
    public function appliesToRenewal(int $renewalCount): bool
    {
        $months = $this->getRecurringMonths();
        if ($months === 0) {
            // One-time: only the first payment
            return $renewalCount === 1;
        }
        if ($months === -1) {
            // Forever
            return true;
        }

        return $renewalCount <= $months;
    }
}
