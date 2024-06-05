<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Class Checkout
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    public function getPixelInitCheckoutData()
    {
        $valuePercentage = (float) \XLite\Core\Config::getInstance()->XC->FacebookMarketing->init_checkout_value;

        $currency = \XLite::getInstance()->getCurrency();
        $pixelData = [
            'currency' => $currency->getCode(),
            'value' => $currency->roundValue($this->getCart()->getSubtotal() * ($valuePercentage / 100)),
        ];

        return $pixelData;
    }
}
