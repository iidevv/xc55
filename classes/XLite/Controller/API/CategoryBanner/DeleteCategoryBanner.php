<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\CategoryBanner;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use XLite\Model\Category;
use XLite\Model\Image\Category\Banner;

class DeleteCategoryBanner
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $category_id)
    {
        /** @var Category $category */
        $category = $this->entityManager->getRepository(Category::class)->find($category_id);
        if (!$category) {
            throw new InvalidArgumentException(sprintf("Category with ID %s not found", $category_id));
        }

        /** @var \XLite\Model\Repo\Image\Category\Banner $repo */
        $repo = $this->entityManager->getRepository(Banner::class);

        $banner = $category->getBanner();
        if (!$banner) {
            throw new InvalidArgumentException("Banner not found");
        }

        $repo->delete($banner);
    }
}
