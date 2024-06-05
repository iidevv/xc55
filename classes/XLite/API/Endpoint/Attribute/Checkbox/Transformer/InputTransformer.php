<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Attribute\Checkbox\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use XLite\API\Endpoint\Attribute\Checkbox\DTO\AttributeCheckboxInput as InputDTO;
use XLite\Model\Attribute as Model;
use XLite\Model\AttributeGroup;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
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
        $repository = $this->entityManager->getRepository(AttributeGroup::class);

        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();
        $entity->setPosition($object->position);
        $entity->setName($object->name);

        if ($object->groupId) {
            /** @var AttributeGroup $group */
            $group = $repository->find($object->groupId);

            if (!$group) {
                throw  new InvalidArgumentException(sprintf('Group with ID %d not found', $object->groupId));
            }

            if ($entity->getId()) {
                $attributeProductClassId = $entity->getProductClass()
                    ? $entity->getProductClass()->getId()
                    : null;
                $groupProductClassId = $group->getProductClass()
                    ? $group->getProductClass()->getId()
                    : null;
                if ($attributeProductClassId !== $groupProductClassId) {
                    throw  new InvalidArgumentException(
                        sprintf(
                            'Attribute and group has different product classes (%d vs. %d)',
                            $attributeProductClassId,
                            $groupProductClassId
                        )
                    );
                }
            }

            $entity->setAttributeGroup($group);
        } else {
            $entity->setAttributeGroup(null);
        }

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && $context['input']['class'] === InputDTO::class;
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
        $input->groupId = $entity->getAttributeGroup() ? $entity->getAttributeGroup()->getId() : null;

        return $input;
    }
}
