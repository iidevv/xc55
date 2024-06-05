<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * getDisplayMode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return self::DISPLAY_MODE_GRID;
    }

    /**
     * isDisplayModeSelected
     *
     * @param string $displayMode Value to check
     *
     * @return boolean
     */
    protected function isDisplayModeSelectorVisible()
    {
        return false;
    }
}