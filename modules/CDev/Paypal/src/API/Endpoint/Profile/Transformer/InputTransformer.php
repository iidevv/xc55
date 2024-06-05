<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\API\Endpoint\Profile\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as InputDTO;
use XLite\API\Endpoint\Profile\Transformer\InputTransformer as ExtendedInputTransformer;
use CDev\Paypal\API\Endpoint\Profile\DTO\ProfileInput as ModuleInputDTO;
use CDev\Paypal\API\Endpoint\Profile\DTO\ProfileOutput as ModuleOutputDTO;
use CDev\Paypal\Model\Profile as Model;
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

        $entity->setSocialLoginProvider($object->social_login_provider);
        $entity->setSocialLoginId($object->social_login_id);

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

        $input->social_login_provider = $entity->getSocialLoginProvider();
        $input->social_login_id = $entity->getSocialLoginId();

        return $input;
    }
}
