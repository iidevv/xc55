<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\AttributeOption;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use XLite\Model\Attribute;
use XLite\Model\AttributeOption;

final class Post
{
    protected EntityManagerInterface $entityManager;

    protected string $attributeType;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $attributeType
    ) {
        $this->entityManager = $entityManager;
        $this->attributeType = $attributeType;
    }

    public function __invoke(AttributeOption $data, int $attribute_id): AttributeOption
    {
        $repository = $this->getRepository();

        /** @var Attribute $attribute */
        $attribute = $repository->find($attribute_id);
        if (!$attribute) {
            throw new InvalidArgumentException(sprintf('Attribute with ID %d not found', $attribute_id));
        }

        if ($attribute->getType() !== $this->attributeType) {
            throw new InvalidArgumentException(sprintf('Attribute with ID %d is not of "%s" type', $attribute_id, $this->attributeType));
        }

        $data->setAttribute($attribute);

        return $data;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Attribute::class);
    }
}
