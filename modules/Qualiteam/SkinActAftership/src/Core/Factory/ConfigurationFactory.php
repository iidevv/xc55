<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Factory;

use Qualiteam\SkinActAftership\Core\Configuration\Configuration;
use Qualiteam\SkinActAftership\Core\Configuration\ConfigurationBuilder;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Config;
use XLite\Core\ConfigCell;

/**
 * Class configuration factory
 */
class ConfigurationFactory
{
    /**
     * Create configuration
     *
     * @return Configuration
     */
    public static function createConfiguration(): Configuration
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            $builder = new ConfigurationBuilder(
                static::getRawConfiguration()
            );

            return $builder->build();
        }, [
            __CLASS__,
            __FUNCTION__
        ]);
    }

    /**
     * Return raw configuration
     *
     * @return ConfigCell
     */
    protected static function getRawConfiguration(): ConfigCell
    {
        return Config::getInstance()->Qualiteam->SkinActAftership;
    }
}
