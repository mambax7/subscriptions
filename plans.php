<?php
/**
 * Subscriptions - Plans listing page
 *
 * @package subscriptions
 */

use XoopsModules\Subscriptions\{
    Helper,
    PlanHandler,
    Utility
};

$GLOBALS['xoopsOption']['template_main'] = 'subscriptions_plans.tpl';

require_once __DIR__ . '/header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/common.php';

$helper = Helper::getInstance();
/** @var PlanHandler $planHandler */
$planHandler = $helper->getHandler('Plan');
$plans       = $planHandler->getActivePlans();
$planData    = [];

foreach ($plans as $plan) {
    $features = $plan->getFeatures();
    $featList = [];
    foreach ($features as $f) {
        $featList[] = $f->getVar('feature', 'n');
    }
    $annualPrice    = $plan->getAnnualPrice();
    $trialDays      = (int)$plan->getVar('trial_days');
    $discountAnnual = (float)$plan->getVar('discount_annual');
    $planData[]  = [
        'plan_id'            => (int)$plan->getVar('plan_id'),
        'name'               => $plan->getVar('name', 'n'),
        'description'        => $plan->getVar('description', 'n'),
        'price'              => Utility::formatMoney((float)$plan->getVar('price'), $plan->getVar('currency', 'n')),
        'price_raw'          => (float)$plan->getVar('price'),
        'annual_price'       => Utility::formatMoney($annualPrice, $plan->getVar('currency', 'n')),
        'discount_annual'    => $discountAnnual,
        'annual_save_badge'  => $discountAnnual > 0 ? sprintf(_MD_SUBSCRIPTIONS_ANNUAL_SAVE, $discountAnnual) : '',
        'billing_cycle'      => $plan->getBillingCycleLabel(),
        'trial_days'         => $trialDays,
        'trial_badge'        => $trialDays > 0 ? sprintf(_MD_SUBSCRIPTIONS_TRIAL_BADGE, $trialDays) : '',
        'setup_fee'          => (float)$plan->getVar('setup_fee'),
        'setup_fee_display'  => (float)$plan->getVar('setup_fee') > 0
            ? Utility::formatMoney((float)$plan->getVar('setup_fee'), $plan->getVar('currency', 'n'))
            : '',
        'pricing_model'      => $plan->getVar('pricing_model', 'n'),
        'usage_unit'         => $plan->getVar('usage_unit', 'n'),
        'usage_price'        => Utility::formatMoney((float)$plan->getVar('usage_price', 'n'), $plan->getVar('currency', 'n')),
        'is_featured'        => (bool)(int)$plan->getVar('is_featured'),
        'features'           => $featList,
        'checkout_url'       => XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . (int)$plan->getVar('plan_id'),
    ];
}

// Pass the module that requires this subscription (for targeted upsell)
$requiredModule = isset($_GET['required_module'])
    ? htmlspecialchars(trim($_GET['required_module']), ENT_QUOTES)
    : '';

$xoopsTpl->assign('xm_plans',           $planData);
$xoopsTpl->assign('xm_required_module', $requiredModule);
$xoopsTpl->assign('xm_is_logged_in',    is_object($GLOBALS['xoopsUser'] ?? null));

require XOOPS_ROOT_PATH . '/footer.php';
