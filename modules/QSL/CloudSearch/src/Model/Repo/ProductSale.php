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
 * @Extender\Depend({"CDev\Sale"})
 */
abstract class ProductSale extends \XLite\Model\Repo\Product
{
    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array        $value        Condition data
     * @param boolean      $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndParticipateSale(QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!$this->isLoadProductsWithCloudSearch()) {
            parent::prepareCndParticipateSale($queryBuilder, $value, $countOnly);
        }
    }
}
