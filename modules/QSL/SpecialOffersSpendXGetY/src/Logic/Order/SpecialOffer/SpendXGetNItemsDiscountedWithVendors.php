<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersSpendXGetY\Logic\Order\SpecialOffer;

use QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers;
use XCart\Extender\Mapping\Extender;
use XLite\Model\OrderItem;
use XC\MultiVendor\Main;

/**
 * SpendXGetNItemsDiscounted when XC-MultiVendor is enabled.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class SpendXGetNItemsDiscountedWithVendors extends SpendXGetNItemsDiscounted
{
    /**
     * Whether the surcharge can be added.
     *
     * @param OrderItem     $orderItem
     * @param SpecialOffers $modifier
     *
     * @return bool
     */
    protected function canAddSurcharge(OrderItem $orderItem, SpecialOffers $modifier): bool
    {
        return parent::canAddSurcharge($orderItem, $modifier)
            && (
                Main::isWarehouseMode()
                || $orderItem->getOrder()->getOrderId() === $modifier->getOrder()->getOrderId()
            );
    }
}
