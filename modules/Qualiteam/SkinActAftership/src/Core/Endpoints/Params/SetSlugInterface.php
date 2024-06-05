<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints\Params;

/**
 * Set slug interface
 */
interface SetSlugInterface
{
    const PARAM_SLUG = 'slug';

    /**
     * Set slug param
     *
     * @param string $slug
     *
     * @return void
     */
    public function setSlug(string $slug): void;
}