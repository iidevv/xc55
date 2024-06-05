<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Product\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Output as ProductOutput;
use XLite\Model\Product;
use XC\ProductTags\API\Endpoint\Product\DTO\Output as DecoratedOutputDTO;
use XC\ProductTags\API\Endpoint\Tag\DTO\TagOutput as ProductTagOutput;
use XC\ProductTags\Model\Product as DecoratedProduct;
use XC\ProductTags\Model\Tag;

/**
 * @Extender\Mixin
 */
class OutputTransformer extends \XLite\API\Endpoint\Product\Transformer\OutputTransformer
{
    /**
     * @param Product|DecoratedProduct $object
     */
    public function transform($object, string $to, array $context = []): ProductOutput
    {
        /** @var DecoratedOutputDTO $output */
        $output = parent::transform($object, $to, $context);

        $output->tags = $this->getTags($object);

        return $output;
    }

    /**
     * @return ProductTagOutput[]
     */
    public function getTags(DecoratedProduct $object): array
    {
        $tags = [];

        /** @var Tag $tag */
        foreach ($object->getTags() as $tag) {
            $output = new ProductTagOutput();
            $output->id = $tag->getId();
            $output->name = $tag->getName();
            $tags[] = $output;
        }

        return $tags;
    }
}
