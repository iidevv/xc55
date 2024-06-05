<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Tag\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XC\ProductTags\API\Endpoint\Tag\DTO\TagOutput as ProductTagOutput;
use XC\ProductTags\Model\Tag;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Tag $object
     */
    public function transform($object, string $to, array $context = []): ProductTagOutput
    {
        $output = new ProductTagOutput();
        $output->id = $object->getId();
        $output->name = $object->getName();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === ProductTagOutput::class && $data instanceof Tag;
    }
}
