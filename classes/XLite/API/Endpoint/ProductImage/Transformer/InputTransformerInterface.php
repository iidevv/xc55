<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\Transformer;

use XLite\API\Endpoint\ProductImage\DTO\ImageInput;
use XLite\Model\Image\Product\Image;

interface InputTransformerInterface
{
    public function transform(ImageInput $object, string $to, array $context = []): Image;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
