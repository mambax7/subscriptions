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
 * class Plan
 *
 * Represents a single membership plan (pricing tier).
 */
class Plan extends \XoopsObject
{
    /**
     * Billing cycle constants
     */
    const CYCLE_ONE_TIME  = 'one_time';
    const CYCLE_DAILY     = 'daily';
    const CYCLE_WEEKLY    = 'weekly';
    const CYCLE_MONTHLY   = 'monthly';
    const CYCLE_QUARTERLY = 'quarterly';
    const CYCLE_ANNUAL    = 'annual';

    /**
     * Pricing model constants
     */
    const MODEL_FLAT   = 'flat';
    const MODEL_USAGE  = 'usage';
    const MODEL_HYBRID = 'hybrid';

    /**
     * Plan constructor.
     */
    public function __construct()
    {
        $this->initVar('plan_id',         XOBJ_DTYPE_INT,    null, false);
        $this->initVar('name',            XOBJ_DTYPE_TXTBOX, '',   true,  150);
        $this->initVar('slug',            XOBJ_DTYPE_TXTBOX, '',   false, 150);
        $this->initVar('description',     XOBJ_DTYPE_TXTAREA,'',   false);
        $this->initVar('price',           XOBJ_DTYPE_OTHER,  '0.00', false);
        $this->initVar('currency',        XOBJ_DTYPE_TXTBOX, 'USD',false, 10);
        $this->initVar('billing_cycle',   XOBJ_DTYPE_TXTBOX, self::CYCLE_MONTHLY, false);
        $this->initVar('trial_days',      XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('setup_fee',       XOBJ_DTYPE_OTHER,  '0.00', false);
        $this->initVar('discount_annual', XOBJ_DTYPE_OTHER,  '0.00', false);
        $this->initVar('pricing_model',   XOBJ_DTYPE_TXTBOX, self::MODEL_FLAT, false);
        $this->initVar('usage_unit',      XOBJ_DTYPE_TXTBOX, '',   false);
        $this->initVar('usage_price',     XOBJ_DTYPE_OTHER,  '0.000000', false);
        $this->initVar('is_featured',     XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('is_active',       XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('sort_order',      XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('max_users',       XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('created_at',      XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('updated_at',      XOBJ_DTYPE_INT,    0,    false);
    }

    /**
     * Returns list of plan features
     *
     * @return array
     */
    public function getFeatures(): array
    {
        $helper = Helper::getInstance();
        $handler  = $helper->getHandler('PlanFeature');
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('plan_id', $this->getVar('plan_id')));
        $criteria->setSort('sort_order');
        $criteria->setOrder('ASC');
        return $handler->getAll($criteria);
    }

    /**
     * Compute effective annual price (with discount applied)
     *
     * @return float
     */
    public function getAnnualPrice(): float
    {
        $monthly  = (float)$this->getVar('price');
        $discount = (float)$this->getVar('discount_annual');
        $annual   = $monthly * 12;
        if ($discount > 0) {
            $annual = $annual * (1 - $discount / 100);
        }
        return round($annual, 2);
    }

    /**
     * Returns human-readable billing cycle label
     *
     * @return string
     */
    public function getBillingCycleLabel(): string
    {
        $labels = [
            self::CYCLE_ONE_TIME  => _MD_SUBSCRIPTIONS_CYCLE_ONE_TIME,
            self::CYCLE_DAILY     => _MD_SUBSCRIPTIONS_CYCLE_DAILY,
            self::CYCLE_WEEKLY    => _MD_SUBSCRIPTIONS_CYCLE_WEEKLY,
            self::CYCLE_MONTHLY   => _MD_SUBSCRIPTIONS_CYCLE_MONTHLY,
            self::CYCLE_QUARTERLY => _MD_SUBSCRIPTIONS_CYCLE_QUARTERLY,
            self::CYCLE_ANNUAL    => _MD_SUBSCRIPTIONS_CYCLE_ANNUAL,
        ];
        return $labels[$this->getVar('billing_cycle')] ?? $this->getVar('billing_cycle');
    }

    /**
     * Compute the next renewal timestamp from a given start time
     *
     * @param int $fromTime Unix timestamp
     * @return int
     */
    public function getNextRenewalDate(int $fromTime = 0): int
    {
        if ($fromTime === 0) {
            $fromTime = time();
        }
        switch ($this->getVar('billing_cycle')) {
            case self::CYCLE_DAILY:
                return strtotime('+1 day', $fromTime);
            case self::CYCLE_WEEKLY:
                return strtotime('+1 week', $fromTime);
            case self::CYCLE_MONTHLY:
                return strtotime('+1 month', $fromTime);
            case self::CYCLE_QUARTERLY:
                return strtotime('+3 months', $fromTime);
            case self::CYCLE_ANNUAL:
                return strtotime('+1 year', $fromTime);
            default:
                return 0; // one-time
        }
    }
}
