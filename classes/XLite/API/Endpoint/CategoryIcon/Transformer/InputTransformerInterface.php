<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryIcon\Transformer;

use XLite\API\Endpoint\CategoryIcon\DTO\IconInput;
use XLite\Model\Image\Category\Image;

interface InputTransformerInterface
{
    public function transform(IconInput $object, string $to, array $context = []): Image;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
