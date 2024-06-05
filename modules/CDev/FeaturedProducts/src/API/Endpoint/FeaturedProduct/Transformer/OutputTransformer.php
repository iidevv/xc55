<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedOutput;
use CDev\FeaturedProducts\Model\FeaturedProduct;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param FeaturedProduct $object
     */
    public function transform($object, string $to, array $context = []): CategoryFeaturedOutput
    {
        $output = new CategoryFeaturedOutput();
        $output->product_id = $object->getProduct()->getProductId();
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === CategoryFeaturedOutput::class && $data instanceof FeaturedProduct;
    }
}
