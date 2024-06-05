<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Model\Repo;

use Doctrine\ORM\Query\Expr\Join;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    const P_FREQ_BOUGHT_ORDER_ITEMS = 'freqBoughtOrderItems';

    protected function prepareCndIsExcludeFreqBoughtTogether(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_bool($value)) {
            $queryBuilder
                ->andWhere('p.isExcludeFreqBoughtTogether = :excludeFreqBoughtTogether')
                ->setParameter('excludeFreqBoughtTogether', (bool)$value ? 1 : 0);
        }
    }

    protected function prepareCndFreqBoughtOrderItems(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            if (is_array($value) && 1 < count($value)) {
                $queryBuilder
                    ->distinct()
                    ->linkInner('p.order_items','oi')
                    ->andWhere('oi.order IN (' . implode(',', $value) . ')');
            } else {
                $queryBuilder->innerJoin('p.order_items', 'oi', Join::WITH, 'oi.order = :freqBoughtTogetherProductId')
                    ->setParameter('freqBoughtTogetherProductId', is_array($value) ? array_pop($value) : $value);
            }
        }
    }
}