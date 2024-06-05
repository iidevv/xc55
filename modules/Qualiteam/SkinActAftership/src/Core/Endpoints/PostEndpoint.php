<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints;

/**
 * Class post endpoint
 */
class PostEndpoint extends AEndpoint
{
    /**
     * Get additional path for url
     *
     * @return string
     */
    protected function getPath(): string
    {
        return '';
    }

    /**
     * Get url params
     *
     * @return string
     */
    protected function getParams(): string
    {
        return '';
    }
}