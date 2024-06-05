<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Product model repository
 * 
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    public const SEARCH_QUICKBOOKS_PRODUCTS = 'quickbooks_products';
    
    /**
     * prepareCndQuickbooksProducts
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndQuickbooksProducts(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->innerJoin(
            'Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts',
            'qp',
            'WITH',
            'qp.product_id = p.product_id'
        );
    }
}