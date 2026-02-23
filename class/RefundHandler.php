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
 * class RefundHandler
 */
class RefundHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_refunds', Refund::class, 'refund_id', 'refund_id');
    }

    /**
     * Process a refund for a payment
     *
     * @param int    $paymentId
     * @param float  $amount
     * @param string $reason
     * @param int    $adminUid  Admin user performing the refund
     * @return array ['success' => bool, 'error' => string]
     */
    public function processRefund(int $paymentId, float $amount, string $reason, int $adminUid): array
    {
        $helper = Helper::getInstance();
        /** @var Payment $payment */
        $paymentHandler = $helper->getHandler('Payment');
        $payment        = $paymentHandler->get($paymentId);
        if (!$payment) {
            return ['success' => false, 'error' => _MD_SUBSCRIPTIONS_PAYMENT_NOT_FOUND];
        }
        if ($payment->getVar('status', 'n') !== Payment::STATUS_COMPLETED) {
            return ['success' => false, 'error' => _MD_SUBSCRIPTIONS_REFUND_NOT_ELIGIBLE];
        }
        $paidAmount = (float)$payment->getVar('amount', 'n');
        if ($amount > $paidAmount) {
            return ['success' => false, 'error' => _MD_SUBSCRIPTIONS_REFUND_EXCEEDS_PAYMENT];
        }

        // Call gateway refund
        $gateway  = GatewayFactory::create($payment->getVar('gateway', 'n'));
        $result   = $gateway->refund($payment->getVar('gateway_txn_id', 'n'), $amount, $reason);

        /** @var Refund $refund */
        $refund = $this->create();
        $refund->setVar('payment_id',        $paymentId);
        $refund->setVar('sub_id',            (int)$payment->getVar('sub_id', 'n'));
        $refund->setVar('user_id',           (int)$payment->getVar('user_id', 'n'));
        $refund->setVar('amount',            $amount);
        $refund->setVar('reason',            $reason);
        $refund->setVar('processed_by',      $adminUid);
        $refund->setVar('created_at',        time());

        if ($result['success']) {
            $refund->setVar('status',           Refund::STATUS_COMPLETED);
            $refund->setVar('gateway_refund_id',$result['refund_id'] ?? '');
            $this->insert($refund);

            // Update payment status
            $newStatus = ($amount >= $paidAmount)
                ? Payment::STATUS_REFUNDED
                : Payment::STATUS_PARTIALLY_REFUNDED;
            $payment->setVar('status', $newStatus);
            $paymentHandler->insert($payment);

            return ['success' => true, 'error' => ''];
        }

        $refund->setVar('status', Refund::STATUS_FAILED);
        $this->insert($refund);
        return ['success' => false, 'error' => $result['error'] ?? 'Refund failed'];
    }
}
