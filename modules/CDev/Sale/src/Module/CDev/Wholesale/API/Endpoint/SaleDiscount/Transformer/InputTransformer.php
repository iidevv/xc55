<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\API\Endpoint\SaleDiscount\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\Sale\API\Endpoint\SaleDiscount\Transformer\InputTransformer as ParentInputTransformerAlias;
use CDev\Sale\Module\CDev\Wholesale\API\Endpoint\SaleDiscount\DTO\SaleDiscountInput as InputDTO;
use CDev\Sale\Module\CDev\Wholesale\Model\SaleDiscount as CurrentModel;
use CDev\Sale\Model\SaleDiscount as Model;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
class InputTransformer extends ParentInputTransformerAlias
{
    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        /** @var CurrentModel $entity */
        $entity = parent::transform($object, $to, $context);

        $entity->setApplyToWholesale($object->apply_to_wholesale);

        return $entity;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var InputDTO $input */
        $input = parent::initialize($inputClass, $context);

        /** @var CurrentModel $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input->apply_to_wholesale = $entity->getApplyToWholesale();

        return $input;
    }
}
