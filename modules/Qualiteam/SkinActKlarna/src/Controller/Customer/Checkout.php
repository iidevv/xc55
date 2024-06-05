<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Controller\Customer;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    protected function assignAdditionalDataToCart(\XLite\Model\Order $cart)
    {
        if (isset(\XLite\Core\Request::getInstance()->authorization_token)) {
            $cart->setAuth(\XLite\Core\Request::getInstance()->authorization_token);
        }

        parent::assignAdditionalDataToCart($cart);
    }

    protected function doNoAction()
    {
        parent::doNoAction();
    }
}