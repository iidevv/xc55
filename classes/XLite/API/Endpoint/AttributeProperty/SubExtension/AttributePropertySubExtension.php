<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeProperty\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\AttributeProperty;

class AttributePropertySubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
{
    /**
     * @var string[]
     */
    protected array $operationNames = [
        'get',
        'post',
        'put',
        'delete',
    ];

    public function support(string $className, string $operationName): bool
    {
        return $className === AttributeProperty::class && in_array($operationName, $this->operationNames);
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

        $attributeID = $this->getAttributeID($context);
        if (!$attributeID) {
            throw new InvalidArgumentException('Attribute ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.product = :product_id', $rootAlias))
            ->andWhere(sprintf('%s.attribute = :attribute_id', $rootAlias))
            ->setParameter('product_id', $productID)
            ->setParameter('attribute_id', $attributeID);
    }

    protected function getProductID(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\/attributes\//S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getAttributeID(array $context): ?int
    {
        if (preg_match('/attributes\/(\d+)\/property/S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
