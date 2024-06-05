<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\SubIdentifierExtractor;

use XCart\Framework\ApiPlatform\Core\Api\SubIdentifierExtractor\SubIdentifierExtractorInterface;
use XLite\Model\Order;

class SubIdentifierExtractor implements SubIdentifierExtractorInterface
{
    public function getIdentifiersFromResourceClass(string $resourceClass): array
    {
        return [
            'id',
        ];
    }

    public function getIdentifiersFromItem($item): array
    {
        /** @var Order $item */
        return [
            'order_id' => $item->getOrderId(),
        ];
    }

    public function supportResourceClass(string $resourceClass): bool
    {
        return $resourceClass === Order::class;
    }

    public function supportItem(object $item): bool
    {
        return $this->supportResourceClass(get_class($item));
    }
}
