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

class ProductClassBasedAttributeSubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
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
        $classID = $this->getClassID($context);
        if (!$classID) {
            throw new InvalidArgumentException('Product class ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.type = :type', $rootAlias))
            ->andWhere(sprintf('%s.productClass = :class_id', $rootAlias))
            ->andWhere(sprintf('%s.product IS NULL', $rootAlias))
            ->setParameter('type', $this->attributeType)
            ->setParameter('class_id', $classID);
    }

    protected function getClassID(array $context): ?int
    {
        if (preg_match('/product_classes\/(\d+)\/attribute/S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
