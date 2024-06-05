<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * PhotoBox
 * @Extender\Mixin
 */
class PhotoBox extends \XLite\View\Product\Details\Customer\PhotoBox
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        return $this->getProduct()->hasImage();
    }
}
