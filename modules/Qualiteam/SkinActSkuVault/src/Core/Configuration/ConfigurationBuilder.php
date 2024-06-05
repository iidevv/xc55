<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Configuration;

use XLite\Core\ConfigCell;

/**
 * ConfigurationBuilder
 */
class ConfigurationBuilder
{
    /**
     * @var ConfigCell
     */
    protected $rawConfiguration;

    /**
     * Constructor
     *
     * @param ConfigCell $rawConfiguration
     *
     * @return void
     */
    public function __construct(ConfigCell $rawConfiguration)
    {
        $this->rawConfiguration = $rawConfiguration;
    }

    /**
     * Build
     *
     * @return Configuration
     * @noinspection PhpUndefinedFieldInspection
     */
    public function build(): Configuration
    {
        return new Configuration(
            (int)$this->rawConfiguration->consumeCommandsInterval * 60,
            (string)$this->rawConfiguration->oDataV4Url,
            (string)$this->rawConfiguration->basicAuthUser,
            (string)$this->rawConfiguration->basicAuthPassword
        );
    }
}
