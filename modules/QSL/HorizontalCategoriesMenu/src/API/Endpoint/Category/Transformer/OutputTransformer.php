<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\API\Endpoint\Category\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Category\DTO\Output as CategoryOutput;
use XLite\Model\Category;
use QSL\HorizontalCategoriesMenu\API\Endpoint\Category\DTO\Output as DecoratedOutputDTO;
use QSL\HorizontalCategoriesMenu\Model\Category as DecoratedCategory;

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

        $output->flyout_columns = $object->getFlyoutColumns();

        return $output;
    }
}
