<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;

use Qualiteam\SkinActCreateOrder\View\OrderItemWholesale;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class ModelOrderItemSelector extends \XLite\Controller\Admin\ModelOrderItemSelector
{
    protected function defineDataItem($item)
    {
        \XLite\Model\Profile::$useOrderProfileForMembership = true;

        $order = Database::getRepo('\XLite\Model\Order')->find(Request::getInstance()->order_id);

        if ($order && $order->getProfile()) {
            $item->orderProfileMembership = $order->getProfile()->getMembership();
        }

        $data = parent::defineDataItem($item);

        \XLite\Model\Profile::$useOrderProfileForMembership = false;

        if (count($item->getWholesalePrices()) > 0) {
            $data['wholesaleWidget'] = (new OrderItemWholesale(['product' => $item, 'cart' => $order]))->getContent();
        } else {
            $data['wholesaleWidget'] = '';
        }

        return $data;
    }

}