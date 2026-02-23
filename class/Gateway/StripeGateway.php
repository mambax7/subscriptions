<?php

namespace XoopsModules\Subscriptions\Gateway;
/**
 * Subscriptions Stripe Gateway Adapter
 *
 * Uses Stripe Checkout / Payment Intents API
 *
 * @package    subscriptions
 * @subpackage class/gateway
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

use XoopsModules\Subscriptions\AbstractGateway;
use XoopsModules\Subscriptions\Payment;

/**
 * class StripeGateway
 *
 * Integrates with the Stripe API using direct HTTPS calls (no SDK dependency).
 * Supports Payment Intents (SCA-compliant) and Stripe Checkout Sessions.
 */
class StripeGateway extends AbstractGateway
{
    private const API_BASE = 'https://api.stripe.com/v1';

    public function getIdentifier(): string
    {
        return 'stripe';
    }

    public function getName(): string
    {
        return 'Stripe';
    }

    public function supportsRecurring(): bool
    {
        return true;
    }

    /**
     * Create a Stripe Checkout Session and return the redirect URL
     *
     * @param Payment $payment
     * @param array              $params  ['return_url', 'cancel_url', 'item_name']
     * @return array
     */
    public function initiatePayment(Payment $payment, array $params = []): array
    {
        $secretKey = $this->getConfig('secret_key', '');
        if (empty($secretKey)) {
            return ['redirect_url' => '', 'form_fields' => [], 'error' => _MD_SUBSCRIPTIONS_GATEWAY_NOT_CONFIGURED];
        }

        $amountCents = (int)round((float)$payment->getVar('amount', 'n') * 100);
        $currency    = strtolower($payment->getVar('currency', 'n') ?: 'usd');
        $paymentId   = (int)$payment->getVar('payment_id');

        $fields = [
            'payment_method_types[]'             => 'card',
            'mode'                                => 'payment',
            'line_items[0][price_data][currency]' => $currency,
            'line_items[0][price_data][product_data][name]' => $params['item_name'] ?? _MD_SUBSCRIPTIONS_SUBSCRIPTION,
            'line_items[0][price_data][unit_amount]'        => $amountCents,
            'line_items[0][quantity]'             => 1,
            'success_url'                         => ($params['return_url'] ?? '') . '?payment_id=' . $paymentId . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'                          => $params['cancel_url'] ?? '',
            'metadata[payment_id]'                => $paymentId,
        ];

        $response = $this->apiRequest('POST', '/checkout/sessions', $fields, $secretKey);

        if (isset($response['error'])) {
            $this->log('Checkout session error', ['error' => $response['error']['message'] ?? '']);
            return ['redirect_url' => '', 'form_fields' => [], 'error' => $response['error']['message'] ?? 'Stripe error'];
        }

        $redirectUrl = $response['url'] ?? '';
        $this->log('Checkout session created', ['session_id' => $response['id'] ?? '', 'payment_id' => $paymentId]);

        return [
            'redirect_url' => $redirectUrl,
            'form_fields'  => [],
            'session_id'   => $response['id'] ?? '',
            'error'        => '',
        ];
    }

    /**
     * Handle a Stripe webhook callback
     *
     * @param array $data  Decoded JSON body of the Stripe event
     * @return array
     */
    public function handleCallback(array $data): array
    {
        $eventType = $data['type'] ?? '';
        $object    = $data['data']['object'] ?? [];

        if ($eventType === 'checkout.session.completed') {
            $paymentId = $object['metadata']['payment_id'] ?? 0;
            $txnId     = $object['payment_intent']         ?? '';
            $amount    = ($object['amount_total'] ?? 0) / 100;
            $currency  = strtoupper($object['currency'] ?? 'USD');
            if ($object['payment_status'] === 'paid') {
                $this->log('Checkout session completed', ['txn_id' => $txnId, 'payment_id' => $paymentId]);
                return [
                    'success'    => true,
                    'txn_id'     => $txnId,
                    'amount'     => $amount,
                    'currency'   => $currency,
                    'payment_id' => (int)$paymentId,
                    'status'     => 'completed',
                    'error'      => '',
                ];
            }
        }

        if ($eventType === 'payment_intent.payment_failed') {
            $txnId = $object['id'] ?? '';
            $this->log('Payment intent failed', ['txn_id' => $txnId]);
            return [
                'success'    => false,
                'txn_id'     => $txnId,
                'amount'     => 0.0,
                'payment_id' => 0,
                'status'     => 'failed',
                'error'      => $object['last_payment_error']['message'] ?? 'Payment failed',
            ];
        }

        return ['success' => false, 'txn_id' => '', 'amount' => 0.0, 'payment_id' => 0, 'status' => 'unknown', 'error' => 'Unhandled event'];
    }

