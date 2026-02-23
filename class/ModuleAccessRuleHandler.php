<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Access Control
 *
 * Enforces subscription-based access to connected modules.
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * class ModuleAccessRuleHandler
 */
class ModuleAccessRuleHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_module_access_rules', ModuleAccessRule::class, 'rule_id', 'rule_id');
    }

    /**
     * Get all rules for a module
     *
     * @param int $moduleId
     * @return ModuleAccessRule[]
     */
    public function getForModule(int $moduleId): array
    {
        $criteria = new \Criteria('module_id', $moduleId);
        return $this->getAll($criteria);
    }
}
