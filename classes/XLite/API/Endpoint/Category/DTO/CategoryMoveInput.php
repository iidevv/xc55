<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\DTO;

class CategoryMoveInput
{
    /**
     * @var int|null
     */
    public ?int $parent = null;

    /**
     * @var int
     */
    public int $position = 0;
}
