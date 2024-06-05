<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\Transformer;

use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedInput;
use CDev\FeaturedProducts\Model\FeaturedProduct;

interface InputTransformerInterface
{
    public function transform(CategoryFeaturedInput $object, string $to, array $context = []): FeaturedProduct;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
