<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Sale\Mapper;

use Doctrine\Common\Collections\Collection;

/**
 * Class Cart
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("CDev\Sale")
 *
 */

class Product extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product
{
    /**
     * @param \CDev\Sale\Model\Product $product
     *
     * @return \XcartGraphqlApi\DTO\ProductDTO
     */
    public function mapToDto(\XLite\Model\Product $product, array $fields = [])
    {
        $dto = parent::mapToDto($product, $fields);

        $dto->on_sale = $product->getParticipateSale();
        $dto->sale_value = $product->getSalePriceValue();
        $dto->sale_type = $product->getDiscountType();

        return $dto;
    }
}
