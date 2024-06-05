<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryBanner\Transformer;

use XLite\API\Endpoint\CategoryBanner\DTO\BannerInput;
use XLite\Model\Image\Category\Banner;

interface InputTransformerInterface
{
    public function transform(BannerInput $object, string $to, array $context = []): Banner;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
