<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Payment Gateway abstraction
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * Abstract base class with shared logic for gateway implementations.
 */
abstract class AbstractGateway implements GatewayInterface
{
    /** @var array Key-value config loaded from DB */
    protected array $config = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Load gateway config from the database
     */
    protected function loadConfig(): void
    {
        $helper = Helper::getInstance();
        $handler = $helper->getHandler('GatewayConfig');
        if (!$handler) {
            return;
        }
        $criteria = new \Criteria('gateway', $this->getIdentifier());
        $rows     = $handler->getAll($criteria);
        foreach ($rows as $row) {
            $this->config[$row->getVar('config_key', 'n')] = $row->getVar('config_val', 'n');
        }
    }

    /**
     * Get a config value
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    protected function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Log gateway communication (non-sensitive data only)
     *
     * @param string $message
     * @param array  $context
     */
    protected function log(string $message, array $context = []): void
    {
        // Write to a non-web-accessible log directory
        $logDir  = XOOPS_VAR_PATH . '/logs/subscriptions';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0750, true);
        }
        $logFile = $logDir . '/gateway_' . $this->getIdentifier() . '_' . date('Y-m-d') . '.log';
        $entry   = date('Y-m-d H:i:s') . ' [' . $this->getIdentifier() . '] ' . $message;
        if (!empty($context)) {
            // Mask sensitive fields before logging
            $safeContext = $this->maskSensitiveData($context);
            $entry      .= ' ' . json_encode($safeContext);
        }
        @file_put_contents($logFile, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Mask sensitive keys in context data before logging
     *
     * @param array $data
     * @return array
     */
    private function maskSensitiveData(array $data): array
    {
        $sensitive = ['card_number', 'cvv', 'cvc', 'password', 'secret', 'api_key', 'access_token'];
        foreach ($sensitive as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***REDACTED***';
            }
        }
        return $data;
    }

    public function supportsRecurring(): bool
    {
        return false;
    }
}
