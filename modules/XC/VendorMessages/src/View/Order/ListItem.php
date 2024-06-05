<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Order;

use XCart\Extender\Mapping\Extender;

/**
 * Orders search result item widget
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Order\ListItem
{
    /**
     * Count unread messages
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return integer
     */
    protected function countUnreadMessages(\XLite\Model\Order $order)
    {
        return $order->countUnreadMessages();
    }
}
