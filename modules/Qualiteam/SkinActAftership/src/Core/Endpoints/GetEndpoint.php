<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints;

/**
 * Get endpoint
 */
class GetEndpoint extends AEndpoint
{
    /**
     * Get additional path for url
     *
     * @return string
     */
    protected function getPath(): string
    {
        return implode('/', $this->preparePath());
    }

    /**
     * Collect path elements
     *
     * @return array
     */
    protected function preparePath(): array
    {
        return [];
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