<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Product model repository
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            [$sort, $order] = $this->getSortOrderValue($value);
            if ($sort == 'r.rating') {
                $queryBuilder->linkLeft('p.reviews', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'r.status = 1');
                $queryBuilder->addSelect('(SUM(r.rating) / COUNT(r.rating)) as rsm, COUNT(DISTINCT r.id) as rates_count');
                $sort = 'rsm';
                $queryBuilder->addOrderBy($sort, $order);
                $sort = 'rates_count';
                $queryBuilder->addOrderBy($sort, 'DESC');
            } else {
                parent::prepareCndOrderBy($queryBuilder, $value);
            }
        }
    }
}
