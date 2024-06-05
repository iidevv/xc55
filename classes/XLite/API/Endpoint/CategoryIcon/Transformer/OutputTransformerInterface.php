<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryIcon\Transformer;

use XLite\API\Endpoint\CategoryIcon\DTO\IconOutput;
use XLite\Model\Image\Category\Image;

interface OutputTransformerInterface
{
    public function transform(Image $object, string $to, array $context = []): IconOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
