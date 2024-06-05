<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    public const SEARCH_BESTSELLERS = 'bestsellers';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param boolean                    $value Condition data
     *
     * @return void
     */
    protected function prepareCndBestsellers(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        if ($value) {
            $qb->andWhere('p.sales > 0');
        }
    }
}
