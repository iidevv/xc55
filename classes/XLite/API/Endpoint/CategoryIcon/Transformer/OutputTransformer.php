<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryIcon\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\CategoryIcon\DTO\IconOutput;
use XLite\Model\Image\Category\Image;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Image $object
     */
    public function transform($object, string $to, array $context = []): IconOutput
    {
        $output = new IconOutput();
        $output->alt = $object->getAlt();
        $output->url = $object->getFrontURL();
        $output->width = $object->getWidth();
        $output->height = $object->getHeight();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === IconOutput::class && $data instanceof Image;
    }
}
