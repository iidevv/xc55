<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\ItemDataProvider as BaseItemDataProvider;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use CDev\Wholesale\Model\WholesalePrice as Model;
use XLite\Model\Repo\Product as ProductRepo;

class ItemDataProvider implements DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{
    protected BaseItemDataProvider $inner;

    protected ProductRepo $repository;

    public function __construct(
        BaseItemDataProvider $inner,
        ProductRepo $repository
    ) {
        $this->inner = $inner;
        $this->repository = $repository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Model::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if ($this->repository->countBy(['product_id' => $id['product_id']]) === 0) {
            throw new ItemNotFoundException(sprintf('Product with ID %d not found', $id['product_id']));
        }

        $id = [
            'id' => $id['id'],
        ];

        return $this->inner->getItem($resourceClass, $id, $operationName, $context);
    }
}
