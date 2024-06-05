<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Product Variant model repository
 * 
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\Repo\ProductVariant
{
    public const SEARCH_QUICKBOOKS_VARIANTS = 'quickbooks_variants';
    
    /**
     * prepareCndQuickbooksVariants
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndQuickbooksVariants(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->innerJoin(
            'Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts',
            'qp',
            'WITH',
            'qp.product_id = v.product AND qp.variant_id = v.id'
        );
    }
}