<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Cart\Transformer;

use XLite\API\Endpoint\Cart\DTO\CartOutput as OutputDTO;
use XLite\Model\Cart;

interface OutputTransformerInterface
{
    public function transform(Cart $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
