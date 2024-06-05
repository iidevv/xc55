<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Common shipping method
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Shipping
{
    public function shouldAllowLongCalculations()
    {
        $shouldAllowLongCalculations = \XLite\Core\Request::getInstance()->should_allow_long_calculations;
        if ($shouldAllowLongCalculations) {
            \XLite\Core\Request::getInstance()->should_allow_long_calculations = false;
            return true;
        }

        return parent::shouldAllowLongCalculations();
    }
}
