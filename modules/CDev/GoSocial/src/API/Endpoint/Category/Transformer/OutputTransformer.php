<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\Category\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Category\DTO\Output as CategoryOutput;
use XLite\Model\Category;
use CDev\GoSocial\API\Endpoint\Category\DTO\Output as DecoratedOutputDTO;
use CDev\GoSocial\Model\Category as DecoratedCategory;

/**
 * @Extender\Mixin
 */
class OutputTransformer extends \XLite\API\Endpoint\Category\Transformer\OutputTransformer
{
    /**
     * @param Category|DecoratedCategory $object
     */
    public function transform($object, string $to, array $context = []): CategoryOutput
    {
        /** @var DecoratedOutputDTO $output */
        $output = parent::transform($object, $to, $context);

        $output->og_meta_tags = $object->getOpenGraphMetaTags();

        return $output;
    }
}
