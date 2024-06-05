<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Hidden\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\API\Endpoint\AttributeValue\Hidden\DTO\AttributeValueHiddenInput as InputDTO;
use XLite\Model\AttributeOption;
use XLite\Model\AttributeValue\AttributeValueHidden as Model;

final class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        if (!$object->option_id) {
            throw new InvalidArgumentException('Attribute option is invalid');
        }

        $repository = $this->getRepository();
        $option = $repository->find($object->option_id);
        if (!$option) {
            throw new InvalidArgumentException(sprintf('Attribute option with ID %d not found', $object->option_id));
        }
        $entity->setAttributeOption($option);

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
        $input->option_id = $entity->getAttributeOption()->getId();

        return $input;
    }

    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AttributeOption::class);
    }
}
