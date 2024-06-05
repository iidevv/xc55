<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Configuration;

use XLite\Core\ConfigCell;

/**
 * Class configuration builder
 */
class ConfigurationBuilder
{
    /**
     * @var ConfigCell
     */
    protected ConfigCell $rawConfiguration;

    /**
     * Constructor
     *
     * @param ConfigCell $rawConfiguration
     */
    public function __construct(ConfigCell $rawConfiguration)
    {
        $this->rawConfiguration = $rawConfiguration;
    }

    /**
     * Build
     *
     * @return Configuration
     */
    public function build(): Configuration
    {
        return new Configuration(
            (string)$this->rawConfiguration->api_key,
        );
    }

}
