<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Core;

use XCart\Extender\Mapping\Extender;

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
            'feedGeneration' => ['XC\GoogleFeed\Core\EventListener\FeedGeneration']
        ];
    }
}
