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
 * class ModuleAccessRuleHandler.
 */
class ModuleAccessRuleHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_module_access_rules', ModuleAccessRule::class, 'rule_id', 'rule_id');
    }

    /**
     * Get all rules for a module.
     *
     * @param int $moduleId
     *
     * @return ModuleAccessRule[]
     */
    public function getForModule(int $moduleId): array
    {
        $criteria = new Criteria('module_id', $moduleId);

        return $this->getAll($criteria);
    }
}
