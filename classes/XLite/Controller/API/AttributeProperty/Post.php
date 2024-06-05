<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\AttributeProperty;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\Attribute;
use XLite\Model\AttributeProperty;
use XLite\Model\Product;

final class Post
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AttributeProperty $data, int $product_id, int $attribute_id): AttributeProperty
    {
        $repository = $this->getProductRepository();

        $product = $repository->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        $data->setProduct($product);

        $repository = $this->getAttributeRepository();

        $attribute = $repository->find($attribute_id);
        if (!$attribute) {
            throw new InvalidArgumentException(sprintf('Attribute with ID %d not found', $attribute_id));
        }

        $data->setAttribute($attribute);

        $model = $this->getAttributePropertyRepository()->findOneBy(['product' => $product, 'attribute' => $attribute]);
        if ($model) {
            throw new InvalidArgumentException('Product can only have one property for attribute');
        }

        return $data;
    }

    private function getProductRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    private function getAttributeRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Attribute::class);
    }

    private function getAttributePropertyRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AttributeProperty::class);
    }
}
