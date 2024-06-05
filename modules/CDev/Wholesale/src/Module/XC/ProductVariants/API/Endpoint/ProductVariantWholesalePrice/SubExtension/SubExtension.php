<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice as Model;
use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;

/**
 * @Extender\Depend("XC\ProductVariants")
 */
class SubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
{
    public function support(string $className, string $operationName): bool
    {
        return $className === Model::class;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($context, $queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($context, $queryBuilder);
    }

    protected function addWhere(array $context, QueryBuilder $queryBuilder): void
    {
        $productID = $this->getProductId($context);
        if (!$productID) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        $productVariantId = $this->getProductVariantId($context);
        if (!$productVariantId) {
            throw new InvalidArgumentException('Product variant ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.productVariant = :product_variant_id', $rootAlias))
            ->setParameter('product_variant_id', $productVariantId);
    }

    protected function getProductId(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\/variants\/\d+/S', $context['request_uri'], $match)) {
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
