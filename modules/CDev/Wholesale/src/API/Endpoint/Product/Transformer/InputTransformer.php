<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\Product\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Input as InputDTO;
use XLite\API\Endpoint\Product\Transformer\InputTransformer as ExtendedInputTransformer;
use CDev\Wholesale\API\Endpoint\Product\DTO\ProductInput as ModuleInputDTO;
use CDev\Wholesale\API\Endpoint\Product\DTO\ProductOutput as ModuleOutputDTO;
use CDev\Wholesale\Model\Product as Model;
use XLite\Model\Product as BaseModel;

/**
 * @Extender\Mixin
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

        $entity->setApplySaleToWholesale($object->apply_sale_to_wholesale);

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

        $input->apply_sale_to_wholesale = $entity->getApplySaleToWholesale();

        return $input;
    }
}
