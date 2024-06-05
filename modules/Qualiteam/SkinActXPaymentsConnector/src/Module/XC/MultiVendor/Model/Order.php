<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\MultiVendor\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 *
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector","XC\MultiVendor"})
 */
class Order extends \XLite\Model\Order
{
    /**
     * Cart is not parent in case if is_zero_auth is true
     *
     * @return boolean
     */
    public function isParent()
    {
        return ($this->isZeroAuth()) ? false : parent::isParent();
    }
}
