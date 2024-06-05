<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button;

/**
 * Checkout buttons separator
 */
class AButtonsSeparator extends \XLite\View\Button\ButtonsSeparator
{
    /**
     * isExpressCheckoutEnabled
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && (\CDev\Paypal\Main::isExpressCheckoutEnabled() || \CDev\Paypal\Main::isPaypalCommercePlatformEnabled());
    }
}
