<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Transformer;

use XLite\API\Endpoint\Product\DTO\Input as InputDTO;
use XLite\Model\Product;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Product;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
