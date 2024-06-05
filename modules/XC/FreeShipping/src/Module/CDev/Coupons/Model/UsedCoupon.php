<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Module\CDev\Coupons\Model;

use XCart\Extender\Mapping\Extender;

/**
 * UsedCoupon
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class UsedCoupon extends \CDev\Coupons\Model\UsedCoupon
{
    /**
     * Get coupon public name
     *
     * @return string
     */
    public function getPublicName()
    {
        if ($this->getType() && $this->getType() === \CDev\Coupons\Model\Coupon::TYPE_FREESHIP) {
            return sprintf('%s (%s)', $this->getPublicCode(), static::t('Free shipping'));
        }

        return parent::getPublicName();
    }
}
