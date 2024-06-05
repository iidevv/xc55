<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Repo\Order
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndRecent(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        parent::prepareCndRecent($queryBuilder, $value);

        if ($value) {
            $alias = 'EgoodsShippingStatusAlias';

            $queryBuilder->innerJoin('o.shippingStatus', $alias);
            $this->assignRecentCondition($queryBuilder, $alias);
            $queryBuilder->setParameter('shippingStatus', \XLite\Model\Order\Status\Shipping::STATUS_WAITING_FOR_APPROVE);
        }
    }

    /**
     * Assign recent search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string $alias
     */
    protected function assignRecentCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias)
    {
        $queryBuilder->orWhere($queryBuilder->expr()->andX(
            $alias . '.code = :shippingStatus',
            'o.orderNumber IS NOT NULL'
        ));
    }

    /**
     * Find all orders bu profile WithEgoods
     *
     * @param \XLite\Model\Profile $profile NOT OPTIONAL (default value is deprecated)
     *
     * @param bool                 $availableOnly
     *
     * @return array
     */
    public function findAllOrdersWithEgoods(\XLite\Model\Profile $profile = null, $availableOnly = true)
    {
        $list = [];

        if ($profile) {
            foreach ($this->defineFindAllOrdersWithEgoodsQuery($profile)->getResult() as $order) {
                if ($order->getDownloadAttachments($availableOnly)) {
                    $list[] = $order;
                }
            }
        }

        return $list;
    }

    /**
     * Define query for findAllOrdersWithEgoods() method
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAllOrdersWithEgoodsQuery(\XLite\Model\Profile $profile = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('o.items', 'item')
            ->innerJoin('item.privateAttachments', 'pa')
            ->leftJoin('o.orig_profile', 'op')
            ->orderBy('o.date', 'desc');

        if ($profile) {
            $qb->andWhere('op.profile_id = :opid')
                ->setParameter('opid', $profile->getProfileId());
        }

        return $qb;
    }

    /**
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return boolean
     */
    public function isAnyOrderWithEgoods(\XLite\Model\Profile $profile = null)
    {
        return (bool) $this->defineFindAllOrdersWithEgoodsQuery($profile)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->count();
    }
}
