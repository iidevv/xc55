<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Base class
 *
 * @Extender\Mixin
 */
class Base extends \XLite\Base
{
    public static function addCDevGASingleton(): void
    {
        static::$singletons['CDevGA'] = GA::class;
    }
}
