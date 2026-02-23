<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

/**
 * Subscriptions Payment class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class Payment.
 */
class Payment extends XoopsObject
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    public const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    public function __construct()
    {
        $this->initVar('payment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('sub_id', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('user_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('invoice_id', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('gateway', XOBJ_DTYPE_TXTBOX, '', true);
        $this->initVar('gateway_txn_id', XOBJ_DTYPE_TXTBOX, '', false);
        $this->initVar('amount', XOBJ_DTYPE_OTHER, '0.00', true);
        $this->initVar('currency', XOBJ_DTYPE_TXTBOX, 'USD', false);
        $this->initVar('status', XOBJ_DTYPE_TXTBOX, self::STATUS_PENDING, false);
        $this->initVar('payment_method', XOBJ_DTYPE_TXTBOX, '', false);
        $this->initVar('payment_data', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('ip_address', XOBJ_DTYPE_TXTBOX, '', false);
        $this->initVar('paid_at', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created_at', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Mark payment as completed.
     *
     * @param string $txnId Gateway transaction ID
     * @param array $data Raw gateway response
     *
     * @return bool
     */
    public function markCompleted(string $txnId, array $data = []): bool
    {
        $helper = Helper::getInstance();
        $this->setVar('status', self::STATUS_COMPLETED);
        $this->setVar('gateway_txn_id', $txnId);
        $this->setVar('payment_data', json_encode($data));
        $this->setVar('paid_at', time());
        $handler = $helper->getHandler('Payment');

        return $handler->insert($this);
    }

    /**
     * Mark payment as failed.
     *
     * @param array $data
     *
     * @return bool
     */
    public function markFailed(array $data = []): bool
    {
        $helper = Helper::getInstance();
        $this->setVar('status', self::STATUS_FAILED);
        $this->setVar('payment_data', json_encode($data));
        $handler = $helper->getHandler('Payment');

        return $handler->insert($this);
    }

    /**
     * Get payment data as decoded array.
     *
     * @return array
     */
    public function getPaymentData(): array
    {
        $raw = $this->getVar('payment_data', 'n');
        if (empty($raw)) {
            return [];
        }

        return json_decode($raw, true) ?? [];
    }
}
