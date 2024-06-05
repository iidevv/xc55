<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Model\Repo\Cart
{
    /**
     * Allowable search params
     */
    public const SEARCH_DATE_RANGE            = 'dateRange';
    public const SEARCH_DATE                  = 'date';
    public const SEARCH_SUBSTRING             = 'substring';
    public const SEARCH_ABANDONED_ONLY        = 'abandonedOnly';
    public const SEARCH_WITH_ITEMS            = 'withItems';
    public const SEARCH_WITH_PROFILES         = 'withProfiles';
    public const SEARCH_LOST_CARTS            = 'lostCarts';
    public const SEARCH_PROFILE               = 'profile';
    public const SEARCH_SKIP_REMINDED         = 'skipReminded';
    public const SEARCH_LAST_VISIT_DATE_RANGE = 'lastVisitDateRange';
    public const SEARCH_LAST_VISIT_DATE       = 'lastVisitDate';

    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_DATE            = 'c.date';
    public const SORT_BY_REMINDER_DATE        = 'c.cart_reminder_date';
    public const SORT_BY_MODE_SUBTOTAL        = 'c.subtotal';
    public const SORT_BY_MODE_LAST_VISIT_DATE = 'c.lastVisitDate';

    /**
     * Add conditions to retrieve abandoned carts.
     *
     * @param \XLite\Core\CommonCell $cnd Current conditions.
     *
     * @return \XLite\Core\CommonCell
     */
    public function addConditionSearchAbandoned(\XLite\Core\CommonCell $cnd)
    {
        $cnd->{static::SEARCH_ABANDONED_ONLY} = 1;
        $cnd->{static::SEARCH_WITH_PROFILES} = 1;
        $cnd->{static::SEARCH_WITH_ITEMS} = 1;
        $cnd->{static::SEARCH_LOST_CARTS} = 0; // Skip carts marked as lost
        $cnd->{static::P_ORDER_BY} = [static::SORT_BY_MODE_LAST_VISIT_DATE, 'DESC'];

        return $cnd;
    }

    /**
     * Search carts with non-empty profiles (registered or guest).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndWithProfiles(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            // Hide carts from "anonymous" customers
            $queryBuilder->linkInner('c.profile')
                ->andWhere('profile.login <> :empty_login ')
                ->setParameter('empty_login', '');
        }
    }

    /**
     * Search carts with or without items.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    Whether it is the COUNT request, or SELECT
     *
     * @return void
     */
    protected function prepareCndWithItems(QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            // Show/hide empty carts
            $queryBuilder = $this->linkWithItems($queryBuilder, $value);

            // Remove duplicate order records provided it is not SELECT COUNT(DISTINCT()) query
            if (!$countOnly) {
                $queryBuilder->groupBy('c.order_id');
            }
        }
    }

    /**
     * Links order items to the query.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function linkWithItems(QueryBuilder $queryBuilder, $value)
    {
        return $queryBuilder->linkInner('c.items')
                ->andWhere('items.item_id ' . ($value ? 'IS NOT NULL' : 'IS NULL'));
    }

    /**
     * Search abandoned carts only.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndAbandonedOnly(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $date = (\XLite\Core\Converter::time() - \XLite\Model\Order::getAbandonmentTime());
            $queryBuilder->andWhere('c.lastVisitDate <= :max_abandoned_cart_date')
                ->setParameter('max_abandoned_cart_date', $date);
        }
    }

    /**
     * Search carts marked/not marked as "lost".
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLostCarts(QueryBuilder $queryBuilder, $value)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 'c.lost <> 0' : 'c.lost = 0');
        }
    }

    /**
     * Search carts having date withing the date range (from the Date Range widget).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(QueryBuilder $queryBuilder, $value)
    {
        $this->prepareDateRangeCondition($queryBuilder, $value, 'date');
    }

    /**
     * Search carts visited withing the date range (from the Date Range widget).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLastVisitDateRange(QueryBuilder $queryBuilder, $value)
    {
        $this->prepareDateRangeCondition($queryBuilder, $value, 'lastVisitDate');
    }

    /**
     * Search carts withing the date range (from the Date Range widget).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     * @param string                     $field        Name of the date field OPTIONAL
     *
     * @return void
     */
    protected function prepareDateRangeCondition(QueryBuilder $queryBuilder, $value, $field = 'date')
    {
        if (!empty($value)) {
            $this->prepareDateCondition(
                $queryBuilder,
                \XLite\View\FormField\Input\Text\DateRange::convertToArray($value),
                $field
            );
        }
    }

    /**
     * Search carts having date withing the date range (specified as an array).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data OPTIONAL
     *
     * @return void
     */
    protected function prepareCndDate(QueryBuilder $queryBuilder, array $value = null)
    {
        $this->prepareDateCondition($queryBuilder, $value, 'date');
    }

    /**
     * Search carts having date withing the date range (specified as an array).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data OPTIONAL
     *
     * @return void
     */
    protected function prepareCndLastVisitDate(QueryBuilder $queryBuilder, array $value = null)
    {
        $this->prepareDateCondition($queryBuilder, $value, 'lastVisitDate');
    }

    /**
     * Search carts withing the date range (specified as an array).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data OPTIONAL
     * @param string                     $field        Name of the date field OPTIONAL
     *
     * @return void
     */
    protected function prepareDateCondition(QueryBuilder $queryBuilder, $value = null, $field = 'date')
    {
        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : intval($value[0]);
            $end = empty($value[1]) ? null : intval($value[1]);

            if ($start) {
                $queryBuilder->andWhere("c.$field >= :{$field}_start")
                    ->setParameter("{$field}_start", $start);
            }

            if ($end) {
                $queryBuilder->andWhere("c.$field <= :{$field}_end")
                    ->setParameter("{$field}_end", $end);
            }
        }
    }

    /**
     * Skip carts having the last reminder sent.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        The last reminder delays (in seconds) OPTIONAL
     *
     * @return void
     */
    protected function prepareCndSkipReminded(QueryBuilder $queryBuilder, $value = 0)
    {
        if (is_numeric($value)) {
            $queryBuilder->andWhere('c.cart_reminder_date > (c.date + :last_reminder_delay)')
                ->setParameter('last_reminder_delay', $value);
        }
    }

    /**
     * Search carts by the customer email substring.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere('profile.login LIKE :substring')
                ->setParameter('substring', '%' . $value . '%');
        }
    }

    /**
     * Search carts of the customer
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Customer profile
     *
     * @return void
     */
    protected function prepareCndProfile(QueryBuilder $queryBuilder, \XLite\Model\Profile $value)
    {
        if (!empty($value)) {
            $queryBuilder->innerJoin('c.profile', 'p')->andWhere('p.profile_id = :profile_id')
                ->setParameter('profile_id', $value->getProfileId());
        }
    }
}
