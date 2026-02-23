<?php
/**
 * Subscriptions SubscriptionsSDK
 *
 * Provides a clean API for third-party XOOPS module owners to:
 *  - Check user subscription access
 *  - Record usage events (for usage-based billing)
 *  - Get plan/subscription information
 *  - Hook into billing events
 *
 * Usage example in a third-party module:
 *
 *   require_once XOOPS_ROOT_PATH . '/modules/subscriptions/sdk/SubscriptionsSDK.php';
 *
 *   $sdk = SubscriptionsSDK::getInstance('mymodule');
 *   if (!$sdk->hasAccess($xoopsUser->getVar('uid'))) {
 *       redirect_header(XOOPS_URL . '/modules/subscriptions/plans.php', 2, 'Access denied');
 *   }
 *   $sdk->recordUsage($xoopsUser->getVar('uid'), 'download');
 *
 * @package    subscriptions
 * @subpackage sdk
 */

use XoopsModules\Subscriptions\{
    Helper
};


defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class SubscriptionsSDK
 */
class SubscriptionsSDK
{
    /** @var string  The module dirname that registered this SubscriptionsSDK instance */
    private string $moduleDirname;

    /** @var AccessControl */
    private AccessControl $accessControl;

    /** @var string|null  API key for this module */
    private ?string $apiKey;

    /** @var array  Static instances keyed by dirname */
    private static array $instances = [];

    /**
     * Private constructor — use getInstance().
     *
     * @param string $moduleDirname
     */
    private function __construct(string $moduleDirname)
    {
        $this->moduleDirname = $moduleDirname;
        $this->loadClasses();
        $this->accessControl = new AccessControl();
        $this->apiKey        = $this->loadApiKey();
    }

    /**
     * Get or create an SubscriptionsSDK instance for a module.
     *
     * @param string $moduleDirname
     * @return self
     */
    public static function getInstance(string $moduleDirname): self
    {
        if (!isset(self::$instances[$moduleDirname])) {
            self::$instances[$moduleDirname] = new self($moduleDirname);
        }
        return self::$instances[$moduleDirname];
    }

    // -----------------------------------------------------------------------
    // Access control
    // -----------------------------------------------------------------------

    /**
     * Check if a user has access to this module.
     *
     * @param int $userId
     * @return bool
     */
    public function hasAccess(int $userId): bool
    {
        return $this->accessControl->userHasAccess($userId, $this->moduleDirname);
    }

    /**
     * Get the access type for a user ('full', 'limited', 'metered', or null).
     *
     * @param int $userId
     * @return string|null
     */
    public function getAccessType(int $userId): ?string
    {
        return $this->accessControl->getAccessType($userId, $this->moduleDirname);
    }

    /**
     * Enforce access — redirect to plans page if denied.
     *
     * @param int $userId
     */
    public function enforceAccess(int $userId): void
    {
        $this->accessControl->enforceAccess($userId, $this->moduleDirname);
    }

    // -----------------------------------------------------------------------
    // Usage tracking
    // -----------------------------------------------------------------------

    /**
     * Record a usage event (for usage-based billing).
     *
     * @param int    $userId
     * @param string $eventType  'visit', 'download', 'api_call', etc.
     * @param float  $quantity
     * @return bool
     */
    public function recordUsage(int $userId, string $eventType = 'visit', float $quantity = 1.0): bool
    {
        return $this->accessControl->recordUsage($userId, $this->moduleDirname, $eventType, $quantity);
    }

    /**
     * Get remaining quota for a user on a 'limited' access rule.
     *
     * Returns null when:
     *  - The user has no active subscription
     *  - The access rule for this module is not 'limited'
     *  - limit_value is 0 (unlimited)
     *
     * Returns an array with:
     *  - 'limit'     int    Total quota for the billing period
     *  - 'used'      float  Units consumed so far this period
     *  - 'remaining' float  Units left (may be negative if over-limit)
     *
     * @param int    $userId
     * @param string $eventType  Optional — filter usage by event type (e.g. 'download')
     * @return array|null
     */
    public function getRemainingQuota(int $userId, string $eventType = ''): ?array
    {
        $helper  = Helper::getInstance();
        $subHandler  = $helper->getHandler('Subscription');
        $sub         = $subHandler->getActiveForUser($userId);
        if (!$sub || !$sub->isActive()) {
            return null;
        }

        // Look up the access rule for this module + plan
        $moduleHandler = $helper->getHandler('ConnectedModule');
        $module        = $moduleHandler->getByDirname($this->moduleDirname);
        if (!$module) {
            return null;
        }

        $moduleId    = (int)$module->getVar('module_id');
        $planId      = (int)$sub->getVar('plan_id');
        $ruleHandler = $helper->getHandler('ModuleAccessRule');
        $criteria    = new \CriteriaCompo();
        $criteria->add(new \Criteria('module_id', $moduleId));
        $criteria->add(new \Criteria('plan_id',   $planId));
        $rules = $ruleHandler->getAll($criteria);
        if (empty($rules)) {
            return null;
        }

        /** @var \XoopsModules\Subscriptions\ModuleAccessRule $rule */
        $rule       = reset($rules);
        $accessType = $rule->getVar('access_type', 'n');
        $limitValue = (int)$rule->getVar('limit_value');

        if ($accessType !== 'limited' || $limitValue <= 0) {
            return null;
        }

        // How much has the user consumed this billing period?
        $periodStart = (int)$sub->getVar('current_period_start');
        $periodEnd   = (int)$sub->getVar('current_period_end') ?: time();
        $logHandler  = $helper->getHandler('UsageLog');
        $subId       = (int)$sub->getVar('sub_id');
        $used        = $logHandler->getUsedQty($subId, $periodStart, $periodEnd, $eventType);

        return [
            'limit'     => $limitValue,
            'used'      => $used,
            'remaining' => max(0.0, $limitValue - $used),
        ];
    }

