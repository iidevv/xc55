<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Hack for normal merge Coupons and VolumeDiscounts surcharges(to not exceed subtotal)
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\VolumeDiscounts")
 */
class VolumeDiscountsDiscount extends \CDev\Coupons\Logic\Order\Modifier\Discount
{
    /**
     * @return float
     */
    public function getDiscountBase()
    {
        $subtotal = $this->order->getSubtotal();
        $expectedClass = 'CDev\VolumeDiscounts\Logic\Order\Modifier\Discount';

        foreach ($this->getOrder()->getSurcharges() as $surcharge) {
            if ($surcharge->getClass() === $expectedClass) {
                $subtotal += $surcharge->getValue();
                break;
            }
        }

        return $subtotal;
    }
}
