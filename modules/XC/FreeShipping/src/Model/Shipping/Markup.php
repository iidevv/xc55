<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping markup model
 * @Extender\Mixin
 */
class Markup extends \XLite\Model\Shipping\Markup
{
    /**
     * Has rates
     *
     * @return boolean
     */
    public function hasRates()
    {
        return !$this->getShippingMethod()
            || !$this->getShippingMethod()->getFree();
    }
}
