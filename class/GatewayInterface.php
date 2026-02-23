<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

/**
 * Subscriptions Payment Gateway abstraction.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Interface GatewayInterface.
 *
 * All payment gateway adapters must implement this interface.
 */
interface GatewayInterface
{
    /**
     * Initiate a payment / redirect URL.
     *
     * @param Payment $payment
     * @param array $params Additional params (return_url, cancel_url, etc.)
     *
     * @return array ['redirect_url' => string, 'form_fields' => array, 'error' => string]
     */
    public function initiatePayment(Payment $payment, array $params = []): array;

    /**
     * Handle a payment callback / IPN from the gateway.
     *
     * @param array $data Raw POST/GET data from gateway
     *
     * @return array ['success' => bool, 'txn_id' => string, 'amount' => float, 'status' => string, 'error' => string]
     */
    public function handleCallback(array $data): array;

    /**
     * Refund a transaction.
     *
     * @param string $txnId
     * @param float $amount
     * @param string $reason
     *
     * @return array ['success' => bool, 'refund_id' => string, 'error' => string]
     */
    public function refund(string $txnId, float $amount, string $reason = ''): array;

    /**
     * Verify whether a payment was actually completed (used for reconciliation).
     *
     * @param string $txnId
     *
     * @return bool
     */
    public function verifyPayment(string $txnId): bool;

    /**
     * Get the gateway identifier string.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Get display name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if this gateway supports recurring billing.
     *
     * @return bool
     */
    public function supportsRecurring(): bool;
}
