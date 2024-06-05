<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Api\Config;

use XLite\Core\ConfigCell;

class ConfigBuilder
{
    protected ConfigCell $configCell;

    public function __construct(ConfigCell $configCell)
    {
        $this->configCell = $configCell;
    }

    public function build(): Config
    {
        return new Config(
            $this->configCell->ssa_api_key,
            $this->configCell->ssa_api_secret,
        );
    }
}