<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Returns module skin dir
     *
     * @return boolean
     */
    public static function getSkinDir()
    {
        return 'modules/XC/FastLaneCheckout/';
    }

    /**
     * Checks if fastlane checkout mode is enabled
     *
     * @return boolean
     */
    public static function isFastlaneEnabled()
    {
        return \XLite\Core\Config::getInstance()->General->checkout_type === 'fast-lane';
    }
}
