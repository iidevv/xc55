<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View;

use XCart\Extender\Mapping\Extender;
use XC\FastLaneCheckout;

/**
 * Checkout success page
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\View\CheckoutSuccess
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        if (FastLaneCheckout\Main::isFastlaneEnabled()) {
            return array_merge(parent::getJSFiles(), [
                'modules/XC/FastLaneCheckout/checkout_fastlane/success.js'
            ]);
        }

        return parent::getJSFiles();
    }
}
