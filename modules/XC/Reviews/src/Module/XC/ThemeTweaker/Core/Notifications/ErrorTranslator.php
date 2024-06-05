<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;

/**
 * ErrorTranslator
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class ErrorTranslator extends \XC\ThemeTweaker\Core\Notifications\ErrorTranslator
{
    protected static function getAvailabilityErrors()
    {
        return parent::getAvailabilityErrors() + [
                'review' => 'No products available. Please create at least one product.',
            ];
    }
}
