<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\Transformer;

use XLite\API\Endpoint\ProductImage\DTO\ImageOutput;
use XLite\Model\Image\Product\Image;

interface OutputTransformerInterface
{
    public function transform(Image $object, string $to, array $context = []): ImageOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
