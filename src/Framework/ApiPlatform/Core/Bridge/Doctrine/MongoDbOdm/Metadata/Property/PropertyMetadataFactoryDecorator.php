<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Metadata\Property;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;

class PropertyMetadataFactoryDecorator implements PropertyMetadataFactoryInterface
{
    private PropertyMetadataFactoryInterface $inner;

    public function __construct(
        PropertyMetadataFactoryInterface $inner
    ) {
        $this->inner = $inner;
    }

    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        $options['enable_getter_setter_extraction'] = false;
        $options['enable_magic_methods_extraction'] = 0;

        return $this->inner->create($resourceClass, $property, $options);
    }
}
