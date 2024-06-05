<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeGroup\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\AttributeGroup;

class WithProductClassSubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
{
    /**
     * @var string[]
     */
    protected array $operationNames = [
        'product_class_based_get_subresources',
        'product_class_based_post_subresources',
        'product_class_based_get_subresource',
        'product_class_based_put_subresource',
        'product_class_based_delete_subresource',
    ];

    public function support(string $className, string $operationName): bool
    {
        return $className === AttributeGroup::class && in_array($operationName, $this->operationNames);
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
        $queryBuilder->andWhere(sprintf('%s.productClass = :product_class_id', $rootAlias))
            ->setParameter('product_class_id', $classID);
    }

    protected function getClassID(array $context): ?int
    {
        if (preg_match('/product_classes\/(\d+)\/attribute_groups/Ss', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
