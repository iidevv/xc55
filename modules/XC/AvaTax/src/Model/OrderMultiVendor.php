<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model;

use XCart\Extender\Mapping\Extender;
use XC\MultiVendor;

/**
 * Order
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 * @Extender\After ("XC\AvaTax")
 */
class OrderMultiVendor extends \XLite\Model\Order
{
    public function isTaxOwner()
    {
        /** @var \XC\MultiVendor\Model\Order $order */
        $order = $this;
        $warehouseMode = MultiVendor\Main::isWarehouseMode();

        return (($order->isChild() && !$warehouseMode) || ($order->isParent() && $warehouseMode));
    }

    protected function isAvataxTransactionsApplicable(): bool
    {
        return parent::isAvataxTransactionsApplicable()
            && $this->isTaxOwner();
    }
}
