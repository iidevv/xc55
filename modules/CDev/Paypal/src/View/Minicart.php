<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use XCart\Extender\Mapping\Extender;
use CDev\Paypal;

/**
 * @Extender\Mixin
 */
class Minicart extends \XLite\View\Minicart
{
    /**
     * Get number of cart items to display by default
     *
     * @return int
     */
    protected function getCountCartItemsToDisplay()
    {
        if (
            Paypal\Main::isExpressCheckoutEnabled()
            || Paypal\Main::isPaypalCommercePlatformEnabled()
            || Paypal\Main::isPaypalForMarketplacesEnabled()
            || Paypal\Main::isPaypalAdvancedEnabled()
        ) {
            return 2;
        } else {
            return parent::getCountCartItemsToDisplay();
        }
    }
}
