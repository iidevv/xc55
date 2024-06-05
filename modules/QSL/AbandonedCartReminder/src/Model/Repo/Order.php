<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * Extra search parameters
     */
    public const SEARCH_RECOVERED  = 'recovered';

    /**
     * Filters an array of order statuses and leaves allowed statuses only.
     *
     * @param array $statuses The array of statuses to filter
     *
     * @return array
     */
    public function filterAllowedStatuses($statuses)
    {
        $allowedStatuses = \XLite\Model\Order::getAllowedStatuses();

        foreach ($statuses as $k => $status) {
            if (!isset($allowedStatuses[$status])) {
                unset($statuses[$k]);
            }
        }

        return $statuses;
    }

    /**
     * Add conditions to retrieve recovered orders.
     *
     * @param \XLite\Core\CommonCell $cnd Current conditions.
     *
     * @return \XLite\Core\CommonCell
     */
    public function addConditionSearchRecovered(\XLite\Core\CommonCell $cnd)
    {
        $cnd->{static::SEARCH_RECOVERED} = 1;
        $cnd->{static::P_ORDER_BY} = ['o.date', 'DESC'];
        $cnd->{static::P_PAYMENT_STATUS} = \XLite\Model\Order\Status\Payment::getPaidStatuses();

        return $cnd;
    }

    /**
     * Return sum of order totals.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Whether to count resulting records, or return the records themselves OPTIONAL
     *
     * @return float
     */
    public function sum(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->innerJoin('o.profile', 'p')
            ->leftJoin('o.orig_profile', 'op');

        foreach ($cnd as $key => $value) {
            if ($key != self::P_LIMIT) {
                $this->callSearchConditionHandler($value, $key, $queryBuilder);
            }
        }

        // We remove all order-by clauses since it is not used for count-only mode
        $queryBuilder->select('SUM(o.total)')->orderBy('o.order_id');
        $result = (float)$queryBuilder->getSingleScalarResult();

        return $result;
    }

    /**
     * Search recovered carts.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndRecovered(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            // Show/hide recovered orders
            $condition = $value ? 'o.recovered > 0' : 'o.recovered = 0';
            $queryBuilder->andWhere($condition);
        }
    }
}
