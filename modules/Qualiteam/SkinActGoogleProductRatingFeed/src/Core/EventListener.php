<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Core;

use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActGoogleProductRatingFeed\Core\EventListener\FeedGeneration;

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
            'feedRatingGeneration' => [FeedGeneration::class]
        ];
    }
}
