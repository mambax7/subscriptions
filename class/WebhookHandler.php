<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Webhook class
 *
 * Handles outgoing webhook delivery for subscription/payment events.
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * class WebhookHandler
 */
class WebhookHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_webhooks', Webhook::class, 'webhook_id', 'url');
    }

    /**
     * Fire an event to all active webhooks subscribed to it
     *
     * @param string $eventType
     * @param array  $payload
     */
    public function fire(string $eventType, array $payload = []): void
    {
        $criteria = new \Criteria('is_active', 1);
        $webhooks = $this->getAll($criteria);

        $body = json_encode([
            'event'     => $eventType,
            'timestamp' => time(),
            'data'      => $payload,
        ]);

        foreach ($webhooks as $webhook) {
            if (!$webhook->subscribesTo($eventType)) {
                continue;
            }
            $this->deliver($webhook, $body, $eventType);
        }
    }

    /**
     * Deliver a webhook payload to a specific URL
     *
     * @param Webhook $webhook
     * @param string  $body       JSON-encoded body
     * @param string  $eventType  Event type string for the header
     */
    private function deliver(Webhook $webhook, string $body, string $eventType = ''): void
    {
        if (!function_exists('curl_init')) {
            return;
        }
        $url    = $webhook->getVar('url', 'n');
        $secret = $webhook->getVar('secret', 'n');
        $sig    = hash_hmac('sha256', $body, $secret);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Subscriptions-Signature: sha256=' . $sig,
                'X-Subscriptions-Event: ' . $eventType,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_errno($ch);
        curl_close($ch);

        // Update webhook stats
        $success = !$curlErr && $httpCode >= 200 && $httpCode < 300;
        if ($success) {
            $webhook->setVar('last_fired', time());
            $webhook->setVar('fail_count', 0);
        } else {
            $webhook->setVar('fail_count', (int)$webhook->getVar('fail_count') + 1);
            // Disable after 10 consecutive failures
            if ((int)$webhook->getVar('fail_count') >= 10) {
                $webhook->setVar('is_active', 0);
            }
        }
        $this->insert($webhook);
    }
}
