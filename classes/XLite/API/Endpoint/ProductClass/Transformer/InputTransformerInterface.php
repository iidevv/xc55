<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductClass\Transformer;

use XLite\Model\ProductClass;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassInput as InputDTO;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): ProductClass;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
