<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\Core\Notifications\Data\ProductVariant;

/**
 * Data
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class Data extends \XC\ThemeTweaker\Core\Notifications\Data
{
    protected function defineProviders()
    {
        return array_merge(parent::defineProviders(), [
            new ProductVariant()
        ]);
    }
}
