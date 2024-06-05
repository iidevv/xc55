<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\API\Endpoint\Profile\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as InputDTO;
use XLite\API\Endpoint\Profile\Transformer\InputTransformer as ExtendedInputTransformer;
use XC\AvaTax\API\Endpoint\Profile\DTO\ProfileInput as ModuleInputDTO;
use XC\AvaTax\API\Endpoint\Profile\DTO\ProfileOutput as ModuleOutputDTO;
use XC\AvaTax\Model\Profile as Model;
use XLite\Model\Profile as BaseModel;

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

        $entity->setAvaTaxExemptionNumber($object->ava_tax_exemption_number);
        $entity->setAvaTaxCustomerUsageType($object->ava_tax_customer_usage_type);

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

        $input->ava_tax_exemption_number = $entity->getAvaTaxExemptionNumber();
        $input->ava_tax_customer_usage_type = $entity->getAvaTaxCustomerUsageType();

        return $input;
    }
}