    // -----------------------------------------------------------------------
    // Plan / Subscription info
    // -----------------------------------------------------------------------

    /**
     * Get the active subscription for a user.
     *
     * @param int $userId
     * @return array|null  Simplified subscription data
     */
    public function getSubscription(int $userId): ?array
    {
        $helper = Helper::getInstance();
        $handler = $helper->getHandler('Subscription');
        $sub     = $handler->getActiveForUser($userId);
        if (!$sub) {
            return null;
        }
        $plan = $sub->getPlan();
        return [
            'sub_id'          => (int)$sub->getVar('sub_id'),
            'status'          => $sub->getVar('status', 'n'),
            'plan_id'         => (int)$sub->getVar('plan_id'),
            'plan_name'       => $plan ? $plan->getVar('name', 'n') : '',
            'billing_cycle'   => $sub->getVar('billing_cycle', 'n'),
            'period_start'    => (int)$sub->getVar('current_period_start'),
            'period_end'      => (int)$sub->getVar('current_period_end'),
            'auto_renew'      => (bool)(int)$sub->getVar('auto_renew'),
            'is_trial'        => $sub->getVar('status', 'n') === 'trial',
        ];
    }

    /**
     * Get all active plans (for displaying upgrade prompts).
     *
     * @return array  Array of simplified plan data
     */
    public function getPlans(): array
    {
        $helper = Helper::getInstance();
        $handler = $helper->getHandler('Plan');
        $plans   = $handler->getActivePlans();
        $result  = [];
        foreach ($plans as $plan) {
            $result[] = [
                'plan_id'       => (int)$plan->getVar('plan_id'),
                'name'          => $plan->getVar('name', 'n'),
                'price'         => (float)$plan->getVar('price', 'n'),
                'currency'      => $plan->getVar('currency', 'n'),
                'billing_cycle' => $plan->getVar('billing_cycle', 'n'),
                'is_featured'   => (bool)(int)$plan->getVar('is_featured'),
                'checkout_url'  => XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . (int)$plan->getVar('plan_id'),
            ];
        }
        return $result;
    }

    /**
     * Get plans that include access to this module.
     *
     * @return array
     */
    public function getPlansWithAccess(): array
    {
        $helper        = Helper::getInstance();
        $moduleHandler = $helper->getHandler('ConnectedModule');
        $module        = $moduleHandler->getByDirname($this->moduleDirname);
        if (!$module) {
            return [];
        }

        $moduleId    = (int)$module->getVar('module_id');
        $ruleHandler = $helper->getHandler('ModuleAccessRule');
        $rules       = $ruleHandler->getForModule($moduleId);

        $planHandler = $helper->getHandler('Plan');
        $result      = [];
        foreach ($rules as $rule) {
            $plan = $planHandler->get((int)$rule->getVar('plan_id'));
            if ($plan && (int)$plan->getVar('is_active')) {
                $result[] = [
                    'plan_id'       => (int)$plan->getVar('plan_id'),
                    'name'          => $plan->getVar('name', 'n'),
                    'price'         => (float)$plan->getVar('price', 'n'),
                    'currency'      => $plan->getVar('currency', 'n'),
                    'billing_cycle' => $plan->getVar('billing_cycle', 'n'),
                    'access_type'   => $rule->getVar('access_type', 'n'),
                    'checkout_url'  => XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . (int)$plan->getVar('plan_id'),
                ];
            }
        }
        return $result;
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Return the URL to the membership plans page.
     *
     * @return string
     */
    public function getPlansUrl(): string
    {
        return XOOPS_URL . '/modules/subscriptions/plans.php';
    }

    /**
     * Return the URL to the user subscription dashboard.
     *
     * @return string
     */
    public function getDashboardUrl(): string
    {
        return XOOPS_URL . '/modules/subscriptions/dashboard.php';
    }

    /**
     * Verify an incoming webhook request from Subscriptions.
     *
     * @param string $payload
     * @param string $signature  Value of X-Subscriptions-Signature header
     * @return bool
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $this->apiKey);
        return hash_equals($expected, $signature);
    }

    /**
     * Load Subscriptions classes
     */
    private function loadClasses(): void
    {
        // Load the PSR-4 autoloader — it handles all class/ files automatically
        $autoloader = XOOPS_ROOT_PATH . '/modules/subscriptions/preloads/autoloader.php';
        if (file_exists($autoloader)) {
            require_once $autoloader;
        }
        // Explicitly load AccessControl (used directly by this SDK)
        $ac = XOOPS_ROOT_PATH . '/modules/subscriptions/class/AccessControl.php';
        if (file_exists($ac)) {
            require_once $ac;
        }
    }

    /**
     * Load the API key for this module from DB
     *
     * @return string|null
     */
    private function loadApiKey(): ?string
    {
        $helper = Helper::getInstance();
        $handler = $helper->getHandler('ConnectedModule');
        $module  = $handler->getByDirname($this->moduleDirname);
        return $module ? $module->getVar('api_key', 'n') : null;
    }
}
