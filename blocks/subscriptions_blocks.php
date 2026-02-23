<?php

declare(strict_types=1);

use XoopsModules\Subscriptions\{Helper, Utility};

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Display featured membership plans block.
 *
 * @param array $options  [0] => max plans, [1] => featured only flag
 */
function b_subscriptions_plans_show(array $options): array
{
    require_once XOOPS_ROOT_PATH . '/modules/subscriptions/preloads/autoloader.php';

    $maxPlans     = max(1, (int)($options[0] ?? 5));
    $featuredOnly = (bool)(int)($options[1] ?? 1);

    $helper   = Helper::getInstance();
    $handler  = $helper->getHandler('Plan');

    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('is_active', 1));
    if ($featuredOnly) {
        $criteria->add(new \Criteria('is_featured', 1));
    }
    $criteria->setSort('sort_order');
    $criteria->setOrder('ASC');
    $criteria->setLimit($maxPlans);

    $block = [];
    foreach ($handler->getAll($criteria) as $plan) {
        $block['plans'][] = [
            'plan_id'       => (int)$plan->getVar('plan_id'),
            'name'          => $plan->getVar('name', 'n'),
            'price'         => Utility::formatMoney((float)$plan->getVar('price'), $plan->getVar('currency', 'n')),
            'billing_cycle' => $plan->getVar('billing_cycle', 'n'),
            'trial_days'    => (int)$plan->getVar('trial_days'),
            'checkout_url'  => XOOPS_URL . '/modules/subscriptions/checkout.php?plan=' . (int)$plan->getVar('plan_id'),
        ];
    }
    $block['plans_url'] = XOOPS_URL . '/modules/subscriptions/plans.php';

    return $block;
}

/**
 * Edit block options form.
 *
 * @param array $options
 */
function b_subscriptions_plans_edit(array $options): string
{
    $max      = (int)($options[0] ?? 5);
    $featured = !empty($options[1]) ? ' checked' : '';

    return '<div>'
        . '<label>Max plans to show: <input type="number" name="options[0]" value="' . $max . '" min="1" max="20" size="4"></label>'
        . '<br>'
        . '<label>Featured only: <input type="checkbox" name="options[1]" value="1"' . $featured . '></label>'
        . '</div>';
}
