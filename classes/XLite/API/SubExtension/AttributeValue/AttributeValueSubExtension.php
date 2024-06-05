<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubExtension\AttributeValue;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;

class AttributeValueSubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
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

    protected string $attributeType;

    protected string $pathPrefix;

    protected string $className;

    public function __construct(string $attributeType, string $pathPrefix, string $className)
    {
        $this->attributeType = $attributeType;
        $this->pathPrefix = $pathPrefix;
        $this->className = $className;
    }

    public function support(string $className, string $operationName): bool
    {
        return $this->className === $className && in_array($operationName, $this->operationNames);
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
            ->innerJoin(sprintf('%s.attribute', $rootAlias), 'attribute')
            ->andWhere('attribute.id = :attribute_id AND attribute.type = :type')
            ->setParameter('product_id', $productID)
            ->setParameter('attribute_id', $attributeID)
            ->setParameter('type', $this->attributeType);
    }

    protected function getProductID(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\/' . $this->pathPrefix . '\//S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getAttributeID(array $context): ?int
    {
        if (preg_match('/' . $this->pathPrefix . '\/(\d+)\/values/S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
