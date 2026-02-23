<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

use function in_array;

/**
 * Subscriptions Subscription class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class Subscription.
 */
class Subscription extends XoopsObject
{
    public const STATUS_TRIAL = 'trial';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAST_DUE = 'past_due';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_PAUSED = 'paused';

    public function __construct()
    {
        $this->initVar('sub_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('user_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('plan_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('status', XOBJ_DTYPE_TXTBOX, self::STATUS_ACTIVE, false);
        $this->initVar('billing_cycle', XOBJ_DTYPE_TXTBOX, 'monthly', false);
        $this->initVar('started_at', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('trial_ends_at', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('current_period_start', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('current_period_end', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('cancelled_at', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('cancel_reason', XOBJ_DTYPE_TXTBOX, '', false);
        $this->initVar('cancel_at_period_end', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('gateway', XOBJ_DTYPE_TXTBOX, '', false);
        $this->initVar('gateway_sub_id', XOBJ_DTYPE_TXTBOX, '', false);
        $this->initVar('coupon_id', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('discount_amount', XOBJ_DTYPE_OTHER, '0.00', false);
        $this->initVar('amount_paid', XOBJ_DTYPE_OTHER, '0.00', false);
        $this->initVar('auto_renew', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('created_at', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('updated_at', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Check if this subscription is currently active (including trial).
     * A subscription scheduled to cancel at period end is still considered
     * active until the period actually expires.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $status = $this->getVar('status');
        if (! in_array($status, [self::STATUS_ACTIVE, self::STATUS_TRIAL, self::STATUS_PAST_DUE], true)) {
            return false;
        }
        $periodEnd = (int) $this->getVar('current_period_end');

        return ! ($periodEnd > 0 && $periodEnd < time());
    }

    /**
     * Whether this subscription is scheduled to cancel at the end of the current period.
     *
     * @return bool
     */
    public function isCancelAtPeriodEnd(): bool
    {
        return (int) $this->getVar('cancel_at_period_end') === 1;
    }

    /**
     * Check if within grace period.
     *
     * @param int $graceDays
     *
     * @return bool
     */
    public function isInGracePeriod(int $graceDays = 3): bool
    {
        $periodEnd = (int) $this->getVar('current_period_end');
        if ($periodEnd === 0) {
            return false;
        }
        $graceEnd = $periodEnd + ($graceDays * 86400);

        return time() <= $graceEnd;
    }

    /**
     * Get the plan object for this subscription.
     *
     * @return Plan|null
     */
    public function getPlan(): ?Plan
    {
        $helper = Helper::getInstance();
        $handler = $helper->getHandler('Plan');

        return $handler->get($this->getVar('plan_id'));
    }

    /**
     * Get status label.
     *
     * @return string
     */
    public function getStatusLabel(): string
    {
        $labels = [
            self::STATUS_TRIAL     => _MD_SUBSCRIPTIONS_STATUS_TRIAL,
            self::STATUS_ACTIVE    => _MD_SUBSCRIPTIONS_STATUS_ACTIVE,
            self::STATUS_PAST_DUE  => _MD_SUBSCRIPTIONS_STATUS_PAST_DUE,
            self::STATUS_CANCELLED => _MD_SUBSCRIPTIONS_STATUS_CANCELLED,
            self::STATUS_EXPIRED   => _MD_SUBSCRIPTIONS_STATUS_EXPIRED,
            self::STATUS_PAUSED    => _MD_SUBSCRIPTIONS_STATUS_PAUSED,
        ];

        return $labels[$this->getVar('status')] ?? $this->getVar('status');
    }

    /**
     * Cancel this subscription.
     *
     * @param string $reason
     * @param bool $atPeriodEnd true = keep active until period ends, false = cancel immediately
     *
     * @return bool
     */
    public function cancel(string $reason = '', bool $atPeriodEnd = false): bool
    {
        $helper = Helper::getInstance();
        if ($atPeriodEnd) {
            // Schedule cancellation — subscription stays active until current_period_end
            $this->setVar('cancel_at_period_end', 1);
            $this->setVar('cancel_reason', $reason);
            $this->setVar('auto_renew', 0);
        } else {
            // Immediate cancellation
            $this->setVar('status', self::STATUS_CANCELLED);
            $this->setVar('cancelled_at', time());
            $this->setVar('cancel_reason', $reason);
            $this->setVar('cancel_at_period_end', 0);
            $this->setVar('auto_renew', 0);
        }
        $this->setVar('updated_at', time());
        $handler = $helper->getHandler('Subscription');

        return $handler->insert($this);
    }

    /**
     * Reactivate a subscription that was scheduled to cancel at period end.
     * Clears the cancel_at_period_end flag and re-enables auto-renew.
     *
     * @return bool
     */
    public function reactivate(): bool
    {
        $helper = Helper::getInstance();
        $this->setVar('cancel_at_period_end', 0);
        $this->setVar('auto_renew', 1);
        $this->setVar('updated_at', time());
        $handler = $helper->getHandler('Subscription');

        return $handler->insert($this);
    }
}
