<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderHistory\Transformer\Detail;

use XLite\API\Endpoint\OrderHistory\DTO\Detail\OrderHistoryDetailOutput as OutputDTO;
use XLite\Model\OrderHistoryEventsData;

interface OutputTransformerInterface
{
    public function transform(OrderHistoryEventsData $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
