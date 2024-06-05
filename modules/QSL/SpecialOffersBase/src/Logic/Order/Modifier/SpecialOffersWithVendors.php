<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Logic\Order\Modifier;

use XC\MultiVendor\Main;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Cart;

/**
 * Discount for selected payment method.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class SpecialOffersWithVendors extends SpecialOffers
{
    /**
     * If orders are divided by vendor, return parent order units list, to correctly count total products and spendings
     * instead of products and spendings for the current order only.
     *
     * @return array
     */
    protected function defineOrderUnits(): array
    {
        if (!Main::isWarehouseMode() && $this->order->isChild()) {
            $tempOrder = $this->order;
            $this->order = $this->order->getParent();
            $result = parent::defineOrderUnits();
            $this->order = $tempOrder;
        } else {
            $result = parent::defineOrderUnits();
        }
        return $result;
    }

    /**
     * Calculate.
     *
     * Calculate for end orders only (parent for warehouse mode and children
     * for non warehouse mode).
     *
     * @return void
     */
    public function calculate()
    {
        $order = $this->getOrder();
        $warehouseMode = Main::isWarehouseMode();

        if (
            !($order instanceof Cart) ||
            (($order->isChild() && !$warehouseMode) || ($order->isParent() && $warehouseMode))
        ) {
            parent::calculate();
        }
    }
}
