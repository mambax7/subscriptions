<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use XoopsDatabase;
use XoopsPersistableObjectHandler;

/**
 * Subscriptions Access Control.
 *
 * Enforces subscription-based access to connected modules.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class ConnectedModuleHandler.
 */
class ConnectedModuleHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_connected_modules', ConnectedModule::class, 'module_id', 'dirname');
    }

    /**
     * Get a connected module by its dirname.
     *
     * @param string $dirname
     *
     * @return ConnectedModule|null
     */
    public function getByDirname(string $dirname): ?ConnectedModule
    {
        $criteria = new Criteria('dirname', $dirname, '=');
        $results = $this->getAll($criteria);

        return ! empty($results) ? reset($results) : null;
    }

    /**
     * Generate a random API key for a connected module.
     *
     * @return string
     */
    public function generateApiKey(): string
    {
        return bin2hex(random_bytes(32));
    }
}
