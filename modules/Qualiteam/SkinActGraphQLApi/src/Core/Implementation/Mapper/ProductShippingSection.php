<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;


class ProductShippingSection
{
    /**
     *
     *
     * @return array
     */
    public function mapProductShippingSection(\XLite\Model\Product $product)
    {
        return [
            'requiresShipping' => $product->getShippable(),
            'freeShipping' => method_exists($product, 'isShipForFree') ? $product->isShipForFree() : false,
            'localPickup' => $product->getFreeShip(),
            'separateBox' => $product->getUseSeparateBox(),
        ];
    }
}
