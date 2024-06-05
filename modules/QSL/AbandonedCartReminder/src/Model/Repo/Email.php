<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XLite\Core\CommonCell;

/**
 * Repository for Email model.
 */
class Email extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    public const SEARCH_DATE_SENT_RANGE = 'dateSentRange';
    public const SEARCH_CLICKED         = 'clicked';
    public const SEARCH_PLACED          = 'placed';
    public const SEARCH_PAID            = 'paid';

    /**
     * Returns the number of sent e-mails matching the search criteria
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countSentEmails(CommonCell $cnd)
    {
        return $this->search($cnd, static::SEARCH_MODE_COUNT);
    }

    /**
     * Returns the number of sent e-mails that were clicked
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countClickedEmails(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_CLICKED} = 1;

        return $this->countSentEmails($cnd);
    }

    /**
     * Returns the number of sent e-mails that were clicked
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countPlacedEmails(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_PLACED} = 1;

        return $this->countSentEmails($cnd);
    }

    /**
     * Returns the number of sent e-mails that were paid
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countPaidEmails(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_PAID} = 1;

        return $this->countSentEmails($cnd);
    }

    /**
     * Returns the number of orders that abandoned cart e-mails were sent for.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countSentOrders(CommonCell $cnd)
    {
        $this->clearSearchState();

        $this->searchState['currentSearchCnd']  = $cnd;
        $qb = $this->processQueryBuilder();

        $qb->select('COUNT(DISTINCT e.orderHash)')
            ->resetDQLPart('groupBy')
            ->resetDQLPart('orderBy');

        return (int)($qb->getSingleScalarResult());
    }

    /**
     * Returns the number of orders that users clicked cart recovery links for.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countClickedOrders(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_CLICKED} = 1;

        return $this->countSentOrders($cnd);
    }

    /**
     * Returns the number of orders that users placed after following cart
     * recovery links.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countPlacedOrders(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_PLACED} = 1;

        return $this->countSentOrders($cnd);
    }

    /**
     * Returns the number of paid orders that users placed after following cart
     * recovery links.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countPaidOrders(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_PAID} = 1;

        return $this->countSentOrders($cnd);
    }

    /**
     * Returns the number of users that abandoned cart e-mails were sent for.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countSentUsers(CommonCell $cnd)
    {
        $this->clearSearchState();

        $this->searchState['currentSearchCnd']  = $cnd;
        $qb = $this->processQueryBuilder();

        $qb->select('COUNT(DISTINCT e.profileHash)')
            ->resetDQLPart('groupBy')
            ->resetDQLPart('orderBy');

        return (int)($qb->getSingleScalarResult());
    }

    /**
     * Returns the number of users clicked cart recovery links.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countClickedUsers(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_CLICKED} = 1;

        return $this->countSentUsers($cnd);
    }

    /**
     * Returns the number of users placed orders after following cart recovery
     * links.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countPlacedUsers(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_PLACED} = 1;

        return $this->countSentUsers($cnd);
    }

    /**
     * Returns the number of users paid for orders placed after following
     * cart recovery links.
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return int
     */
    public function countPaidUsers(CommonCell $cnd)
    {
        $cnd = clone($cnd);
        $cnd->{self::SEARCH_PAID} = 1;

        return $this->countSentUsers($cnd);
    }

    /**
     * Deletes abandoned cart emails sent before a particular date.
     *
     * @param int $timestamp Date
     *
     * @return void
     */
    public function deletePastEmails($timestamp)
    {
        $this->prepareDeletePastEmails($timestamp)->execute();
    }

    /**
     * Prepare query builder for deletePastEmails() method
     *
     * @param int $timestamp Emails sent before this date will be deleted
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareDeletePastEmails($timestamp)
    {
        $q = $this->getQueryBuilder()
            ->delete($this->_entityName, 'e')
            ->andWhere('e.dateSent < :date')
            ->setParameter('date', (int) $timestamp);

        return $q;
    }

    /**
     * Adds a condition to leave only those e-mails which resulted into users
     * clicking cart recovery links.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param string                     $value Condition data
     *
     * @return void
     */
    protected function prepareCndClicked(QueryBuilder $qb, $value)
    {
        $qb->andWhere($value ? 'e.dateClicked > 0' : 'e.dateClicked = 0');
    }

    /**
     * Adds a condition to leave only those e-mails which resulted into users
     * placing their order after following cart recovery links.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param string                     $value Condition data
     *
     * @return void
     */
    protected function prepareCndPlaced(QueryBuilder $qb, $value)
    {
        $qb->andWhere($value ? 'e.datePlaced > 0' : 'e.datePlaced = 0');
    }

    /**
     * Adds a condition to leave only those e-mails which resulted into users
     * paying for their orders placed after following cart recovery links.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param string                     $value Condition data
     *
     * @return void
     */
    protected function prepareCndPaid(QueryBuilder $qb, $value)
    {
        $qb->andWhere($value ? 'e.datePaid > 0' : 'e.datePaid = 0');
    }

    /**
     * Search carts having date withing the date range (from the Date Range widget).
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param string                     $value Condition data
     *
     * @return void
     */
    protected function prepareCndDateSentRange(QueryBuilder $qb, $value)
    {
        $this->prepareDateRangeCondition($qb, $value, 'dateSent');
    }

    /**
     * Search emails within the date range (from the Date Range widget).
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param string                     $value Condition data
     * @param string                     $field Name of the date field OPTIONAL
     *
     * @return void
     */
    protected function prepareDateRangeCondition(QueryBuilder $qb, $value, $field = 'date')
    {
        if (!empty($value)) {
            $this->prepareDateCondition(
                $qb,
                \XLite\View\FormField\Input\Text\DateRange::convertToArray($value),
                $field
            );
        }
    }

    /**
     * Search emails within the date range (specified as an array).
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data OPTIONAL
     * @param string                     $field        Name of the date field OPTIONAL
     *
     * @return void
     */
    protected function prepareDateCondition(QueryBuilder $qb, $value = null, $field = 'dateSent')
    {
        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : intval($value[0]);
            $end = empty($value[1]) ? null : intval($value[1]);

            if ($start) {
                $qb->andWhere("e.$field >= :{$field}_start")
                    ->setParameter("{$field}_start", $start);
            }

            if ($end) {
                $qb->andWhere("e.$field <= :{$field}_end")
                    ->setParameter("{$field}_end", $end);
            }
        }
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    public function findSentReminderIdsByOrder(\XLite\Model\Cart $cart)
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () use ($cart) {
            $qb    = $this->createQueryBuilder();
            $alias = $this->getMainAlias($qb);

            $qb->select("{$alias}.reminderId")
               ->where("{$alias}.order = :order")
               ->setParameter('order', $cart)
               ->groupBy("{$alias}.reminderId");

            return array_map(static fn ($item) => array_shift($item), $qb->getResult());
        }, ['findSentReminderIdsByOrder', $cart]);
    }
}
