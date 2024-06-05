<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubExtension\Attribute;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\Attribute;

class GlobalAttributeSubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
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
        $this->addWhere($queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder);
    }

    protected function addWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.type = :type', $rootAlias))
            ->andWhere(sprintf('%s.productClass IS NULL', $rootAlias))
            ->andWhere(sprintf('%s.product IS NULL', $rootAlias))
            ->setParameter('type', $this->attributeType);
    }
}
