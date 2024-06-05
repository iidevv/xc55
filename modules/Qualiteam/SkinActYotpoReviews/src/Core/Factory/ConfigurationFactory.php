<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use Qualiteam\SkinActYotpoReviews\Core\Configuration\ConfigurationBuilder;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\ConfigCell;
use XLite\Core\Config;

class ConfigurationFactory
{
    /**
     * Configuration
     *
     * @return \Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration
     */
    public static function createConfiguration(): Configuration
    {
        return ExecuteCached::executeCachedRuntime(function () {
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
        /** @noinspection PhpUndefinedFieldInspection */
        return Config::getInstance()->Qualiteam->SkinActYotpoReviews;
    }
}