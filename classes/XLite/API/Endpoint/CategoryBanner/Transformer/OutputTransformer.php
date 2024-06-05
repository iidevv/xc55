<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryBanner\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\CategoryBanner\DTO\BannerOutput;
use XLite\Model\Image\Category\Banner;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Banner $object
     */
    public function transform($object, string $to, array $context = []): BannerOutput
    {
        $output = new BannerOutput();
        $output->alt = $object->getAlt();
        $output->url = $object->getFrontURL();
        $output->width = $object->getWidth();
        $output->height = $object->getHeight();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === BannerOutput::class && $data instanceof Banner;
    }
}
