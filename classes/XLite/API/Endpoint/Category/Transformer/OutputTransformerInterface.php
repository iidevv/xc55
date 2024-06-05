<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\Transformer;

use XLite\API\Endpoint\Category\DTO\Output as CategoryOutput;
use XLite\Model\Category;

interface OutputTransformerInterface
{
    public function transform(Category $object, string $to, array $context = []): CategoryOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
