<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Core;

use XCart\Extender\Mapping\Extender;
use QSL\ProductFeeds\Core\EventListener\GenerateFeeds;

/**
 * General event listener.
 * @Extender\Mixin
 */
abstract class EventListener extends \XLite\Core\EventListener
{
    /**
     * Return list of active listeners.
     *
     * @return array
     */
    protected function getListeners()
    {
        return parent::getListeners()
            + [
                GenerateFeeds::EVENT_NAME => ['\QSL\ProductFeeds\Core\EventListener\GenerateFeeds'],
            ];
    }
}
