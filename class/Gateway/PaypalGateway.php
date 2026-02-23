<?php

namespace XoopsModules\Subscriptions\Gateway;
/**
 * Subscriptions PayPal Gateway Adapter
 *
 * Uses PayPal Standard (NVP/Checkout) - pluggable architecture
 *
 * @package    subscriptions
 * @subpackage class/gateway
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

use XoopsModules\Subscriptions\AbstractGateway;
use XoopsModules\Subscriptions\Payment;

/**
 * class PaypalGateway
 *
 * Implements PayPal Payments Standard (IPN-based) integration.
 * For production use, replace with PayPal REST SDK or PayPal Checkout v2.
 */
class PaypalGateway extends AbstractGateway
{
    private const SANDBOX_URL    = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    private const PRODUCTION_URL = 'https://www.paypal.com/cgi-bin/webscr';
    private const IPN_SANDBOX    = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    private const IPN_PRODUCTION = 'https://ipnpb.paypal.com/cgi-bin/webscr';

    public function getIdentifier(): string
    {
        return 'paypal';
    }

    public function getName(): string
    {
        return 'PayPal';
    }

    public function supportsRecurring(): bool
    {
        return true;
    }

    /**
     * Build a PayPal redirect URL for a single payment
     *
     * @param Payment $payment
     * @param array              $params  ['return_url', 'cancel_url', 'notify_url', 'item_name']
     * @return array
     */
    public function initiatePayment(Payment $payment, array $params = []): array
    {
        $business   = $this->getConfig('business_email', '');
        if (empty($business)) {
            return ['redirect_url' => '', 'form_fields' => [], 'error' => _MD_SUBSCRIPTIONS_GATEWAY_NOT_CONFIGURED];
        }

        $isSandbox  = (bool)(int)$this->getConfig('sandbox', 1);
        $baseUrl    = $isSandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $currency   = $payment->getVar('currency', 'n');
        $amount     = number_format((float)$payment->getVar('amount', 'n'), 2, '.', '');
        $paymentId  = (int)$payment->getVar('payment_id');

        $fields = [
            'cmd'           => '_xclick',
            'business'      => $business,
            'item_name'     => $params['item_name']  ?? _MD_SUBSCRIPTIONS_SUBSCRIPTION,
            'amount'        => $amount,
            'currency_code' => $currency,
            'no_shipping'   => '1',
            'no_note'       => '1',
            'custom'        => $paymentId,
            'return'        => $params['return_url']  ?? '',
            'cancel_return' => $params['cancel_url']  ?? '',
            'notify_url'    => $params['notify_url']  ?? '',
            'charset'       => 'UTF-8',
        ];

        $redirectUrl = $baseUrl . '?' . http_build_query($fields);
        $this->log('Initiated payment', ['payment_id' => $paymentId, 'amount' => $amount]);

        return [
            'redirect_url' => $redirectUrl,
            'form_fields'  => $fields,
            'error'        => '',
        ];
    }

    /**
     * Handle a PayPal IPN notification
     *
     * @param array $data  $_POST data from PayPal IPN
     * @return array
     */
    public function handleCallback(array $data): array
    {
        // Verify IPN with PayPal
        if (!$this->verifyIpn($data)) {
            $this->log('IPN verification failed', ['txn_id' => $data['txn_id'] ?? '']);
            return ['success' => false, 'txn_id' => '', 'amount' => 0.0, 'status' => 'failed', 'error' => 'IPN verification failed'];
        }

        $txnId    = $data['txn_id']         ?? '';
        $status   = strtolower($data['payment_status'] ?? '');
        $amount   = (float)($data['mc_gross']          ?? 0);
        $currency = $data['mc_currency']               ?? 'USD';
        $custom   = (int)($data['custom']              ?? 0); // payment_id

        $this->log('IPN received', ['txn_id' => $txnId, 'status' => $status, 'amount' => $amount]);

        if ($status === 'completed') {
            return [
                'success'    => true,
                'txn_id'     => $txnId,
                'amount'     => $amount,
                'currency'   => $currency,
                'payment_id' => $custom,
                'status'     => 'completed',
                'error'      => '',
            ];
        }

        return [
            'success'    => false,
            'txn_id'     => $txnId,
            'amount'     => $amount,
            'payment_id' => $custom,
            'status'     => $status,
            'error'      => "Payment status: {$status}",
        ];
    }

