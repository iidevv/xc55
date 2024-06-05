<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Configuration;

/**
 * Configuration
 */
class Configuration
{
    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * Constructor
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Return apiKey value
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
