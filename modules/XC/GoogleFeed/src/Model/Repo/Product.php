<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Products repository
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Model\Repo\Product
{
    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFeedGenerationQueryBuilder($position)
    {
        $qb = parent::defineFeedGenerationQueryBuilder($position);

        $this->assignEnabledCondition($qb);
        $this->assignGoogleFeedEnabledCondition($qb);

        $alias = $qb->getRootAliases()[0];
        $qb->orderBy($alias . '.product_id');

        return $qb;
    }

    /**
     * Assign google feed enabled condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string                     $alias        Alias OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function assignGoogleFeedEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $alias = $alias ?: $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($alias . '.googleFeedEnabled = :enabled')
            ->setParameter('enabled', true);

        return $queryBuilder;
    }
}
