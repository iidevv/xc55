<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryBanner\Transformer;

use XLite\API\Endpoint\CategoryBanner\DTO\BannerOutput;
use XLite\Model\Image\Category\Banner;

interface OutputTransformerInterface
{
    public function transform(Banner $object, string $to, array $context = []): BannerOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
