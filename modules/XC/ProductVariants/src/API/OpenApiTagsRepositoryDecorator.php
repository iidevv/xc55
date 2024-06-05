<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API;

use XLite\API\OpenApiTagsRepositoryInterface;

class OpenApiTagsRepositoryDecorator implements OpenApiTagsRepositoryInterface
{
    protected OpenApiTagsRepositoryInterface $inner;

    public function __construct(
        OpenApiTagsRepositoryInterface $inner
    ) {
        $this->inner = $inner;
    }

    public function getTags(): array
    {
        $tags = $this->inner->getTags();
        $tags['Products'][] = 'Product Variant';

        return $tags;
    }
}
