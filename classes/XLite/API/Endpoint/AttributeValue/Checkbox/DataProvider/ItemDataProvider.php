<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Checkbox\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\ItemDataProvider as BaseItemDataProvider;
use XLite\Model\AttributeValue\AttributeValueCheckbox;

class ItemDataProvider extends BaseItemDataProvider
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return parent::supports($resourceClass, $operationName, $context)
            && $resourceClass === AttributeValueCheckbox::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $id = [
            'product'   => $id['product_id'],
            'attribute' => $id['attribute_id'],
            'id'        => $id['id'],
        ];

        return parent::getItem($resourceClass, $id, $operationName, $context);
    }
}
