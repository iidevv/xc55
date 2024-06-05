<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderHistory\Transformer;

use XLite\API\Endpoint\OrderHistory\DTO\OrderHistoryOutput as OutputDTO;
use XLite\Model\OrderHistoryEvents;

interface OutputTransformerInterface
{
    public function transform(OrderHistoryEvents $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
