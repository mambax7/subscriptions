<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use CriteriaCompo;
use XoopsDatabase;
use XoopsPersistableObjectHandler;

use function sprintf;

/**
 * Subscriptions Payment class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class PaymentHandler.
 */
class PaymentHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_payments', Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get all payments for a user.
     *
     * @param int $userId
     *
     * @return Payment[]
     */
    public function getForUser(int $userId): array
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('user_id', $userId));
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');

        return $this->getAll($criteria);
    }

    /**
     * Get all payments for a subscription.
     *
     * @param int $subId
     *
     * @return Payment[]
     */
    public function getForSubscription(int $subId): array
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('sub_id', $subId));
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');

        return $this->getAll($criteria);
    }

    /**
     * Create a pending payment record.
     *
     * @param int $userId
     * @param int $subId
     * @param float $amount
     * @param string $gateway
     * @param string $currency
     * @param int $invoiceId
     *
     * @return Payment
     */
    public function createPending(
        int $userId,
        int $subId,
        float $amount,
        string $gateway,
        string $currency = 'USD',
        int $invoiceId = 0
    ): Payment {
        /** @var Payment $payment */
        $payment = $this->create();
        $payment->setVar('user_id', $userId);
        $payment->setVar('sub_id', $subId);
        $payment->setVar('invoice_id', $invoiceId);
        $payment->setVar('gateway', $gateway);
        $payment->setVar('amount', $amount);
        $payment->setVar('currency', $currency);
        $payment->setVar('status', Payment::STATUS_PENDING);
        $payment->setVar('ip_address', filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : '');
        $payment->setVar('created_at', time());
        $this->insert($payment);

        return $payment;
    }

    /**
     * Sum of completed payments for a subscription.
     *
     * @param int $subId
     *
     * @return float
     */
    public function getTotalPaidForSubscription(int $subId): float
    {
        $sql = sprintf(
            "SELECT SUM(amount) FROM %s WHERE sub_id = %d AND status = 'completed'",
            $this->table,
            (int) $subId
        );
        $result = $this->db->query($sql);
        if ($result) {
            [$total] = $this->db->fetchRow($result);

            return (float) $total;
        }

        return 0.0;
    }
}
