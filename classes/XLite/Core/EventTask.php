<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Event task
 */
class EventTask extends \XLite\Base\Singleton
{
    public const STATE_STANDBY     = 1;
    public const STATE_IN_PROGRESS = 2;
    public const STATE_FINISHED    = 3;
    public const STATE_ABORTED     = 4;

    /**
     * Driver
     *
     * @var \XLite\Core\EventDriver\AEventDriver
     */
    protected $driver;

    /**
     * Call events
     *
     * @param string $name Event name
     * @param array  $args Event arguments OPTIONAL
     *
     * @return boolean
     */
    public static function __callStatic($name, array $args = [])
    {
        $result = false;

        if (in_array($name, \XLite\Core\EventListener::getInstance()->getEvents())) {
            $args = isset($args[0]) && is_array($args[0]) ? $args[0] : [];
            $driver = static::getInstance()->getDriver();
            $result = $driver ? $driver->fire($name, $args) : false;
        }

        return $result;
    }

    /**
     * Get driver
     *
     * @return \XLite\Core\EventDriver\AEventDriver
     */
    public function getDriver()
    {
        if (!isset($this->driver)) {
            $this->driver = new \XLite\Core\EventDriver\Db();
        }

        return $this->driver;
    }
}
