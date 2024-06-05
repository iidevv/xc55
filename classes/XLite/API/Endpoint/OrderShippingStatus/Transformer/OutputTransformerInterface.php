<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderShippingStatus\Transformer;

use XLite\API\Endpoint\OrderShippingStatus\DTO\OrderShippingStatusOutput as OutputDTO;
use XLite\Model\Order\Status\Shipping;

interface OutputTransformerInterface
{
    public function transform(Shipping $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
