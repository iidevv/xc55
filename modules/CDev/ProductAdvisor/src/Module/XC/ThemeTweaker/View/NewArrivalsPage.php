<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Module\XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
class NewArrivalsPage extends \CDev\ProductAdvisor\View\NewArrivalsPage implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * @return string
     */
    protected function getDefaultDisplayName()
    {
        return static::t('New arrivals');
    }
}
