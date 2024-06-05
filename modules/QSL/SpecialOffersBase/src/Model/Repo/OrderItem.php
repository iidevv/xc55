<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Order item surcharges repository
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\Repo\OrderItem
{
    /**
     * Returns the sum of order item surcharges
     *
     * @param \XLite\Model\Order $order    Order
     * @param bool               $included Whether to return included surcharges, or excluded OPTIONAL
     *
     * @return float
     */
    public function getSpecialOffersOrderItemSurchargesSum(\XLite\Model\Order $order, $included = false)
    {
        return $this->getSpecialOffersOrderItemSurchargesSumQB($order, $included)
            ->getSingleScalarResult();
    }

    /**
     * Returns a query builder to retrieve the sum of order item surcharges
     *
     * @param \XLite\Model\Order $order    Order
     * @param bool               $included Whether to return included surcharges, or excluded OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getSpecialOffersOrderItemSurchargesSumQB(\XLite\Model\Order $order, $included = false)
    {
        $qb = $this->createPureQueryBuilder()
            ->linkInner('o.surcharges', 's')
            ->select('sum(s.value) as surcharges_sum')
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->andWhere('s.available = :available')
            ->setParameter('available', true);

        if (!is_null($included)) {
            $qb->andWhere('s.include = :included')
                ->setParameter('included', $included);
        }

        return $qb;
    }
}
