<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\DTO;

class CategoryStatsOutput
{
    /**
     * @var int
     */
    public int $subcategories_count_all;

    /**
     * @var int
     */
    public int $subcategories_count_enabled;
}
