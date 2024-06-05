<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Extension\CollectionSubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

interface CollectionSubExtensionInterface
{
    public const COLLECTION_SUB_EXTENSION_TAG = 'xcart.doctrine.orm.query_sub_extension.collection';

    public function support(string $className, string $operationName): bool;

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = []): void;
}
