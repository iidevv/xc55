<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Event mediator
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class MediatorCoupons extends \QSL\Segment\Core\Mediator
{
    /**
     * Process coupons list changes
     *
     * @param array $old Old coupons list
     * @param array $new New coupons list
     */
    public function processCouponsDifference(array $old, array $new)
    {
        $addedCoupons = [];
        foreach ($new as $ni => $usedCouponNew) {
            $found = false;
            foreach ($old as $oi => $usedCouponOld) {
                if ($usedCouponNew->getCoupon()->getId() == $usedCouponOld->getCoupon()->getId()) {
                    $found = true;
                    unset($old[$oi]);
                    break;
                }
            }

            if (!$found) {
                $addedCoupons[] = $usedCouponNew;
            }
        }

        $removedCoupons = $old;

        // Assemble messages
        foreach ($addedCoupons as $usedCoupon) {
            $this->addMessage(
                'track',
                $this->assembleAddedCouponMessage($usedCoupon)
            );
        }

        foreach ($removedCoupons as $usedCoupon) {
            $this->addMessage(
                'track',
                $this->assembleRemovedCouponMessage($usedCoupon)
            );
        }
    }

    /**
     * Assemble message for 'track (added coupon)' request
     *
     * @param \CDev\Coupons\Model\UsedCoupon $usedCoupon Coupon
     *
     * @return array
     */
    protected function assembleAddedCouponMessage(\CDev\Coupons\Model\UsedCoupon $usedCoupon)
    {
        /** @var \XLite\Model\Currency $currency */
        $currency = $usedCoupon->getOrder()->getCurrency();

        return [
            'event'      => 'Added Discount Coupon',
            'properties' => [
                'coupon'   => $usedCoupon->getPublicCode(),
                'discount' => $currency->roundValue($usedCoupon->getValue()),
                'currency' => $currency->getCode(),
            ],
        ];
    }

    /**
     * Assemble message for 'track (removed coupon)' request
     *
     * @param \CDev\Coupons\Model\UsedCoupon $usedCoupon Coupon
     *
     * @return array
     */
    protected function assembleRemovedCouponMessage(\CDev\Coupons\Model\UsedCoupon $usedCoupon)
    {
        /** @var \XLite\Model\Currency $currency */
        $currency = $usedCoupon->getOrder()->getCurrency();

        return [
            'event'      => 'Removed Discount Coupon',
            'properties' => [
                'coupon'   => $usedCoupon->getPublicCode(),
                'discount' => $currency->roundValue($usedCoupon->getValue()),
                'currency' => $currency->getCode(),
            ],
        ];
    }
}
