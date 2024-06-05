<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View;

use XCart\Extender\Mapping\Extender;
use XC\FastLaneCheckout;

/**
 * Pick address from address book
 * @Extender\Mixin
 */
abstract class SelectAddress extends \XLite\View\SelectAddress
{
    /**
     * Returns true if add new address button should be visible in address book
     *
     * @return boolean
     */
    protected function isAddAddressButtonVisible()
    {
        return FastLaneCheckout\Main::isFastlaneEnabled();
    }

    /**
     * Returns address book type
     *
     * @return string
     */
    protected function getAddressType()
    {
        return \XLite\Core\Request::getInstance()->atype;
    }
}
