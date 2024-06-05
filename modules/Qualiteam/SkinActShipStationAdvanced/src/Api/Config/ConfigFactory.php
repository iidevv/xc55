<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Api\Config;

use XLite\Core\Cache\ExecuteCached;
use XLite\Core\ConfigCell;
use XLite\Core\Config;

class ConfigFactory
{
    public static function createConfig()
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            $configBuilder = new ConfigBuilder(
                static::getModuleConfig()
            );

            return $configBuilder->build();
        }, [
            __METHOD__,
            __CLASS__
        ]);
    }

    protected static function getModuleConfig(): ConfigCell
    {
        return Config::getInstance()->ShipStation->Api;
    }
}
