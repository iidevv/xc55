<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Module\XC\MailChimp\Logic\DataMapper;

use XCart\Extender\Mapping\Extender;

/**
 * Coupon
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class Coupon extends \CDev\Coupons\Module\XC\MailChimp\Logic\DataMapper\Coupon
{
    public static function getPromoRuleDataByCoupon(\CDev\Coupons\Model\Coupon $coupon)
    {
        $data = parent::getPromoRuleDataByCoupon($coupon);

        if ($coupon->isFreeShipping()) {
            $data['amount'] = 0;
            $data['type'] = 'fixed';
            $data['target'] = 'shipping';
        }

        return $data;
    }
}
