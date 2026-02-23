<?php

declare(strict_types=1);
/**
 * Subscriptions Admin Connected Modules management.
 */

use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Utility;

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';

// Determine template BEFORE cp_header is included
$GLOBALS['xoopsOption']['template_main'] = ($op === 'edit')
    ? 'subscriptions_admin_module_form.tpl'
    : 'subscriptions_admin_modules.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');

/** @var ConnectedModuleHandler $modHandler */
$modHandler = $helper->getHandler('ConnectedModule');
/** @var ModuleAccessRuleHandler $ruleHandler */
$ruleHandler = $helper->getHandler('ModuleAccessRule');
/** @var PlanHandler $planHandler */
$planHandler = $helper->getHandler('Plan');

switch ($op) {
    case 'save':
        if (! Utility::verifyToken($_POST['token'] ?? '', 'admin_modules')) {
            redirect_header('modules.php', 2, _NOPERM);
        }
        $moduleId = (int) ($_POST['module_id'] ?? 0);
        /** @var ConnectedModule $mod */
        $mod = $moduleId > 0 ? $modHandler->get($moduleId) : $modHandler->create();
        if (! $mod) {
            redirect_header('modules.php', 2, _AM_SUBSCRIPTIONS_MODULE_NOT_FOUND);
        }
        $mod->setVar('dirname', trim($_POST['dirname'] ?? ''));
        $mod->setVar('name', trim($_POST['name'] ?? ''));
        $mod->setVar('description', trim($_POST['description'] ?? ''));
        $mod->setVar('webhook_url', trim($_POST['webhook_url'] ?? ''));
        $mod->setVar('is_active', (int) ($_POST['is_active'] ?? 1));
        if ($moduleId === 0) {
            $mod->setVar('api_key', $modHandler->generateApiKey());
            $mod->setVar('created_at', time());
        }
        if ($modHandler->insert($mod)) {
            $newModuleId = $moduleId > 0 ? $moduleId : (int) $mod->getVar('module_id');
            // Save access rules
            $ruleHandler->deleteAll(new Criteria('module_id', $newModuleId));
            $rulePlanIds = array_map('intval', (array) ($_POST['rule_plan_ids'] ?? []));
            foreach ($rulePlanIds as $planId) {
                if ($planId <= 0) {
                    continue;
                }
                /** @var ModuleAccessRule $rule */
                $rule = $ruleHandler->create();
                $rule->setVar('module_id', $newModuleId);
                $rule->setVar('plan_id', $planId);
                $rule->setVar('access_type', $_POST['access_type_' . $planId] ?? 'full');
                $rule->setVar('limit_value', (int) ($_POST['limit_value_' . $planId] ?? 0));
                $rule->setVar('xoops_group', (int) ($_POST['xoops_group_' . $planId] ?? 0));
                $ruleHandler->insert($rule);
            }
            redirect_header('modules.php', 2, _AM_SUBSCRIPTIONS_MODULE_SAVED);
        }
        redirect_header('modules.php', 2, _AM_SUBSCRIPTIONS_SAVE_ERROR);

        break;
    case 'delete':
        $moduleId = (int) ($_REQUEST['module_id'] ?? 0);
        if ($moduleId > 0 && Utility::verifyToken($_REQUEST['token'] ?? '', 'admin_module_del_' . $moduleId)) {
            $modHandler->delete($modHandler->get($moduleId), true);
            $ruleHandler->deleteAll(new Criteria('module_id', $moduleId));
        }
        redirect_header('modules.php', 2, _AM_SUBSCRIPTIONS_MODULE_DELETED);

        break;
    case 'edit':
        $moduleId = (int) ($_REQUEST['module_id'] ?? 0);
        $mod = $moduleId > 0 ? $modHandler->get($moduleId) : $modHandler->create();
        if (! $mod) {
            redirect_header('modules.php', 2, _AM_SUBSCRIPTIONS_MODULE_NOT_FOUND);
        }
        $existingRules = $moduleId > 0 ? $ruleHandler->getForModule($moduleId) : [];
        $rulesByPlan = [];
        foreach ($existingRules as $r) {
            $rulesByPlan[(int) $r->getVar('plan_id')] = Utility::objVars($r);
        }
        $allPlans = $planHandler->getActivePlans();

        $planList = [];
        foreach ($allPlans as $p) {
            $planList[] = [
                'plan_id' => (int) $p->getVar('plan_id'),
                'name'    => $p->getVar('name', 'n'),
            ];
        }

        $xoopsTpl->assign('xm_module', Utility::objVars($mod));
        $xoopsTpl->assign('xm_plans', $planList);
        $xoopsTpl->assign('xm_rules', $rulesByPlan);
        $xoopsTpl->assign('xm_token', Utility::generateToken('admin_modules'));
        $xoopsTpl->assign('xm_is_edit', $moduleId > 0);

        break;
    default:
        $modules = $modHandler->getAll();
        $moduleData = [];
        foreach ($modules as $m) {
            $moduleData[] = array_merge(Utility::objVars($m), [
                'rule_count' => $ruleHandler->getCount(new Criteria('module_id', $m->getVar('module_id'))),
                'del_token'  => Utility::generateToken('admin_module_del_' . $m->getVar('module_id')),
            ]);
        }
        $xoopsTpl->assign('xm_modules', $moduleData);
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
}

require __DIR__ . '/admin_footer.php';
