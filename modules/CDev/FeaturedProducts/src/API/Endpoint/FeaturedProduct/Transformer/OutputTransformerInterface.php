<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\Transformer;

use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedOutput;
use CDev\FeaturedProducts\Model\FeaturedProduct;

interface OutputTransformerInterface
{
    public function transform(FeaturedProduct $object, string $to, array $context = []): CategoryFeaturedOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
