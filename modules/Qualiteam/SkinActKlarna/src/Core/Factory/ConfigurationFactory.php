<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Factory;

use Qualiteam\SkinActKlarna\Core\Configuration\Configuration;
use Qualiteam\SkinActKlarna\Core\Configuration\ConfigurationBuilder;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Database;
use XLite\Model\Payment\Method;

class ConfigurationFactory
{
    /**
     * Configuration
     *
     * @return Configuration
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
     * @return Method|null
     */
    protected static function getRawConfiguration(): ?Method
    {
        return Database::getRepo(Method::class)
            ->findOneBy([
                'service_name' => 'Klarna'
            ]);
    }
}