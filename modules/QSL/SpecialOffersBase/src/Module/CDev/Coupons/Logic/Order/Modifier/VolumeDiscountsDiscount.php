<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Module\CDev\Coupons\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Value discount modifier
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\VolumeDiscounts", "CDev\Coupons"})
 */
class VolumeDiscountsDiscount extends \CDev\Coupons\Logic\Order\Modifier\Discount
{
    /**
     * Calculate
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function calculate()
    {
        $surcharge = null;

        $total = 0;

        foreach ($this->getUsedCoupons() as $used) {
            if ($used->getCoupon()) {
                $used->setValue($used->getCoupon()->getAmount($this->order));
            }
            $total += $used->getValue();

            if ($used->getCoupon()) {
                $this->distributeDiscountAmongItems(
                    $used->getValue(),
                    $this->getOrder()->getValidItemsByCoupon($used->getCoupon())
                );
            }
        }

        if ($this->isValidTotal($total)) {
            $subtotal = $this->getSpecialOfferSubtotal();
            foreach ($this->getOrder()->getSurcharges() as $surcharge) {
                if ($surcharge->getClass() == 'CDev\VolumeDiscounts\Logic\Order\Modifier\Discount') {
                    $subtotal += $surcharge->getValue();
                    break;
                }
            }
            $total = min($total, $subtotal);
            $surcharge = $this->addOrderSurcharge($this->code, $total * -1, false);
        }

        return $surcharge;
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
