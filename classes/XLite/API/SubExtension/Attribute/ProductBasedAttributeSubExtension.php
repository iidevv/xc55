<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubExtension\Attribute;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\Attribute;

class ProductBasedAttributeSubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
{
    private string $attributeType;

    /**
     * @var string[]
     */
    protected array $operationNames;

    public function __construct(string $attributeType, array $operationNames)
    {
        $this->attributeType = $attributeType;
        $this->operationNames = $operationNames;
    }

    public function support(string $className, string $operationName): bool
    {
        return $className === Attribute::class && in_array($operationName, $this->operationNames, true);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $context);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $context);
    }

    protected function addWhere(QueryBuilder $queryBuilder, array $context): void
    {
        $productID = $this->getProductID($context);
        if (!$productID) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.type = :type', $rootAlias))
            ->andWhere(sprintf('%s.productClass IS NULL', $rootAlias))
            ->andWhere(sprintf('%s.product = :product_id', $rootAlias))
            ->setParameter('type', $this->attributeType)
            ->setParameter('product_id', $productID);
    }

    protected function getProductID(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\/attribute/S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
