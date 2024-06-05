<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantInput as ModuleInputDTO;
use CDev\Sale\Module\XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantOutput as ModuleOutputDTO;
use CDev\Sale\Model\ProductVariant as Model;
use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantInput as InputDTO;
use XC\ProductVariants\API\Endpoint\ProductVariant\Transformer\InputTransformer as ExtendedInputTransformer;
use XC\ProductVariants\Model\ProductVariant as BaseModel;

/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants"})
 */
class InputTransformer extends ExtendedInputTransformer
{
    /**
     * @param ModuleInputDTO $object
     */
    public function transform($object, string $to, array $context = []): BaseModel
    {
        /** @var Model $entity */
        $entity = parent::transform($object, $to, $context);

        $entity->setDiscountType($object->sale_discount_type);
        $entity->setSalePriceValue($object->sale_price_value);
        $entity->setDefaultSale(!$object->sale_price_value);

        return $entity;
    }

    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        /** @var ModuleOutputDTO $input */
        $input = parent::initialize($inputClass, $context);

        $input->sale_discount_type = $entity->getDiscountType();
        $input->sale_price_value = $entity->getSalePriceValue();

        return $input;
    }
}
