<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\TrackingNumber;

use XLite\API\Endpoint\Order\DTO\TrackingNumber\OrderTrackingNumberOutput as OutputDTO;
use XLite\Model\OrderTrackingNumber;

interface OutputTransformerInterface
{
    public function transform(OrderTrackingNumber $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
