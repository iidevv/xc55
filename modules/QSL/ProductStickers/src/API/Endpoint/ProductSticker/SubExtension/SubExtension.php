<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\API\Endpoint\ProductSticker\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use QSL\ProductStickers\Model\ProductSticker as Model;

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
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.isLabel = :no_label', $rootAlias))
            ->setParameter('no_label', false);
    }
}
