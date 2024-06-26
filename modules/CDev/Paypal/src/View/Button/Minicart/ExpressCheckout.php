<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Minicart;

use XCart\Extender\Mapping\ListChild;

/**
 * Express Checkout button
 *
 * @ListChild (list="minicart.horizontal.buttons", weight="100")
 */
class ExpressCheckout extends \CDev\Paypal\View\Button\AExpressCheckout
{
    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = $this->getCart();

        return parent::isVisible()
            && $cart
            && (0 < $cart->getTotal())
            && $cart->checkCart();
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' cart-checkout'
            . (
            \CDev\Paypal\Main::isPaypalCreditEnabled($this->getCart())
                ? ' pp-funding-credit'
                : ''
            );
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'mini_cart';
    }
}
