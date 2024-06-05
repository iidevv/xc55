<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\DataProvider;

use XLite\Model\Order;

class ItemDataProvider extends AItemDataProvider
{
    protected function getEntityName(): string
    {
        return Order::class;
    }
}
