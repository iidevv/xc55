<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\ProductWholesalePrice\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use CDev\Wholesale\Model\WholesalePrice as Model;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;

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

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.product = :product_id', $rootAlias))
            ->setParameter('product_id', $productID);
    }

    protected function getProductId(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\//S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
