<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Transformer;

use XLite\API\Endpoint\Product\DTO\Output as ProductOutput;
use XLite\Model\Product;

interface OutputTransformerInterface
{
    public function transform(Product $object, string $to, array $context = []): ProductOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
