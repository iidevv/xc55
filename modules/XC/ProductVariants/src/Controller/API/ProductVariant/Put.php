<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\API\ProductVariant;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use XC\ProductVariants\Model\Product as ExtendedProduct;
use XC\ProductVariants\Model\ProductVariant as Model;
use XLite\Model\Product;

class Put
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Model $data, int $product_id): Model
    {
        /** @var ExtendedProduct $product */
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d not found', $product_id));
        }

        if ($data->getDefaultValue()) {
            /** @var Model $variant */
            foreach ($product->getVariants() as $variant) {
                if ($variant->getId() !== $data->getId()) {
                    $variant->setDefaultValue(false);
                }
            }
        }

        $this->entityManager->flush();

        Post::setDefaultVariant($product);

        return $data;
    }
}
