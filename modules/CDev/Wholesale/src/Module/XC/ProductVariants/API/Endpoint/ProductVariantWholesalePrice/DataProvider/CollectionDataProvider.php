<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider as BaseCollectionDataProvider;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice as Model;
use XC\ProductVariants\Model\Repo\ProductVariant as ProductVariantRepo;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Repo\Product as ProductRepo;

/**
 * @Extender\Depend("XC\ProductVariants")
 */
class CollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    protected BaseCollectionDataProvider $inner;

    protected ProductRepo $productRepository;

    protected ProductVariantRepo $productVariantRepository;

    public function __construct(
        BaseCollectionDataProvider $inner,
        ProductRepo $productRepository,
        ProductVariantRepo $productVariantRepository
    ) {
        $this->inner = $inner;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $productId = $this->getProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        if ($this->productRepository->countBy(['product_id' => $productId]) === 0) {
            throw new ItemNotFoundException(sprintf('Product with ID %d not found', $productId));
        }

        $variantId = $this->getProductVariantId($context);
        if (!$variantId) {
            throw new InvalidArgumentException('Product variant ID is invalid');
        }

        if ($this->productVariantRepository->countBy(['id' => $variantId]) === 0) {
            throw new ItemNotFoundException(sprintf('Product variant with ID %d not found', $variantId));
        }

        return $this->inner->getCollection($resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Model::class;
    }

    protected function getProductId(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\/variants\/\d+/Ss', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getProductVariantId(array $context): ?int
    {
        if (preg_match('/products\/\d+\/variants\/(\d+)/Ss', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
