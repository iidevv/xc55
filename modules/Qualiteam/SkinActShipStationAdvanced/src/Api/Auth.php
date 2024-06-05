<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Api;

use Qualiteam\SkinActShipStationAdvanced\Api\Config\Config;

class Auth
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    protected function getAuthKey(): string
    {
        return base64_encode("{$this->config->getApiKey()}:{$this->config->getApiSecret()}");
    }

    public function getAuth(): string
    {
        return sprintf('Basic %s', $this->getAuthKey());
    }
}