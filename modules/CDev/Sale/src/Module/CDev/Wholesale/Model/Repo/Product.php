<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\Wholesale","CDev\Sale"})
 */
class Product extends \XLite\Model\Repo\Product
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndSaleDiscount(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        parent::prepareCndSaleDiscount($queryBuilder, $value, $countOnly);

        if (
            $value instanceof \CDev\Sale\Model\SaleDiscount
            && !$value->getApplyToWholesale()
        ) {
            $queryBuilder->linkLeft('p.wholesalePrices', 'wp')
                ->andWhere('wp IS NULL');
        }
    }
}
