<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Admin;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Coupon extends \CDev\Coupons\Controller\Admin\Coupon
{
    protected function doNoAction()
    {
        $coupon = $this->getCoupon();

        if ($coupon->getExtraCoupon()) {
            $url = $this->buildURL('extra_coupon', '', ['id' => $coupon->getExtraCoupon()->getId()]);
            $this->redirect($url);
        }

        parent::doNoAction();
    }
}