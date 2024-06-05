<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\API\Endpoint\Category\DTO\CategoryProductOutput;
use XLite\Model\Category;
use XLite\Model\Product;

class DataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private EntityRepository $repository;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(Category::class);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Category::class && $operationName === 'get_category_products';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $categoryId = $this->getCategoryId($context);
        if (!$categoryId) {
            throw new InvalidArgumentException("Category ID is invalid");
        }

        /** @var Category $category */
        $category = $this->repository->find($categoryId);

        if (!$category) {
            throw new InvalidArgumentException(sprintf("Category with ID %s not found", $categoryId));
        }

        /** @var Product $product */
        foreach ($category->getProducts() as $product) {
            $o = new CategoryProductOutput();
            $o->product_id = $product->getProductId();
            yield $o;
        }
    }

    protected function getCategoryId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/categories\/(\d+)\/products/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
