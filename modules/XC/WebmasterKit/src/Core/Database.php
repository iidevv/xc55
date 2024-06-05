<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * Database
 * @Extender\Mixin
 */
abstract class Database extends \XLite\Core\Database
{
    /**
     * Start Doctrine entity manager
     *
     * @return void
     */
    public function startEntityManager()
    {
        parent::startEntityManager();

        if (
            !defined('LC_CACHE_BUILDING')
            && isset(Config::getInstance()->XC->WebmasterKit->logSQL)
            && Config::getInstance()->XC->WebmasterKit->logSQL
        ) {
            static::$em->getConnection()->getConfiguration()
                ->setSQLLogger(\XLite\Logger::getInstance());
        }
    }
}
