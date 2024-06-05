<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class OrderTrackingNumber extends \XLite\View\ItemsList\Model\OrderTrackingNumber
{
    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return $this->getOrder() && $this->getOrder()->isNotFinishedOrder()
            ? static::CREATE_INLINE_NONE
            : parent::isInlineCreation();
    }
}
