<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Core;

use XCart\Extender\Mapping\Extender;
use XC\FacebookMarketing\Logic\ProductFeed\Generator;

/**
 * Event listener (common)
 * @Extender\Mixin
 */
class EventListener extends \XLite\Core\EventListener
{
    /**
     * Get listeners
     *
     * @return array
     */
    protected function getListeners()
    {
        return parent::getListeners() + [
            Generator::getEventName() => ['XC\FacebookMarketing\Core\EventListener\ProductFeedGeneration']
        ];
    }
}
