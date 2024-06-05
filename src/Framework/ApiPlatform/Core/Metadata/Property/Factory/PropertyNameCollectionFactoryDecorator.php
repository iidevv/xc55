<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Metadata\Property\Factory;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyNameCollection;

class PropertyNameCollectionFactoryDecorator implements PropertyNameCollectionFactoryInterface
{
    private PropertyNameCollectionFactoryInterface $inner;

    public function __construct(
        PropertyNameCollectionFactoryInterface $inner
    ) {
        $this->inner = $inner;
    }

    public function create(string $resourceClass, array $options = []): PropertyNameCollection
    {
        $options['enable_getter_setter_extraction'] = false;
        $options['enable_magic_methods_extraction'] = 0;

        return $this->inner->create($resourceClass, $options);
    }
}
