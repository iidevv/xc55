<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\API\ProductVariant;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use XC\ProductVariants\Model\ProductVariant;
use XLite\Model\Product;

class Delete
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $product_id, int $id)
    {
        /** @var \XC\ProductVariants\Model\Repo\ProductVariant $repo */
        $repo = $this->entityManager->getRepository(ProductVariant::class);
        $variant = $repo->find($id);
        if (!$variant) {
            throw new InvalidArgumentException(sprintf("Product variant with ID %s not found", $id));
        }

        $product = $this->entityManager->getRepository(Product::class)->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $product_id));
        }

        $repo->delete($variant);

        Post::setDefaultVariant($product);

        $this->entityManager->flush();
    }
}
