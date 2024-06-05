<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\LayoutSettings;

use XCart\Extender\Mapping\Extender;

/**
 * Layout settings
 * @Extender\Mixin
 */
class LayoutTypeSelector extends \XLite\View\LayoutSettings\LayoutTypeSelector
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return false;
    }
}