    /**
     * Refund a PayPal transaction via NVP API
     *
     * @param string $txnId
     * @param float  $amount
     * @param string $reason
     * @return array
     */
    public function refund(string $txnId, float $amount, string $reason = ''): array
    {
        $apiUser     = $this->getConfig('api_username', '');
        $apiPassword = $this->getConfig('api_password', '');
        $apiSig      = $this->getConfig('api_signature', '');
        $isSandbox   = (bool)(int)$this->getConfig('sandbox', 1);

        if (empty($apiUser) || empty($apiPassword) || empty($apiSig)) {
            return ['success' => false, 'refund_id' => '', 'error' => _MD_SUBSCRIPTIONS_GATEWAY_NOT_CONFIGURED];
        }

        $endpoint = $isSandbox
            ? 'https://api-3t.sandbox.paypal.com/nvp'
            : 'https://api-3t.paypal.com/nvp';

        $params = [
            'METHOD'           => 'RefundTransaction',
            'VERSION'          => '204',
            'USER'             => $apiUser,
            'PWD'              => $apiPassword,
            'SIGNATURE'        => $apiSig,
            'TRANSACTIONID'    => $txnId,
            'REFUNDTYPE'       => 'Partial',
            'AMT'              => number_format($amount, 2, '.', ''),
            'NOTE'             => substr($reason, 0, 255),
        ];

        $response = $this->httpPost($endpoint, $params);
        parse_str($response, $parsed);

        if (isset($parsed['ACK']) && strtolower($parsed['ACK']) === 'success') {
            return [
                'success'   => true,
                'refund_id' => $parsed['REFUNDTRANSACTIONID'] ?? '',
                'error'     => '',
            ];
        }

        $errorMsg = $parsed['L_LONGMESSAGE0'] ?? 'Refund failed';
        return ['success' => false, 'refund_id' => '', 'error' => $errorMsg];
    }

    /**
     * Verify a transaction using NVP GetTransactionDetails
     *
     * @param string $txnId
     * @return bool
     */
    public function verifyPayment(string $txnId): bool
    {
        $apiUser    = $this->getConfig('api_username', '');
        $apiPassword= $this->getConfig('api_password', '');
        $apiSig     = $this->getConfig('api_signature', '');
        $isSandbox  = (bool)(int)$this->getConfig('sandbox', 1);

        if (empty($apiUser)) {
            return false;
        }

        $endpoint = $isSandbox
            ? 'https://api-3t.sandbox.paypal.com/nvp'
            : 'https://api-3t.paypal.com/nvp';

        $params = [
            'METHOD'        => 'GetTransactionDetails',
            'VERSION'       => '204',
            'USER'          => $apiUser,
            'PWD'           => $apiPassword,
            'SIGNATURE'     => $apiSig,
            'TRANSACTIONID' => $txnId,
        ];

        $response = $this->httpPost($endpoint, $params);
        parse_str($response, $parsed);

        return isset($parsed['ACK'])
            && strpos(strtolower($parsed['ACK']), 'success') === 0
            && strtolower($parsed['PAYMENTSTATUS'] ?? '') === 'completed';
    }

    /**
     * Verify a PayPal IPN message by posting back to PayPal
     *
     * @param array $data
     * @return bool
     */
    private function verifyIpn(array $data): bool
    {
        $isSandbox = (bool)(int)$this->getConfig('sandbox', 1);
        $ipnUrl    = $isSandbox ? self::IPN_SANDBOX : self::IPN_PRODUCTION;

        $body = 'cmd=_notify-validate&' . http_build_query($data);
        $response = $this->httpPost($ipnUrl, [], $body);
        return $response === 'VERIFIED';
    }

    /**
     * Perform a simple HTTPS POST request
     *
     * @param string $url
     * @param array  $fields
     * @param string $rawBody  If provided, post this instead of $fields
     * @return string
     */
    private function httpPost(string $url, array $fields = [], string $rawBody = ''): string
    {
        if (!function_exists('curl_init')) {
            return '';
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $rawBody !== '' ? $rawBody : http_build_query($fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->log('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return (string)$result;
    }
}
