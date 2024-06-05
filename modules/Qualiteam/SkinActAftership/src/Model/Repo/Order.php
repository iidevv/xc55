<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model\Repo;

use Doctrine\ORM\Query\Expr\Join;
use XCart\Extender\Mapping\Extender;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * Find ordered shipping method couriers
     *
     * @param string|null $orderNumber
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOrderShippingMethodCouriers(?string $orderNumber): mixed
    {
        return $this->prepareFindOrderShippingMethodCouriers($orderNumber)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param string|null $orderNumber
     *
     * @return AQueryBuilder|null
     */
    protected function prepareFindOrderShippingMethodCouriers(?string $orderNumber): ?AQueryBuilder
    {
        return $this->createQueryBuilder()
            ->andWhere('o.orderNumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->leftJoin('\XLite\Model\Shipping\Method', 's', Join::WITH, 's.method_id = o.shipping_id')
            ->select('s.aftership_couriers');
    }
}