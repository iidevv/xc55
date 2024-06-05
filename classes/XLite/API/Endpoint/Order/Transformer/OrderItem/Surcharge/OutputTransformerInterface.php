<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\OrderItem\Surcharge;

use XLite\API\Endpoint\Order\DTO\Surcharge\OrderSurchargeOutput as OutputDTO;
use XLite\Model\OrderItem\Surcharge;

interface OutputTransformerInterface
{
    public function transform(Surcharge $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
