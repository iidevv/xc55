<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Controller\API\FeaturedProduct;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use CDev\FeaturedProducts\Model\FeaturedProduct;
use Doctrine\ORM\EntityManagerInterface;
use XLite\Model\Category;
use XLite\Model\Product;

class DeleteCategoryFeatured
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $category_id, int $product_id)
    {
        $category = $this->entityManager->getRepository(Category::class)->find($category_id);
        if (!$category) {
            throw new InvalidArgumentException(sprintf("Category with ID %s not found", $category_id));
        }

        $product = $this->entityManager->getRepository(Product::class)->find($product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $product_id));
        }

        /** @var \CDev\FeaturedProducts\Model\Repo\FeaturedProduct $repo */
        $repo = $this->entityManager->getRepository(FeaturedProduct::class);

        /** @var FeaturedProduct $link */
        $link = $repo->findOneBy([
            'category' => $category,
            'product'  => $product,
        ]);

        if ($link) {
            $repo->delete($link);
        } else {
            throw new InvalidArgumentException('Wrong input');
        }
    }
}
