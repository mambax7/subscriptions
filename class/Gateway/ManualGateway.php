<?php

namespace XoopsModules\Subscriptions\Gateway;
/**
 * Subscriptions Manual/Offline Gateway Adapter
 *
 * For offline or manual payment handling (bank transfer, cheque, etc.)
 *
 * @package    subscriptions
 * @subpackage class/gateway
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

use XoopsModules\Subscriptions\AbstractGateway;
use XoopsModules\Subscriptions\Payment;

/**
 * class ManualGateway
 *
 * Allows administrators to manually confirm payments.
 * Useful for bank transfers, cheques, or other offline methods.
 */
class ManualGateway extends AbstractGateway
{
    public function getIdentifier(): string
    {
        return 'manual';
    }

    public function getName(): string
    {
        return 'Manual / Offline';
    }

    public function initiatePayment(Payment $payment, array $params = []): array
    {
        $instructions = $this->getConfig('instructions', _MD_SUBSCRIPTIONS_MANUAL_INSTRUCTIONS);
        $paymentId    = (int)$payment->getVar('payment_id');
        $this->log('Manual payment initiated', ['payment_id' => $paymentId]);
        return [
            'redirect_url' => $params['return_url'] ?? '',
            'form_fields'  => [],
            'instructions' => $instructions,
            'error'        => '',
        ];
    }

    public function handleCallback(array $data): array
    {
        // Manual gateway does not have automatic callbacks
        return [
            'success'    => false,
            'txn_id'     => '',
            'amount'     => 0.0,
            'payment_id' => 0,
            'status'     => 'pending',
            'error'      => 'Manual payment requires admin approval',
        ];
    }

    public function refund(string $txnId, float $amount, string $reason = ''): array
    {
        // Manual refunds are handled offline
        return [
            'success'   => true,
            'refund_id' => 'MANUAL-' . time(),
            'error'     => '',
        ];
    }

    public function verifyPayment(string $txnId): bool
    {
        // Manual verifications are done by admin
        return false;
    }
}
