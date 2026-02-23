<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use InvalidArgumentException;

use function defined;

/**
 * Subscriptions Payment Gateway abstraction.
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Factory: create a gateway instance by identifier.
 */
class GatewayFactory
{
    /**
     * @param string $identifier
     *
     * @throws InvalidArgumentException
     *
     * @return GatewayInterface
     */
    public static function create(string $identifier): GatewayInterface
    {
        $classMap = [
            'paypal' => Gateway\PaypalGateway::class,
            'stripe' => Gateway\StripeGateway::class,
            'manual' => Gateway\ManualGateway::class,
        ];

        if (! isset($classMap[$identifier])) {
            throw new InvalidArgumentException("Unknown gateway: {$identifier}");
        }

        $fqcn = $classMap[$identifier];
        if (! class_exists($fqcn, false)) {
            $file = XOOPS_ROOT_PATH . '/modules/subscriptions/class/Gateway/' . ucfirst($identifier) . 'Gateway.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }

        return new $fqcn();
    }

    /**
     * Return all registered gateway identifiers.
     *
     * @return string[]
     */
    public static function getAvailable(): array
    {
        return ['paypal', 'stripe', 'manual'];
    }
}
