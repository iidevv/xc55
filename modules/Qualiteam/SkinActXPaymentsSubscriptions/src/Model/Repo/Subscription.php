<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo;

use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter as Converter;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XLite\Core\CommonCell;
use XLite\Model\AEntity;
use XLite\Model\Profile;
use XLite\Model\Repo\ARepo;
use XLite\View\FormField\Input\Text\DateRange;

/**
 * Subscriptions repository
 */
class Subscription extends ARepo
{
    // {{{ Search

    const SEARCH_LIMIT           = 'limit';
    const SEARCH_ORDER           = 'order';
    const SEARCH_PROFILE         = 'profile';
    const SEARCH_ID              = 'id';
    const SEARCH_CARD_ID         = 'cardId';
    const SEARCH_PRODUCT_NAME    = 'productName';
    const SEARCH_STATUS          = 'status';
    const SEARCH_DATE_RANGE      = 'dateRange';
    const SEARCH_NEXT_DATE_RANGE = 'nextDateRange';
    const SEARCH_REAL_DATE       = 'realDate';

    const SEARCH_PAY_TODAY       = 'payToday';

    const SEARCH_ORDER_BY        = 'orderBy';

    const STATUS_ANY           = '';
    const STATUS_EXPIRED       = 'E';
    const STATUS_ACTIVE_FAILED = 'AF';
    const STATUS_ACTIVE        = 'A';
    const STATUS_ACTIVE_OR_PENDING = 'AP';
    const STATUS_ACTIVE_OR_RESTARTED = 'AR';

    /**
     * Returns first active subscription by card
     *
     * @return \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription
     */
    public function findOneActiveByCardId($cardId, $includePending = false)
    {
        $cnd = new CommonCell;
        $cnd->{static::SEARCH_CARD_ID} = $cardId;
        $cnd->{static::SEARCH_STATUS} = ($includePending)
            ? static::STATUS_ACTIVE_OR_PENDING
            : ASubscriptionPlan::STATUS_ACTIVE;
        $cnd->{static::P_LIMIT} = [0, 1];

        $result = $this->search($cnd);

        return current($result);
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCardId(QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('s.card = :cardId')
                ->setParameter('cardId', $value);
        }
//Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData

    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLimit(QueryBuilder $queryBuilder, array $value)
    {
        $queryBuilder->setFrameResults($value);
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Order         $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrder(QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('XLite\Model\OrderItem', 'oi', 'WITH', 'oi.subscription = s.id')
            ->andWhere('oi.order = :order')
            ->setParameter('order', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProfile(QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('s.initialOrderItem')
            ->linkInner('initialOrderItem.order', 'o')
            ->andWhere('o.orig_profile = :profile')
            ->setParameter('profile', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndId(QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkInner('s.initialOrderItem')
                ->linkInner('initialOrderItem.order', 'initialOrder')
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        's.id = :id',
                        'initialOrder.orderNumber = :id'
                    )
                )
                ->setParameter('id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProductName(QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkInner('XLite\Model\OrderItem', 'oi')
                ->andWhere('oi.name LIKE :name')
                ->setParameter('name', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndStatus(QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            switch ($value) {
                case static::STATUS_ACTIVE_FAILED:
                    $value = ASubscriptionPlan::STATUS_ACTIVE;
                    $queryBuilder->andWhere('s.failedTries != 0');
                    break;

                case static::STATUS_EXPIRED:
                    $value = ASubscriptionPlan::STATUS_ACTIVE;
                    $queryBuilder->andWhere('s.realDate < :currentDate')
                        ->setParameter('currentDate', Converter::now());
                    break;

                default:
            }
            if (
                static::STATUS_ACTIVE_OR_PENDING == $value
                || static::STATUS_ACTIVE_OR_RESTARTED == $value
            ) {
                $cnd = new Orx();
                $cnd->add('s.status = :statusActive');
                $cnd->add('s.status = :statusRestarted');
                if (static::STATUS_ACTIVE_OR_PENDING == $value) {
                    $cnd->add('s.status = :statusNotStarted');
                }

                $queryBuilder->andWhere($cnd)
                    ->setParameter('statusActive', ASubscriptionPlan::STATUS_ACTIVE)
                    ->setParameter('statusRestarted', ASubscriptionPlan::STATUS_RESTARTED);
                if (static::STATUS_ACTIVE_OR_PENDING == $value) {
                    $queryBuilder
                        ->setParameter('statusNotStarted', ASubscriptionPlan::STATUS_NOT_STARTED);
                }

            } else {
                $queryBuilder->andWhere('s.status = :status')
                    ->setParameter('status', $value);

            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            [$start, $end] = DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('s.startDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('s.startDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndNextDateRange(QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            [$start, $end] = DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('s.realDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('s.realDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndRealDate(QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('s.realDate = :realDate')
            ->setParameter('realDate', $value);
    }

    /**
     * Find subscriptions which should be paid today
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndPayToday(QueryBuilder $queryBuilder, $value = null)
    {
        // TODO: Value is for today? Is it necesssary?
        $today = Converter::convertTimeToDayStart($value);

        // Real Date must be in the future, so if it's in past (for more than one day)
        // it means that some subscriptions were missing (skiped)
        $queryBuilder->andWhere('s.realDate <= :today')->setParameter('today', $today);

        $cnd = new Orx();
        $cnd->add('s.status = :statusActive');
        $cnd->add('s.status = :statusRestarted');
        $queryBuilder->andWhere($cnd)
            ->setParameter('statusActive', ASubscriptionPlan::STATUS_ACTIVE)
            ->setParameter('statusRestarted', ASubscriptionPlan::STATUS_RESTARTED);
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(QueryBuilder $queryBuilder, array $value)
    {
        [$sort, $order] = $this->getSortOrderValue($value);

        switch ($sort) {
            case 'profile.email':
            case 'profile.login':
                $queryBuilder->linkInner('s.initialOrderItem')
                    ->linkInner('initialOrderItem.order', 'initialOrder')
                    ->linkInner('initialOrder.profile');
                break;

            case 'initialOrderItem.name':
                $queryBuilder->linkInner('s.initialOrderItem');
                break;

            default:
                break;
        }

        $queryBuilder->addOrderBy($sort, $order);
    }

    // }}}

    /**
     * Update single entity
     *
     * @param AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(AEntity $entity, array $data = [])
    {
        parent::performUpdate($entity, $data);

        $entity->checkStatuses();
    }
}
