<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\Transformer;

use Exception;
use CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantOutput as ModuleOutputDTO;
use CDev\Sale\Model\ProductVariant as Model;
use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantOutput as OutputDTO;
use XC\ProductVariants\API\Endpoint\ProductVariant\Transformer\OutputTransformer as ExtendedOutputTransformer;

/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants"})
 */
class OutputTransformer extends ExtendedOutputTransformer
{
    /**
     * @param Model $object
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::transform($object, $to, $context);

        $dto->sale_discount_type = $object->getDiscountType();
        $dto->sale_price_value = $object->getSalePriceValue();
        $dto->default_sale_price = $object->getDefaultSale();

        return $dto;
    }
}
