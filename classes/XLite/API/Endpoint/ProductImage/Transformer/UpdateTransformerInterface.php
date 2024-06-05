<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\Transformer;

use XLite\API\Endpoint\ProductImage\DTO\ImageUpdate;
use XLite\Model\Image\Product\Image;

interface UpdateTransformerInterface
{
    public function transform(ImageUpdate $object, string $to, array $context = []): Image;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
