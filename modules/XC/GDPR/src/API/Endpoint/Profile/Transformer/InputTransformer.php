<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\API\Endpoint\Profile\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as InputDTO;
use XLite\API\Endpoint\Profile\Transformer\InputTransformer as ExtendedInputTransformer;
use XC\GDPR\API\Endpoint\Profile\DTO\ProfileInput as ModuleInputDTO;
use XC\GDPR\API\Endpoint\Profile\DTO\ProfileOutput as ModuleOutputDTO;
use XC\GDPR\Model\Profile as Model;
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

        $entity->setGdprConsent($object->gdpr_consent);
        $entity->setAllCookiesConsent($object->all_cookies_consent);
        $entity->setDefaultCookiesConsent($object->default_cookies_consent);

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

        $input->gdpr_consent = $entity->isGdprConsent();
        $input->all_cookies_consent = $entity->getAllCookiesConsent();
        $input->default_cookies_consent = $entity->getDefaultCookiesConsent();

        return $input;
    }
}
