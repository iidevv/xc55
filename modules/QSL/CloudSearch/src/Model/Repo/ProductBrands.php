<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;

/**
 * The "product" repo class
 *
 * @Extender\Mixin
 * @Extender\Depend({"QSL\ShopByBrand"})
 */
abstract class ProductBrands extends \XLite\Model\Repo\Product
{
    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed        $value        Condition data
     *
     * @return void
     */
    protected function prepareCndBrandId(QueryBuilder $queryBuilder, $value)
    {
        if (!$this->isLoadProductsWithCloudSearch()) {
            parent::prepareCndBrandId($queryBuilder, $value);
        }
    }
}