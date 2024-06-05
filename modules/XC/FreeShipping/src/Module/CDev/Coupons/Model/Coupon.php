<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Module\CDev\Coupons\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate Coupon model (CDev\Coupons module)
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\Model\Coupon
{
    public const TYPE_FREESHIP = 'S';

    /**
     * Get amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(\XLite\Model\Order $order)
    {
        return $this->isFreeShipping() ? 0 : parent::getAmount($order);
    }

    /**
     * Return true if coupon has 'Free shipping' type
     *
     * @return boolean
     */
    public function isFreeShipping()
    {
        return $this->getType() == static::TYPE_FREESHIP;
    }

    /**
     * Get public name
     *
     * @return float
     */
    public function getPublicCode()
    {
        $result = parent::getPublicCode();

        if ($this->isFreeShipping()) {
            $result = sprintf('%s (%s)', $result, static::t('Free shipping'));
        }

        return $result;
    }
}
