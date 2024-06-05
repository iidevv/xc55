<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer;

use XLite\API\Endpoint\Order\DTO\OrderOutput as OutputDTO;
use XLite\Model\Order;

interface OutputTransformerInterface
{
    public function transform(Order $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
