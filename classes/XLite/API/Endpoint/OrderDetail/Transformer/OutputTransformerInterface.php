<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderDetail\Transformer;

use XLite\API\Endpoint\OrderDetail\DTO\OrderDetailOutput as OutputDTO;
use XLite\Model\OrderDetail;

interface OutputTransformerInterface
{
    public function transform(OrderDetail $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
