<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task;

/**
 * Orders Garbage Cleaner
 */
class OrdersGarbageCleaner extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Remove expired shopping carts');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Order')->collectGarbage();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return 1800;
    }
}
