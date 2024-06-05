<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeGroup\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XLite\API\Endpoint\AttributeGroup\DTO\AttributeGroupInput as InputDTO;
use XLite\API\Language;
use XLite\Model\AttributeGroup as Model;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();
        if (Language::getInstance()->getLanguageCode()) {
            $entity->setEditLanguage(Language::getInstance()->getLanguageCode());
        }
        $entity->setPosition($object->position);
        $entity->setName($object->name);

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && ($context['input']['class'] ?? null) !== null;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input = new InputDTO();
        $input->name = $entity->getName();
        $input->position = $entity->getPosition();

        return $input;
    }
}
