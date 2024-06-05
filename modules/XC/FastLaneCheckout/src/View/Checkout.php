<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View;

use XCart\Extender\Mapping\Extender;
use XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 * @Extender\Mixin
 */
class Checkout extends \XLite\View\Checkout
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        // Disable default checkout when fastlane is enabled
        if (FastLaneCheckout\Main::isFastlaneEnabled()) {
            $result = [
                'DO_NOT_DISPLAY'
            ];
        }

        return $result;
    }
}
