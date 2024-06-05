<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * PhotoBox
 * @Extender\Mixin
 * @Extender\Depend("XC\CrispWhiteSkin")
 */
class PhotoBox extends \XLite\View\Product\Details\Customer\PhotoBox
{
    /**
     * Hardcoded to false in SS-21399
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        return false;
    }
}
