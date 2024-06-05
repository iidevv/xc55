<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Category\DTO\CategoryStatsOutput;
use XLite\Model\Category;

class CategoryStatsOutputTransformer implements DataTransformerInterface, CategoryStatsOutputTransformerInterface
{
    /**
     * @param Category $object
     */
    public function transform($object, string $to, array $context = []): CategoryStatsOutput
    {
        $output = new CategoryStatsOutput();
        $output->subcategories_count_all = $object->getQuickFlags()->getSubcategoriesCountAll();
        $output->subcategories_count_enabled = $object->getQuickFlags()->getSubcategoriesCountEnabled();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === CategoryStatsOutput::class && $data instanceof Category;
    }
}
