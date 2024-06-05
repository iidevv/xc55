<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    // {{{ Search

    public const SEARCH_FEATURED_CATEGORY_ID = 'featuredCategoryId';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb    Query builder to prepare
     * @param integer                                 $value Condition data
     *
     * @return void
     */
    protected function prepareCndFeaturedCategoryId(\XLite\Model\QueryBuilder\AQueryBuilder $qb, $value)
    {
        $qb->linkInner('p.featuredProducts')
            ->andWhere('featuredProducts.category = :featuredCategoryId')
            ->setParameter('featuredCategoryId', $value);
    }

    // }}}
}
