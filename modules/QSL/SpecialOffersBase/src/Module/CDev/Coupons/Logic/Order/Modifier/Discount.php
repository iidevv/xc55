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
 * @Extender\Depend ({"!CDev\VolumeDiscounts", "CDev\Coupons"})
 */
class Discount extends \CDev\Coupons\Logic\Order\Modifier\Discount
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
            $total = min($total, $this->getSpecialOfferSubtotal());
            $surcharge = $this->addOrderSurcharge($this->code, $total * -1, false);
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
