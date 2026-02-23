<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Plan class
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * class PlanFeatureHandler
 */
class PlanFeatureHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_plan_features', PlanFeature::class, 'feature_id', 'feature');
    }

    /**
     * Save all features for a plan (replace)
     *
     * @param int   $planId
     * @param array $features  Array of feature strings
     * @return bool
     */
    public function saveForPlan(int $planId, array $features): bool
    {
        $helper = Helper::getInstance();
        // Delete existing
        $criteria = new \Criteria('plan_id', $planId);
        $this->deleteAll($criteria);
        // Insert new
        foreach (array_values($features) as $i => $featureTxt) {
            $featureTxt = trim((string)$featureTxt);
            if ($featureTxt === '') {
                continue;
            }
            /** @var PlanFeature $obj */
            $obj = $this->create();
            $obj->setVar('plan_id',    $planId);
            $obj->setVar('feature',    $featureTxt);
            $obj->setVar('sort_order', $i);
            if (!$this->insert($obj)) {
                return false;
            }
        }
        return true;
    }
}


