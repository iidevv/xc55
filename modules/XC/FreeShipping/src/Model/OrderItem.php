<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate OrderItem model
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Return true if order item is forced to be 'free shipping' item
     *
     * @return boolean
     */
    public function isFreeShipping()
    {
        return $this->getProduct()->getFreeShip();
    }

    public function isShipForFree()
    {
        return $this->getProduct()->isShipForFree();
    }
}
