<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\ItemsList\Model\Order;

use XCart\Extender\Mapping\Extender;

/**
 * Order item. Fix for orders with no items
 *
 * @Extender\Mixin
 */
class OrderItem extends \XLite\View\ItemsList\Model\OrderItem
{
    /**
     * Entity if it doesn't exist
     *
     * @return \XLite\Model\OrderItem
     */
    public function getEntity()
    {
        if (!$this->entity) {

            $this->entity = new \XLite\Model\OrderItem();

        }
        return $this->entity;
    }
}
