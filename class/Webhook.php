<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

use function in_array;

/**
 * Subscriptions Webhook class.
 *
 * Handles outgoing webhook delivery for subscription/payment events.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class Webhook.
 */
class Webhook extends XoopsObject
{
    /** Supported event types */
    public const EVENT_SUBSCRIPTION_CREATED = 'subscription.created';

    public const EVENT_SUBSCRIPTION_RENEWED = 'subscription.renewed';

    public const EVENT_SUBSCRIPTION_CANCELLED = 'subscription.cancelled';

    public const EVENT_SUBSCRIPTION_EXPIRED = 'subscription.expired';

    public const EVENT_PAYMENT_COMPLETED = 'payment.completed';

    public const EVENT_PAYMENT_FAILED = 'payment.failed';

    public const EVENT_REFUND_COMPLETED = 'refund.completed';

    public const EVENT_TRIAL_STARTED = 'trial.started';

    public const EVENT_TRIAL_ENDING = 'trial.ending';

    public function __construct()
    {
        $this->initVar('webhook_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('url', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('events', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('secret', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('is_active', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('last_fired', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('fail_count', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created_at', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Get the list of subscribed event types.
     *
     * @return array
     */
    public function getEvents(): array
    {
        $raw = $this->getVar('events', 'n');

        return json_decode($raw, true) ?? [];
    }

    /**
     * Check if this webhook is subscribed to a given event.
     *
     * @param string $event
     *
     * @return bool
     */
    public function subscribesTo(string $event): bool
    {
        $events = $this->getEvents();

        return in_array($event, $events, true) || in_array('*', $events, true);
    }
}
