<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use CriteriaCompo;

/**
 * Subscriptions Access Control.
 *
 * Enforces subscription-based access to connected modules.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class AccessControl.
 */
class AccessControl
{
    /** @var SubscriptionHandler */
    private SubscriptionHandler $subscriptionHandler;

    /** @var ConnectedModuleHandler */
    private ConnectedModuleHandler $moduleHandler;

    public function __construct()
    {
        $helper = Helper::getInstance();
        $this->subscriptionHandler = $helper->getHandler('Subscription');
        $this->moduleHandler = $helper->getHandler('ConnectedModule');
    }

    /**
     * Check if a user has access to a given module.
     *
     * @param int $userId
     * @param string $moduleDirname
     *
     * @return bool
     */
    public function userHasAccess(int $userId, string $moduleDirname): bool
    {
        if ($userId <= 0) {
            return false;
        }
        // Admins always have access
        if ($this->isAdmin($userId)) {
            return true;
        }

        $module = $this->moduleHandler->getByDirname($moduleDirname);
        if (! $module || ! (int) $module->getVar('is_active')) {
            return false;
        }

        $moduleId = (int) $module->getVar('module_id');
        $helper = Helper::getInstance();

        // Fetch active subscriptions for user
        $sub = $this->subscriptionHandler->getActiveForUser($userId);
        if (! $sub) {
            return false;
        }
        if (! $sub->isActive()) {
            return false;
        }

        // Check if the plan grants access to this module
        $planId = (int) $sub->getVar('plan_id');
        $ruleHandler = $helper->getHandler('ModuleAccessRule');
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('module_id', $moduleId));
        $criteria->add(new Criteria('plan_id', $planId));
        $rules = $ruleHandler->getAll($criteria);

        return ! empty($rules);
    }

    /**
     * Enforce access: redirect to plans page if access denied.
     *
     * @param int $userId
     * @param string $moduleDirname
     */
    public function enforceAccess(int $userId, string $moduleDirname): void
    {
        if (! $this->userHasAccess($userId, $moduleDirname)) {
            $url = XOOPS_URL . '/modules/subscriptions/plans.php?required_module=' . rawurlencode($moduleDirname);
            redirect_header($url, 2, _MD_SUBSCRIPTIONS_ACCESS_DENIED);
        }
    }

    /**
     * Get access type for a user+module combination ('full', 'limited', 'metered', or null).
     *
     * @param int $userId
     * @param string $moduleDirname
     *
     * @return string|null
     */
    public function getAccessType(int $userId, string $moduleDirname): ?string
    {
        if ($userId <= 0) {
            return null;
        }
        if ($this->isAdmin($userId)) {
            return 'full';
        }
        $module = $this->moduleHandler->getByDirname($moduleDirname);
        if (! $module) {
            return null;
        }
        $helper = Helper::getInstance();
        $moduleId = (int) $module->getVar('module_id');
        $sub = $this->subscriptionHandler->getActiveForUser($userId);
        if (! $sub || ! $sub->isActive()) {
            return null;
        }
        $planId = (int) $sub->getVar('plan_id');
        $ruleHandler = $helper->getHandler('ModuleAccessRule');
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('module_id', $moduleId));
        $criteria->add(new Criteria('plan_id', $planId));
        $rules = $ruleHandler->getAll($criteria);
        if (empty($rules)) {
            return null;
        }
        /** @var ModuleAccessRule $rule */
        $rule = reset($rules);

        return $rule->getVar('access_type', 'n');
    }

    /**
     * Record a usage event (for usage-based billing).
     *
     * @param int $userId
     * @param string $moduleDirname
     * @param string $eventType 'visit', 'download', etc.
     * @param float $quantity
     *
     * @return bool
     */
    public function recordUsage(int $userId, string $moduleDirname, string $eventType = 'visit', float $quantity = 1.0): bool
    {
        $module = $this->moduleHandler->getByDirname($moduleDirname);
        if (! $module) {
            return false;
        }
        $sub = $this->subscriptionHandler->getActiveForUser($userId);
        if (! $sub) {
            return false;
        }
        $subId = (int) $sub->getVar('sub_id');
        $moduleId = (int) $module->getVar('module_id');
        $helper = Helper::getInstance();

        // Get unit price from plan
        $plan = $sub->getPlan();
        $unitPrice = $plan ? (float) $plan->getVar('usage_price') : 0.0;

        $logHandler = $helper->getHandler('UsageLog');

        return $logHandler->record($subId, $userId, $moduleId, $eventType, $quantity, $unitPrice);
    }

    /**
     * Sync XOOPS group membership for a user based on all active access rules
     * that have an xoops_group set for their current active subscription's plan.
     *
     * Call this after a subscription becomes active OR after cancellation/expiry.
     *
     * @param int $userId
     * @param bool $adding true = add to groups granted by plan; false = remove them
     */
    public function syncXoopsGroups(int $userId, bool $adding): void
    {
        if ($userId <= 0) {
            return;
        }
        $helper = Helper::getInstance();
        $ruleHandler = $helper->getHandler('ModuleAccessRule');

        // Collect all xoops_group IDs from the rules for this user's plan
        $groupIds = [];
        if ($adding) {
            $sub = $this->subscriptionHandler->getActiveForUser($userId);
            if ($sub && $sub->isActive()) {
                $planId = (int) $sub->getVar('plan_id');
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('plan_id', $planId));
                $criteria->add(new Criteria('xoops_group', 0, '>'));
                $rules = $ruleHandler->getAll($criteria);
                foreach ($rules as $rule) {
                    $gid = (int) $rule->getVar('xoops_group');
                    if ($gid > 0) {
                        $groupIds[] = $gid;
                    }
                }
            }
        } else {
            // When removing, find ALL rules with any xoops_group (across all plans)
            // so we clean up even if plan changed
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('xoops_group', 0, '>'));
            $rules = $ruleHandler->getAll($criteria);
            foreach ($rules as $rule) {
                $gid = (int) $rule->getVar('xoops_group');
                if ($gid > 0) {
                    $groupIds[] = $gid;
                }
            }
        }

        if (empty($groupIds)) {
            return;
        }

        /** @var \XoopsUser $xUser */
        $xUser = xoops_getHandler('user')->get($userId);
        if (! $xUser) {
            return;
        }

        $memberHandler = xoops_getHandler('member');
        $groupIds = array_unique($groupIds);

        foreach ($groupIds as $gid) {
            if ($adding) {
                // addUserToGroup returns false silently if already a member
                $memberHandler->addUserToGroup($gid, $userId);
            } else {
                $memberHandler->removeUsersFromGroup($gid, [$userId]);
            }
        }
    }

    /**
     * Check if user is an XOOPS admin.
     *
     * @param int $userId
     *
     * @return bool
     */
    private function isAdmin(int $userId): bool
    {
        /** @var XoopsUser $user */
        $user = xoops_getHandler('user')->get($userId);

        return $user && $user->isAdmin();
    }
}
