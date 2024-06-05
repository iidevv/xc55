<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use QSL\Make\Main as MakeMain;
use QSL\Make\Model\Product as MakeProduct;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * CloudSearch store-side API methods
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\Make"})
 */
class StoreApiMMY extends \QSL\CloudSearch\Core\StoreApi
{
    /**
     * Get "conditions" that can be used to restrict the results when searching.
     *
     * This is different from "attributes" which are used to construct full-fledged filters (CloudFilters).
     *
     * @param Product $product
     * @return array
     */
    protected function getProductConditions(Product $product)
    {
        $conditions = parent::getProductConditions($product);

        $conditions['mmy'] = [];

        $mapping = [
            MakeProduct::UNIVERSAL_FIT    => 100,
            MakeProduct::REGULAR_PRODUCT  => 10,
        ];

        $fitments = Database::getRepo(MakeMain::getLastLevelProductRepository())->search(new CommonCell([
            'productId' => $product->getProductId(),
        ]));

        if ($fitments) {
            foreach ($fitments as $fitment) {
                $conditions['mmy'][] = 'level_' . $fitment->getLevel()->getId();
            }
        } elseif (isset($mapping[$product->getFitmentType()])) {
            $conditions['mmy'][] = $mapping[$product->getFitmentType()];
        }

        return $conditions;
    }

    /**
     * Get sort fields that can be used to sort CloudSearch search results.
     * Sort fields are dynamic in the way that custom sort_int_*, sort_float_*, sort_str_* are allowed.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function getSortFields(Product $product)
    {
        $mapping = [
            MakeProduct::VEHICLE_SPECIFIC => 1000,
            MakeProduct::UNIVERSAL_FIT    => 100,
            MakeProduct::REGULAR_PRODUCT  => 10,
        ];

        return parent::getSortFields($product) + ['sort_int_fitment_type' => $mapping[$product->getFitmentType()]];
    }
}
