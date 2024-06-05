<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Product model repository.
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    /**
     * Allowable search params
     */
    public const P_DEPRECATED_GOOGLE_TAXONOMY = 'deprecatedGoogleTaxonomy';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDeprecatedGoogleTaxonomy(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkInner('p.googleShoppingCategory', 'gc')
                ->andWhere('gc.deprecated = 1');
        }
    }
}
