<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\InjectLoggerTrait;

/**
 * Cache decorator
 */
class Cache extends \XLite\Base
{
    use InjectLoggerTrait;

    /**
     * Cache driver
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $driver;

    public function __construct()
    {
        $this->driver = \Doctrine\Common\Cache\Psr6\DoctrineProvider::wrap(\XCart\Container::getContainer()->get('xcart.cache'));
    }

    /**
     * Get driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Call driver's method
     *
     * @param string $name      Method name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        return call_user_func_array([$this->driver, $name], $arguments);
    }
}
