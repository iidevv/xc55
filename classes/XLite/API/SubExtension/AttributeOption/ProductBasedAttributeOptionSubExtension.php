<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\SubExtension\AttributeOption;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\Attribute;
use XLite\Model\AttributeOption;

class ProductBasedAttributeOptionSubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
{
    protected string $attributeType;

    /**
     * @var string[]
     */
    protected array $operationNames;

    protected string $pathPrefix;

    protected EntityManagerInterface $entityManager;

    public function __construct(
        string $attributeType,
        array $operationNames,
        string $pathPrefix,
        EntityManagerInterface $entityManager
    ) {
        $this->attributeType = $attributeType;
        $this->operationNames = $operationNames;
        $this->pathPrefix = $pathPrefix;
        $this->entityManager = $entityManager;
    }

    public function support(string $className, string $operationName): bool
    {
        return $className === AttributeOption::class && in_array($operationName, $this->operationNames);
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
        $attributeId = $this->getAttributeId($context);
        if (!$attributeId) {
            throw new InvalidArgumentException('Attribute ID is invalid');
        }

        if (!$this->hasAttribute($attributeId)) {
            throw new InvalidArgumentException(sprintf('Attribute with ID %d not found', $attributeId));
        }

        $productID = $this->getProductID($context);
        if (!$productID) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.attribute', $rootAlias), 'attribute')
            ->andWhere('attribute.type = :type AND attribute.id = :attributeId')
            ->andWhere('attribute.productClass IS NULL')
            ->andWhere('attribute.product = :productId')
            ->setParameter('type', $this->attributeType)
            ->setParameter('productId', $productID)
            ->setParameter('attributeId', $attributeId);
    }

    protected function getAttributeId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/' . $this->pathPrefix . '\/(\d+)\/options/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getProductID(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\/attribute/S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getAttributeRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Attribute::class);
    }

    protected function hasAttribute(int $id): bool
    {
        $count = $this->getAttributeRepository()->createQueryBuilder('m')
            ->andWhere('m.id = :id AND m.type = :type')
            ->setParameter('id', $id)
            ->setParameter('type', $this->attributeType)
            ->count();

        return $count > 0;
    }
}
