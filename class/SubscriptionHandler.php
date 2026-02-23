<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use CriteriaCompo;
use XoopsDatabase;
use XoopsPersistableObjectHandler;

use function sprintf;

/**
 * Subscriptions Subscription class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class SubscriptionHandler.
 */
class SubscriptionHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_subscriptions', Subscription::class, 'sub_id', 'sub_id');
    }

    /**
     * Get active subscription for a user (optionally filtered by plan).
     *
     * @param int $userId
     * @param int $planId 0 = any plan
     *
     * @return Subscription|null
     */
    public function getActiveForUser(int $userId, int $planId = 0): ?Subscription
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('user_id', $userId));
        $criteria->add(new Criteria('status', sprintf(
            "('%s','%s','%s')",
            Subscription::STATUS_ACTIVE,
            Subscription::STATUS_TRIAL,
            Subscription::STATUS_PAST_DUE
        ), 'IN'));
        if ($planId > 0) {
            $criteria->add(new Criteria('plan_id', $planId));
        }
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');
        $criteria->setLimit(1);
        $subs = $this->getAll($criteria);

        return ! empty($subs) ? reset($subs) : null;
    }

    /**
     * Get all subscriptions for a user.
     *
     * @param int $userId
     *
     * @return Subscription[]
     */
    public function getAllForUser(int $userId): array
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('user_id', $userId));
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');

        return $this->getAll($criteria);
    }

    /**
     * Get subscriptions expiring within the next N days.
     *
     * @param int $days
     *
     * @return Subscription[]
     */
    public function getExpiringWithin(int $days = 7): array
    {
        $now = time();
        $future = $now + ($days * 86400);
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('status', sprintf(
            "('%s','%s')",
            Subscription::STATUS_ACTIVE,
            Subscription::STATUS_PAST_DUE
        ), 'IN'));
        $criteria->add(new Criteria('current_period_end', $now, '>'));
        $criteria->add(new Criteria('current_period_end', $future, '<='));

        return $this->getAll($criteria);
    }

    /**
     * Get subscriptions that have trials ending within the next N hours.
     * Used by the cron job to fire EVENT_TRIAL_ENDING notifications.
     *
     * @param int $hours
     *
     * @return Subscription[]
     */
    public function getTrialsEndingSoon(int $hours = 24): array
    {
        $now = time();
        $future = $now + ($hours * 3600);
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('status', Subscription::STATUS_TRIAL));
        $criteria->add(new Criteria('trial_ends_at', $now, '>'));
        $criteria->add(new Criteria('trial_ends_at', $future, '<='));

        return $this->getAll($criteria);
    }

    /**
     * Get active/trial subscriptions whose current_period_end is in the past
     * and have NOT been flagged for cancel_at_period_end (those are handled separately).
     * Used by the cron job to mark expired subscriptions.
     *
     * @return Subscription[]
     */
    public function getExpiredActive(): array
    {
        $sql = sprintf(
            "SELECT * FROM %s WHERE status IN ('active','trial','past_due') AND cancel_at_period_end = 0 AND current_period_end > 0 AND current_period_end < %d",
            $this->table,
            time()
        );
        $result = $this->db->query($sql);
        $subs = [];
        if ($result) {
            while ($row = $this->db->fetchArray($result)) {
                /** @var Subscription $sub */
                $sub = $this->create(false);
                $sub->assignVars($row);
                $subs[] = $sub;
            }
        }

        return $subs;
    }

    /**
     * Get subscriptions scheduled to cancel at period end where the period has already passed.
     * Called by the cron job to finalise pending cancellations.
     *
     * @return Subscription[]
     */
    public function getPendingCancellations(): array
    {
        $sql = sprintf(
            "SELECT * FROM %s WHERE cancel_at_period_end = 1 AND current_period_end > 0 AND current_period_end < %d AND status IN ('active','trial','past_due')",
            $this->table,
            time()
        );
        $result = $this->db->query($sql);
        $subs = [];
        if ($result) {
            while ($row = $this->db->fetchArray($result)) {
                /** @var Subscription $sub */
                $sub = $this->create(false);
                $sub->assignVars($row);
                $subs[] = $sub;
            }
        }

        return $subs;
    }

    /**
     * Create a new subscription for a user and plan.
     *
     * @param int $userId
     * @param Plan $plan
     * @param string $gateway
     * @param int $couponId
     * @param float $discountAmount
     *
     * @return false|Subscription
     */
    public function createSubscription(
        int $userId,
        Plan $plan,
        string $gateway = '',
        int $couponId = 0,
        float $discountAmount = 0.0
    ) {
        $now = time();
        $trialDays = (int) $plan->getVar('trial_days');
        $cycle = $plan->getVar('billing_cycle');

        /** @var Subscription $sub */
        $sub = $this->create();
        $sub->setVar('user_id', $userId);
        $sub->setVar('plan_id', $plan->getVar('plan_id'));
        $sub->setVar('billing_cycle', $cycle);
        $sub->setVar('gateway', $gateway);
        $sub->setVar('coupon_id', $couponId);
        $sub->setVar('discount_amount', $discountAmount);
        $sub->setVar('started_at', $now);
        $sub->setVar('created_at', $now);
        $sub->setVar('updated_at', $now);
        $sub->setVar('amount_paid', max(0, (float) $plan->getVar('price') - $discountAmount));
        $sub->setVar('auto_renew', 1);

        if ($trialDays > 0) {
            $trialEnd = strtotime("+{$trialDays} days", $now);
            $sub->setVar('status', Subscription::STATUS_TRIAL);
            $sub->setVar('trial_ends_at', $trialEnd);
            $sub->setVar('current_period_start', $now);
            $sub->setVar('current_period_end', $trialEnd);
        } else {
            $periodEnd = $plan->getNextRenewalDate($now);
            $sub->setVar('status', Subscription::STATUS_ACTIVE);
            $sub->setVar('current_period_start', $now);
            $sub->setVar('current_period_end', $periodEnd ?: 0);
        }

        if ($this->insert($sub)) {
            return $sub;
        }

        return false;
    }
}
