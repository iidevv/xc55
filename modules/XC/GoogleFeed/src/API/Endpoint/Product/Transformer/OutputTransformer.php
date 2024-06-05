<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\API\Endpoint\Product\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Output as ProductOutput;
use XLite\Model\Product;
use XC\GoogleFeed\API\Endpoint\Product\DTO\Output as DecoratedOutputDTO;
use XC\GoogleFeed\Model\Product as DecoratedProduct;

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

        $output->google_feed_enabled = $object->getGoogleFeedEnabled();

        return $output;
    }
}
