<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use XC\ProductFilter\Model\Attribute;
use XCart\Extender\Mapping\Extender;

/**
 * CloudSearch store-side API methods
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductFilter")
 */
class StoreApiProductFilter extends \QSL\CloudSearch\Core\StoreApi
{
    /**
     * Check if specific attribute should be preselected as a custom filter for CloudFilters
     *
     * @param $attribute
     *
     * @return bool
     */
    protected function isPreselectAttributeAsFilter($attribute)
    {
        return $attribute['productClassId'] !== null && $attribute['visible'];
    }

    /**
     * Override to modify QueryBuilder before querying attributes
     *
     * @param $qb
     */
    protected function addProductAttributesQuerySelects($qb)
    {
        $qb->addSelect('a.visible');
    }
}
