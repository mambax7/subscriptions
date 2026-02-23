<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use CriteriaCompo;
use XoopsDatabase;
use XoopsPersistableObjectHandler;

/**
 * Subscriptions Payment Gateway abstraction.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class GatewayConfigHandler.
 */
class GatewayConfigHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_gateway_configs', GatewayConfig::class, 'config_id', 'config_key');
    }

    /**
     * Save a key-value pair for a gateway.
     *
     * @param string $gateway
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function saveConfig(string $gateway, string $key, string $value): bool
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gateway', $gateway, '='));
        $criteria->add(new Criteria('config_key', $key, '='));
        $existing = $this->getAll($criteria);
        /** @var GatewayConfig $obj */
        if (! empty($existing)) {
            $obj = reset($existing);
        } else {
            $obj = $this->create();
            $obj->setVar('gateway', $gateway);
            $obj->setVar('config_key', $key);
        }
        $obj->setVar('config_val', $value);

        return $this->insert($obj);
    }
}
