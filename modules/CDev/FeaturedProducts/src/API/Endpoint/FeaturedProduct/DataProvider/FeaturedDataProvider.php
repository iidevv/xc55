<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedOutput;
use CDev\FeaturedProducts\Model\Category as DecoratedCategory;
use CDev\FeaturedProducts\Model\FeaturedProduct;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\Category;

class FeaturedDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private EntityRepository $repository;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(Category::class);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === FeaturedProduct::class
            && in_array($operationName, ['get_category_featured', 'get_front_page_featured'], true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $categoryId = $this->getCategoryId($context);
        if (!$categoryId) {
            throw new InvalidArgumentException("Category ID is wrong");
        }

        /** @var DecoratedCategory $category */
        $category = $this->repository->find($categoryId);

        if (!$category) {
            throw new InvalidArgumentException(sprintf("Category with ID %s not found", $categoryId));
        }

        /** @var FeaturedProduct $featuredProduct */
        foreach ($category->getFeaturedProducts() as $featuredProduct) {
            $o = new CategoryFeaturedOutput();
            $o->product_id = $featuredProduct->getProduct()->getProductId();
            yield $o;
        }
    }

    protected function getCategoryId(array $context): ?int
    {
        if ($context['collection_operation_name'] === 'get_front_page_featured') {
            return $this->repository->getRootCategoryId();
        }

        if (
            isset($context['request_uri'])
            && preg_match('/categories\/(\d+)\/featured/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
