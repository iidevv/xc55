<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\Transformer;

use XLite\API\Endpoint\Category\DTO\CategoryProductInput;
use XLite\Model\Category;

interface CategoryProductInputTransformerInterface
{
    public function transform(CategoryProductInput $object, string $to, array $context = []): Category;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
