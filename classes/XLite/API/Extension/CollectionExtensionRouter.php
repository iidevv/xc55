<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;

class CollectionExtensionRouter implements QueryCollectionExtensionInterface
{
    /**
     * @var CollectionSubExtensionInterface[]
     */
    protected array $subExtensions = [];

    /**
     * @param CollectionSubExtensionInterface[] $subExtensions
     */
    public function __construct(iterable $subExtensions)
    {
        foreach ($subExtensions as $subExtension) {
            $this->addSubExtension($subExtension);
        }
    }

    public function addSubExtension(CollectionSubExtensionInterface $subExtension): void
    {
        $this->subExtensions[] = $subExtension;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        foreach ($this->subExtensions as $subExtension) {
            if ($subExtension->support($resourceClass, $operationName)) {
                $subExtension->applyToCollection(
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    $operationName,
                    $context
                );
            }
        }
    }
}
