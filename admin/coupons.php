<?php

declare(strict_types=1);
/**
 * Subscriptions Admin Coupons management.
 */

use XoopsModules\Subscriptions\Helper;
use XoopsModules\Subscriptions\Utility;

$op = isset($_REQUEST['op']) ? htmlspecialchars(trim($_REQUEST['op']), ENT_QUOTES) : 'list';

// Determine template BEFORE cp_header is included
$GLOBALS['xoopsOption']['template_main'] = ($op === 'edit')
    ? 'subscriptions_admin_coupon_form.tpl'
    : 'subscriptions_admin_coupons.tpl';

require_once __DIR__ . '/admin_header.php';
defined('XOOPS_ROOT_PATH') || die('Restricted access');
xoops_cp_header();
Utility::addAdminAssets();

$helper = Helper::getInstance();
$helper->loadLanguage('admin');

/** @var CouponHandler $handler */
$handler = $helper->getHandler('Coupon');

switch ($op) {
    case 'save':
        if (! Utility::verifyToken($_POST['token'] ?? '', 'admin_coupons')) {
            redirect_header('coupons.php', 2, _NOPERM);
        }
        $couponId = (int) ($_POST['coupon_id'] ?? 0);
        /** @var Coupon $coupon */
        $coupon = $couponId > 0 ? $handler->get($couponId) : $handler->create();
        if (! $coupon) {
            redirect_header('coupons.php', 2, _AM_SUBSCRIPTIONS_COUPON_NOT_FOUND);
        }

        $planIds = [];
        if (! empty($_POST['plan_ids']) && is_array($_POST['plan_ids'])) {
            $planIds = array_map('intval', $_POST['plan_ids']);
        }
        $appliesTo = empty($planIds) ? 'all' : 'plan_ids';

        $coupon->setVar('code', strtoupper(trim($_POST['code'] ?? '')));
        $coupon->setVar('name', trim($_POST['name'] ?? ''));
        $coupon->setVar('discount_type', $_POST['discount_type'] ?? 'percentage');
        $coupon->setVar('discount_value', (float) ($_POST['discount_value'] ?? 0));
        $coupon->setVar('min_amount', (float) ($_POST['min_amount'] ?? 0));
        $coupon->setVar('max_uses', (int) ($_POST['max_uses'] ?? 0));
        $coupon->setVar('max_uses_per_user', (int) ($_POST['max_uses_per_user'] ?? 1));
        $coupon->setVar('recurring_months', (int) ($_POST['recurring_months'] ?? 0));
        $coupon->setVar('applies_to', $appliesTo);
        $coupon->setVar('plan_ids', json_encode($planIds));
        $coupon->setVar('valid_from', $_POST['valid_from'] ? strtotime($_POST['valid_from']) : 0);
        $coupon->setVar('valid_until', $_POST['valid_until'] ? strtotime($_POST['valid_until']) : 0);
        $coupon->setVar('is_active', (int) ($_POST['is_active'] ?? 1));

        if ($couponId === 0) {
            $coupon->setVar('created_at', time());
        }

        if ($handler->insert($coupon)) {
            redirect_header('coupons.php', 2, _AM_SUBSCRIPTIONS_COUPON_SAVED);
        }
        redirect_header('coupons.php', 2, _AM_SUBSCRIPTIONS_SAVE_ERROR);

        break;
    case 'delete':
        $couponId = (int) ($_REQUEST['coupon_id'] ?? 0);
        if ($couponId > 0 && Utility::verifyToken($_REQUEST['token'] ?? '', 'admin_coupon_del_' . $couponId)) {
            $handler->delete($handler->get($couponId), true);
        }
        redirect_header('coupons.php', 2, _AM_SUBSCRIPTIONS_COUPON_DELETED);

        break;
    case 'edit':
        $couponId = (int) ($_REQUEST['coupon_id'] ?? 0);
        $coupon = $couponId > 0 ? $handler->get($couponId) : $handler->create();
        if (! $coupon) {
            redirect_header('coupons.php', 2, _AM_SUBSCRIPTIONS_COUPON_NOT_FOUND);
        }
        $planHandler = $helper->getHandler('Plan');
        $allPlans = $planHandler->getActivePlans();

        $planList = [];
        foreach ($allPlans as $p) {
            $planList[] = [
                'plan_id' => (int) $p->getVar('plan_id'),
                'name'    => $p->getVar('name', 'n'),
            ];
        }

        $xoopsTpl->assign('xm_coupon', [
            'coupon_id'         => (int) $coupon->getVar('coupon_id'),
            'code'              => $coupon->getVar('code', 'n'),
            'name'              => $coupon->getVar('name', 'n'),
            'discount_type'     => $coupon->getVar('discount_type', 'n'),
            'discount_value'    => $coupon->getVar('discount_value', 'n'),
            'min_amount'        => $coupon->getVar('min_amount', 'n'),
            'max_uses'          => (int) $coupon->getVar('max_uses'),
            'uses_count'        => (int) $coupon->getVar('uses_count'),
            'max_uses_per_user' => (int) $coupon->getVar('max_uses_per_user'),
            'recurring_months'  => (int) $coupon->getVar('recurring_months'),
            'applies_to'        => $coupon->getVar('applies_to', 'n'),
            'valid_from'        => (int) $coupon->getVar('valid_from'),
            'valid_until'       => (int) $coupon->getVar('valid_until'),
            'is_active'         => (int) $coupon->getVar('is_active'),
        ]);
        $xoopsTpl->assign('xm_plans', $planList);
        $xoopsTpl->assign('xm_selected_plan_ids', $coupon->getPlanIds());
        $xoopsTpl->assign('xm_token', Utility::generateToken('admin_coupons'));
        $xoopsTpl->assign('xm_is_edit', $couponId > 0);

        break;
    default:
        $coupons = $handler->getAll();
        $couponData = [];
        foreach ($coupons as $c) {
            $couponData[] = array_merge(Utility::objVars($c), [
                'del_token' => Utility::generateToken('admin_coupon_del_' . $c->getVar('coupon_id')),
            ]);
        }
        $xoopsTpl->assign('xm_coupons', $couponData);
        $xoopsTpl->assign('xm_admin_nav', Utility::adminNav());

        break;
}

require __DIR__ . '/admin_footer.php';
