<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\Product\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Output as ProductOutput;
use XLite\Model\Product;
use CDev\GoSocial\API\Endpoint\Product\DTO\Output as DecoratedOutputDTO;
use CDev\GoSocial\Model\Product as DecoratedProduct;

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

        $output->og_meta_tags = $object->getOpenGraphMetaTags();

        return $output;
    }
}
