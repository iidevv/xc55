<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductClass\Transformer;

use XLite\API\Endpoint\ProductClass\DTO\ProductClassOutput as ProductClassOutput;
use XLite\Model\ProductClass;

interface OutputTransformerInterface
{
    public function transform(ProductClass $object, string $to, array $context = []): ProductClassOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
