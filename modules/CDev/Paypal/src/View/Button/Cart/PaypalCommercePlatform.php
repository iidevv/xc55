<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Cart;

use XCart\Extender\Mapping\ListChild;

/**
 * Express Checkout button
 *
 * @ListChild (list="cart.panel.totals", weight="100")
 */
class PaypalCommercePlatform extends \CDev\Paypal\View\Button\APaypalCommercePlatform
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/paypal_commerce_platform/cart.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pcp-cart';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'cart';
    }
}