    /**
     * Refund a charge via Stripe API
     *
     * @param string $txnId  PaymentIntent or Charge ID
     * @param float  $amount
     * @param string $reason  'duplicate' | 'fraudulent' | 'requested_by_customer'
     * @return array
     */
    public function refund(string $txnId, float $amount, string $reason = 'requested_by_customer'): array
    {
        $secretKey = $this->getConfig('secret_key', '');
        if (empty($secretKey)) {
            return ['success' => false, 'refund_id' => '', 'error' => _MD_SUBSCRIPTIONS_GATEWAY_NOT_CONFIGURED];
        }

        $allowedReasons = ['duplicate', 'fraudulent', 'requested_by_customer'];
        if (!in_array($reason, $allowedReasons, true)) {
            $reason = 'requested_by_customer';
        }

        $fields = [
            'payment_intent' => $txnId,
            'amount'         => (int)round($amount * 100),
            'reason'         => $reason,
        ];

        $response = $this->apiRequest('POST', '/refunds', $fields, $secretKey);

        if (isset($response['error'])) {
            return ['success' => false, 'refund_id' => '', 'error' => $response['error']['message'] ?? 'Refund failed'];
        }

        return [
            'success'   => ($response['status'] ?? '') === 'succeeded',
            'refund_id' => $response['id'] ?? '',
            'error'     => '',
        ];
    }

    /**
     * Verify a PaymentIntent status
     *
     * @param string $txnId
     * @return bool
     */
    public function verifyPayment(string $txnId): bool
    {
        $secretKey = $this->getConfig('secret_key', '');
        if (empty($secretKey)) {
            return false;
        }
        $response = $this->apiRequest('GET', '/payment_intents/' . rawurlencode($txnId), [], $secretKey);
        return ($response['status'] ?? '') === 'succeeded';
    }

    /**
     * Validate a Stripe webhook signature
     *
     * @param string $payload    Raw request body
     * @param string $sigHeader  Stripe-Signature header value
     * @return bool
     */
    public function validateWebhookSignature(string $payload, string $sigHeader): bool
    {
        $secret = $this->getConfig('webhook_secret', '');
        if (empty($secret)) {
            return false;
        }
        // Parse Stripe-Signature header
        $parts = [];
        foreach (explode(',', $sigHeader) as $part) {
            [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
            $parts[$k] = $v;
        }
        $timestamp = $parts['t']  ?? '';
        $signature = $parts['v1'] ?? '';
        if (empty($timestamp) || empty($signature)) {
            return false;
        }
        $expectedSig = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        return hash_equals($expectedSig, $signature);
    }

    /**
     * Make a Stripe API request
     *
     * @param string $method   GET|POST|DELETE
     * @param string $path
     * @param array  $fields
     * @param string $secretKey
     * @return array
     */
    private function apiRequest(string $method, string $path, array $fields, string $secretKey): array
    {
        if (!function_exists('curl_init')) {
            return ['error' => ['message' => 'cURL not available']];
        }
        $url = self::API_BASE . $path;
        $ch  = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_USERPWD        => $secretKey . ':',
            CURLOPT_HTTPHEADER     => ['Stripe-Version: 2023-10-16'],
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        } elseif ($method === 'GET' && !empty($fields)) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($fields));
        }
        $body = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            $this->log('cURL error: ' . $error);
            return ['error' => ['message' => $error]];
        }
        curl_close($ch);
        return json_decode((string)$body, true) ?? [];
    }
}
