<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\AttributeValue\Select;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Product;

final class Post
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AttributeValueSelect $data, int $product_id, int $attribute_id): AttributeValueSelect
    {
        /** @var \XLite\Model\Repo\Product $repository */
        $repository = $this->getProductRepository();

        /** @var Product $product */
        $product = $repository->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        $data->setProduct($product);

        /** @var \XLite\Model\Repo\Attribute $repository */
        $repository = $this->getAttributeRepository();

        /** @var Attribute $attribute */
        $attribute = $repository->find($attribute_id);
        if (!$attribute) {
            throw new InvalidArgumentException(sprintf('Attribute with ID %d not found', $attribute_id));
        }

        if ($attribute->getType() !== Attribute::TYPE_SELECT) {
            throw new \InvalidArgumentException(
                sprintf('Attribute is wrong type - "%s" instead "%s"', $attribute->getType(), Attribute::TYPE_SELECT)
            );
        }

        $data->setAttribute($attribute);

        if ($this->getAttributeValueRepository()->findOneBy(['product' => $product, 'attribute' => $attribute, 'attribute_option' => $data->getAttributeOption()])) {
            throw new InvalidArgumentException('Cannot create attribute value. Value already exists');
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

    private function getAttributeValueRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AttributeValueSelect::class);
    }
}
