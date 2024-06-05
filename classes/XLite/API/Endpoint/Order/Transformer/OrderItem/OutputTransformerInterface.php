<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\OrderItem;

use XLite\API\Endpoint\Order\DTO\OrderItem\OrderItemOutput as OutputDTO;
use XLite\Model\OrderItem;

interface OutputTransformerInterface
{
    public function transform(OrderItem $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
