<?php

declare(strict_types=1);
/**
 * Subscriptions Admin Plans management.
 */

use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\PlanHandler;
use XoopsModules\Subscriptions\Utility;

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';

// Determine template BEFORE cp_header is included
$GLOBALS['xoopsOption']['template_main'] = ($op === 'edit')
    ? 'subscriptions_admin_plan_form.tpl'
    : 'subscriptions_admin_plans.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');

/** @var PlanHandler $planHandler */
$planHandler = $helper->getHandler('Plan');
$featureHandler = $helper->getHandler('PlanFeature');

switch ($op) {
    // ---- Save (insert or update) ----------------------------------------
    case 'save':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || ! Utility::verifyToken($_POST['token'] ?? '', 'admin_plans')
        ) {
            redirect_header('plans.php', 2, _NOPERM);
        }

        $planId = (int) ($_POST['plan_id'] ?? 0);
        /** @var Plan $plan */
        $plan = $planId > 0 ? $planHandler->get($planId) : $planHandler->create();
        if (! $plan) {
            redirect_header('plans.php', 2, _AM_SUBSCRIPTIONS_PLAN_NOT_FOUND);
        }

        $plan->setVar('name', trim($_POST['name'] ?? ''));
        $plan->setVar('description', trim($_POST['description'] ?? ''));
        $plan->setVar('price', (float) ($_POST['price'] ?? 0));
        $plan->setVar('currency', $_POST['currency'] ?? 'USD');
        $plan->setVar('billing_cycle', $_POST['billing_cycle'] ?? 'monthly');
        $plan->setVar('trial_days', (int) ($_POST['trial_days'] ?? 0));
        $plan->setVar('setup_fee', (float) ($_POST['setup_fee'] ?? 0));
        $plan->setVar('discount_annual', (float) ($_POST['discount_annual'] ?? 0));
        $plan->setVar('pricing_model', $_POST['pricing_model'] ?? 'flat');
        $plan->setVar('usage_unit', trim($_POST['usage_unit'] ?? ''));
        $plan->setVar('usage_price', (float) ($_POST['usage_price'] ?? 0));
        $plan->setVar('is_featured', (int) ($_POST['is_featured'] ?? 0));
        $plan->setVar('is_active', (int) ($_POST['is_active'] ?? 1));
        $plan->setVar('sort_order', (int) ($_POST['sort_order'] ?? 0));
        $plan->setVar('max_users', (int) ($_POST['max_users'] ?? 0));
        $plan->setVar('updated_at', time());

        if ($planId === 0) {
            $plan->setVar('slug', $planHandler->generateSlug(trim($_POST['name'] ?? '')));
            $plan->setVar('created_at', time());
        }

        if ($planHandler->insert($plan)) {
            $newId = $planId > 0 ? $planId : $plan->getVar('plan_id');
            $features = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
            $featureHandler->saveForPlan((int) $newId, $features);
            redirect_header('plans.php', 2, _AM_SUBSCRIPTIONS_PLAN_SAVED);
        }
        redirect_header('plans.php', 2, _AM_SUBSCRIPTIONS_SAVE_ERROR);

        break;
        // ---- Delete ---------------------------------------------------------
    case 'delete':
        $planId = (int) ($_REQUEST['plan_id'] ?? 0);
        if ($planId > 0 && Utility::verifyToken($_REQUEST['token'] ?? '', 'admin_plan_del_' . $planId)) {
            $planHandler->delete($planHandler->get($planId), true);
            $featureHandler->deleteAll(new Criteria('plan_id', $planId));
        }
        redirect_header('plans.php', 2, _AM_SUBSCRIPTIONS_PLAN_DELETED);

        break;
        // ---- Edit form -------------------------------------------------------
    case 'edit':
        $planId = (int) ($_REQUEST['plan_id'] ?? 0);
        $plan = $planId > 0 ? $planHandler->get($planId) : $planHandler->create();
        if (! $plan) {
            redirect_header('plans.php', 2, _AM_SUBSCRIPTIONS_PLAN_NOT_FOUND);
        }
        $features = $plan->getVar('plan_id') ? $plan->getFeatures() : [];
        $featLines = [];
        foreach ($features as $f) {
            $featLines[] = $f->getVar('feature', 'n');
        }
        $xoopsTpl->assign('xm_plan', Utility::objVars($plan));
        $xoopsTpl->assign('xm_features_text', implode("\n", $featLines));
        $xoopsTpl->assign('xm_token', Utility::generateToken('admin_plans'));
        $xoopsTpl->assign('xm_is_edit', $planId > 0);
        $xoopsTpl->assign('xm_form_action', 'plans.php?op=save');
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
        // ---- Default: list ---------------------------------------------------
    default:
        $plans = $planHandler->getAll();
        $planData = [];
        foreach ($plans as $p) {
            $row = Utility::objVars($p);
            $row['del_token'] = Utility::generateToken('admin_plan_del_' . (int) $p->getVar('plan_id'));
            $planData[] = $row;
        }
        $xoopsTpl->assign('xm_plans', $planData);
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
}

require __DIR__ . '/admin_footer.php';
