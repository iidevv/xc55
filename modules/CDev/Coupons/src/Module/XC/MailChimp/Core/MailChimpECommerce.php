<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Module\XC\MailChimp\Core;

use XCart\Extender\Mapping\Extender;
use CDev\Coupons\Model\Coupon;
use CDev\Coupons\Module\XC\MailChimp\Logic\DataMapper\Coupon as CouponDataMapper;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class MailChimpECommerce extends \XC\MailChimp\Core\MailChimpECommerce
{
    public function getCoupons($storeId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Getting coupons');

        $result = $this->mailChimpAPI->get(
            "ecommerce/stores/{$storeId}/promo-rules"
        );

        return $this->mailChimpAPI->success()
            ? $result['promo_rules']
            : null;
    }

    public function createCoupon($storeId, Coupon $coupon)
    {
        $this->mailChimpAPI->setActionMessageToLog('Creating coupon');

        try {
            $rule = $this->mailChimpAPI->post(
                "ecommerce/stores/{$storeId}/promo-rules",
                CouponDataMapper::getPromoRuleDataByCoupon($coupon)
            );

            if ($this->mailChimpAPI->success() && $ruleId = $rule['id']) {
                $result = $this->mailChimpAPI->post(
                    "ecommerce/stores/{$storeId}/promo-rules/{$ruleId}/promo-codes",
                    CouponDataMapper::getPromoCodeDataByCoupon($coupon)
                );

                return $this->mailChimpAPI->success()
                    ? $result
                    : null;
            }
        } catch (Exception\CouponDoesNotMatch $e) {
        }

        return false;
    }

    public function updateCoupon($storeId, Coupon $coupon)
    {
        $this->mailChimpAPI->setActionMessageToLog('Updating coupon');
        try {
            $rule = $this->mailChimpAPI->patch(
                "ecommerce/stores/{$storeId}/promo-rules/{$coupon->getId()}",
                CouponDataMapper::getPromoRuleDataByCoupon($coupon)
            );

            if ($this->mailChimpAPI->success() && $ruleId = $rule['id']) {
                $result = $this->mailChimpAPI->patch(
                    "ecommerce/stores/{$storeId}/promo-rules/{$ruleId}/promo-codes/{$coupon->getId()}",
                    CouponDataMapper::getPromoCodeDataByCoupon($coupon)
                );

                return $this->mailChimpAPI->success()
                    ? $result
                    : null;
            }
        } catch (Exception\CouponDoesNotMatch $e) {
            $this->removeCoupon($storeId, $coupon->getId());
        }

        return false;
    }

    public function removeCoupon($storeId, $couponId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Removing coupon');

        return $this->mailChimpAPI->delete("/ecommerce/stores/{$storeId}/promo-rules/{$couponId}");
    }
}
