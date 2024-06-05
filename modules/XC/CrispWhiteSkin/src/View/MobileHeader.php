<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * MobileHeader
 * @Extender\Mixin
 */
abstract class MobileHeader extends \XLite\View\MobileHeader
{
    /**
     * Check block visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }
}
