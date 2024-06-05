<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\ItemDataProvider as BaseItemDataProvider;
use XLite\Model\Address;

class ItemDataProvider extends BaseItemDataProvider
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return parent::supports($resourceClass, $operationName, $context)
            && $resourceClass === Address::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $id = [
            'profile'   => (int) $id['profile_id'],
        ];

        return parent::getItem($resourceClass, $id, $operationName, $context);
    }
}
