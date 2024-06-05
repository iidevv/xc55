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
use XLite\Model\Product;

final class ProductBasedPost
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

    public function __invoke(AttributeOption $data, int $product_id, int $attribute_id): AttributeOption
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

        $product = $this->getProductRepository()->find($product_id);
        if (!$product) {
            throw new \InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        $data->setProduct($product);

        return $data;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Attribute::class);
    }

    private function getProductRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }
}
