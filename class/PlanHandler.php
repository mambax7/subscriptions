<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use CriteriaCompo;
use XoopsDatabase;
use XoopsPersistableObjectHandler;

/**
 * Subscriptions Plan class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class PlanHandler.
 */
class PlanHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_plans', Plan::class, 'plan_id', 'name');
    }

    /**
     * Returns all active plans ordered by sort_order.
     *
     * @return Plan[]
     */
    public function getActivePlans(): array
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('is_active', 1));
        $criteria->setSort('sort_order');
        $criteria->setOrder('ASC');

        return $this->getAll($criteria);
    }

    /**
     * Find plan by slug.
     *
     * @param string $slug
     *
     * @return Plan|null
     */
    public function getBySlug(string $slug): ?Plan
    {
        $criteria = new Criteria('slug', $slug, '=');
        $plans = $this->getAll($criteria);

        return ! empty($plans) ? reset($plans) : null;
    }

    /**
     * Generate unique slug from name.
     *
     * @param string $name
     *
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));
        $slug = trim($slug, '-');
        $base = $slug;
        $i = 1;
        while ($this->getBySlug($slug) !== null) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
