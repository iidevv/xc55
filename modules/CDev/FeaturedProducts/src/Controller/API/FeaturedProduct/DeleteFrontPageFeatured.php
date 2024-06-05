<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Controller\API\FeaturedProduct;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use XLite\Model\Category;
use XLite\Model\Product;
use CDev\FeaturedProducts\Model\FeaturedProduct;

class DeleteFrontPageFeatured
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $product_id)
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $product_id));
        }

        /** @var \CDev\FeaturedProducts\Model\Repo\FeaturedProduct $repo */
        $repo = $this->entityManager->getRepository(FeaturedProduct::class);

        $link = $repo->findOneBy([
            'category' => $this->entityManager->getRepository(Category::class)->getRootCategory(),
            'product'  => $product,
        ]);

        if ($link) {
            $repo->delete($link);
        } else {
            throw new InvalidArgumentException('Wrong input');
        }
    }
}
