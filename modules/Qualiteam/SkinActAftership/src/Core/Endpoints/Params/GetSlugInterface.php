<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints\Params;

/**
 * Get slug interface
 */
interface GetSlugInterface
{
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug(): string;
}