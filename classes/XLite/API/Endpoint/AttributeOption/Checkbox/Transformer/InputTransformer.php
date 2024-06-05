<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeOption\Checkbox\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XLite\API\Endpoint\AttributeOption\Checkbox\DTO\AttributeOptionCheckboxInput as InputDTO;
use XLite\Model\AttributeOption as Model;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();
        $entity->setPosition($object->position);
        $entity->setName($object->value);

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
        $input->value = $entity->getName();
        $input->position = $entity->getPosition();

        return $input;
    }
}
