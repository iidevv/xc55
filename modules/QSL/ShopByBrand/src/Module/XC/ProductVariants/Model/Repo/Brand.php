<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Module\XC\ProductVariants\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"XC\ProductVariants", "QSL\ShopByBrand"})
 */
class Brand extends \QSL\ShopByBrand\Model\Repo\Brand
{
    /**
     * Drops out of stock products from the query.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder to prepare
     */
    protected function dropOutOfStockProducts(\Doctrine\ORM\QueryBuilder $qb)
    {
        $qb->linkLeft('p.variants', 'pv');

        $useProductAmount = new \Doctrine\ORM\Query\Expr\Orx();
        $useProductAmount->add('pv.id IS NULL');
        $useProductAmount->add('pv.defaultAmount = :defaultAmount');
        $productAmountCnd = new \Doctrine\ORM\Query\Expr\Andx();
        $productAmountCnd->add($useProductAmount);
        $productAmountCnd->add('p.amount > :zero');

        $orCnd = new \Doctrine\ORM\Query\Expr\Orx();
        $orCnd->add('p.inventoryEnabled = :disabled');
        $orCnd->add('pv.amount > :zero');
        $orCnd->add($productAmountCnd);

        $qb->andWhere($orCnd)
            ->setParameter('defaultAmount', true)
            ->setParameter('disabled', false)
            ->setParameter('zero', 0);
    }
}
