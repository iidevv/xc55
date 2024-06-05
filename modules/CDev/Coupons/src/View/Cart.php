<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\View\Cart
{
    /**
     * Discount coupons (local cache)
     *
     * @var array
     */
    protected $discountCoupons;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/cart.less';

        return $list;
    }

    /**
     * Check - discount coupon subpanel is visible or not
     *
     * @param array $surcharge Surcharge
     *
     * @return boolean
     */
    protected function isDiscountCouponSubpanelVisible(array $surcharge)
    {
        return strtolower($surcharge['code']) === 'dcoupon' && $this->getDiscountCoupons();
    }

    /**
     * Get coupons
     *
     * @return array
     */
    protected function getDiscountCoupons()
    {
        if ($this->discountCoupons === null) {
            $this->discountCoupons = $this->getCart()->getUsedCoupons()->toArray();
        }

        return $this->discountCoupons;
    }

    /**
     * Check discount coupon remove control is visible or not
     *
     * @return boolean
     */
    protected function isDiscountCouponRemoveVisible()
    {
        return true;
    }
}
