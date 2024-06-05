<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Module\CDev\VolumeDiscounts\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Value discount modifier
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\VolumeDiscounts")
 */
class Discount extends \CDev\VolumeDiscounts\Logic\Order\Modifier\Discount
{
    /**
     * Calculate
     *
     * @return float
     */
    public function calculate()
    {
        $surcharge = null;

        $discount = $this->getDiscount();

        if ($discount) {
            $total = $discount->getAmount($this->order);

            if ($total) {
                $total = min($total, $this->getSpecialOfferSubtotal());
                $surcharge = $this->addOrderSurcharge($this->code, $total * -1, false);

                // Distribute discount value among the ordered products
                $this->distributeDiscount($total);
            }
        }

        return $surcharge;
    }

    /**
     * Returns discount condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getDiscountCondition()
    {
        $cnd = parent::getDiscountCondition();

        $cnd->{\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_SUBTOTAL}
            = $this->getSpecialOfferSubtotal();

        return $cnd;
    }

    /**
     * Returns the order subtotal plus order item surcharges.
     *
     * @return float
     */
    protected function getSpecialOfferSubtotal()
    {
        return $this->getOrder()->getSpecialOffersSubtotal();
    }
}
