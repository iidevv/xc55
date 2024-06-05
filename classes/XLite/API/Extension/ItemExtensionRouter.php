<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;

class ItemExtensionRouter implements QueryItemExtensionInterface
{
    /**
     * @var ItemSubExtensionInterface[]
     */
    protected array $subExtensions = [];

    /**
     * @param ItemSubExtensionInterface[] $subExtensions
     */
    public function __construct(
        iterable $subExtensions
    ) {
        foreach ($subExtensions as $subExtension) {
            $this->addSubExtension($subExtension);
        }
    }

    public function addSubExtension(ItemSubExtensionInterface $subExtension): void
    {
        $this->subExtensions[] = $subExtension;
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        foreach ($this->subExtensions as $subExtension) {
            if ($subExtension->support($resourceClass, $operationName)) {
                $subExtension->applyToItem(
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    $identifiers,
                    $operationName,
                    $context
                );
            }
        }
    }
}
