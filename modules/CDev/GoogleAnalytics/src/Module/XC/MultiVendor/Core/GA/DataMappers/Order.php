<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\XC\MultiVendor\Core\GA\DataMappers;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Base\Surcharge;
use XC\MultiVendor\Main;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class Order extends \CDev\GoogleAnalytics\Core\GA\DataMappers\Order
{
    public function getPurchaseData(\XLite\Model\Order $order): array
    {
        $data = parent::getPurchaseData($order);

        if (Main::isWarehouseMode()) {
            $ids = [];

            $shippingCost = 0;

            /** @var \XLite\Model\Order|\XC\MultiVendor\Model\Order $order */
            $order = $order->isChild() ? $order->getParent() : $order;

            foreach ($order->getChildren() as $child) {
                $ids[]        = $child->getOrderNumber();
                $shippingCost += $child->getSurchargeSumByType(Surcharge::TYPE_SHIPPING);
            }

            $data['id'] = implode('/', $ids);

            $data['shipping'] = $shippingCost;

            return $data;
        }

        return $data;
    }
}
