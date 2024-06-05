<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return true if checkout layout is used
     *
     * @return boolean
     */
    public function isFastlaneEnabled()
    {
        return FastLaneCheckout\Main::isFastlaneEnabled();
    }
}
