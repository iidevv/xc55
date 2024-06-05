<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\ShopByBrand\Mapper;

/**
 * Class Cart
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\ShopByBrand")
 *
 */

class Product extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product
{
    /**
     * @param \XLite\Model\Product $product
     *
     * @return \XcartGraphqlApi\DTO\ProductDTO
     */
    public function mapToDto(\XLite\Model\Product $product, array $fields = [])
    {
        $dto = parent::mapToDto($product, $fields);

        $mapper = new Brand();

        $dto->brand = $mapper->mapToArray($product->getBrand());

        return $dto;
    }
}
