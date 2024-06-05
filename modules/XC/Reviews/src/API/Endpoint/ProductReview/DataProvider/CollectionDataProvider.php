<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\API\Endpoint\ProductReview\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider as BaseCollectionDataProvider;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use XC\Reviews\Model\Review as Model;
use XLite\Model\Repo\Product as ProductRepo;

class CollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    protected BaseCollectionDataProvider $inner;

    protected ProductRepo $repository;

    public function __construct(
        BaseCollectionDataProvider $inner,
        ProductRepo $repository
    ) {
        $this->inner = $inner;
        $this->repository = $repository;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $productId = $this->detectProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        if ($this->repository->countBy(['product_id' => $productId]) === 0) {
            throw new ItemNotFoundException(sprintf('Product with ID %d not found', $productId));
        }

        return $this->inner->getCollection($resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Model::class;
    }

    protected function detectProductId(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\//S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
