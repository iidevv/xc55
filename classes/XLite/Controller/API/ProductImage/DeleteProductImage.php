<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\ProductImage;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use XLite\Model\Image\Product\Image;
use XLite\Model\Product;

class DeleteProductImage
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $product_id, int $image_id)
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $product_id));
        }

        /** @var \XLite\Model\Repo\Image\Product\Image $repo */
        $repo = $this->entityManager->getRepository(Image::class);

        $image = $repo->find($image_id);
        if (!$image) {
            throw new InvalidArgumentException(sprintf("Image with ID %s not found", $image_id));
        }

        $repo->delete($image);
    }
}
