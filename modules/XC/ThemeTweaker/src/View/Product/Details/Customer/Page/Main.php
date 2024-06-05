<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * Product details
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
class Main extends \XLite\View\Product\Details\Customer\Page\Main implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * @return string
     */
    protected function getDefaultDisplayName()
    {
        return static::t('Product details');
    }
}
