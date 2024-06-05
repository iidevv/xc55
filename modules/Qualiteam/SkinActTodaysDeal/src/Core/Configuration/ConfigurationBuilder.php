<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActTodaysDeal\Core\Configuration;

use XLite\Core\ConfigCell;

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
            (string) $this->rawConfiguration->td_name,
            (int) $this->rawConfiguration->td_category,
            (int) $this->rawConfiguration->td_limit
        );
    }
}