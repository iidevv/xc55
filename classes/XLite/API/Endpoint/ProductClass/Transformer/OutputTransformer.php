<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductClass\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassOutput as ProductClassOutput;
use XLite\Model\ProductClass;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param ProductClass $object
     */
    public function transform($object, string $to, array $context = []): ProductClassOutput
    {
        $output = new ProductClassOutput();
        $output->id = $object->getId();
        $output->name = $object->getName();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === ProductClassOutput::class && $data instanceof ProductClass;
    }
}
