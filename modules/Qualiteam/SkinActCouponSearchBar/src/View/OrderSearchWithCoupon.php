<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCouponSearchBar\View;


use XLite\Core\Database;
use XLite\Core\Request;

use XCart\Extender\Mapping\ListChild;


/**
 * @ListChild(list="admin.h1.after", weight="9999", zone="admin")
 */
class OrderSearchWithCoupon extends \XLite\View\AView
{

    public static function getAllowedTargets()
    {
        return ['order_list'];
    }

    protected function getCoupon()
    {
        static $coupon = null;

        if ($coupon === null) {
            $cid = Request::getInstance()->couponId;
            $coupon = Database::getRepo('\CDev\Coupons\Model\Coupon')->find($cid);
        }

        return $coupon;
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getCoupon();
    }

    protected function getDescription()
    {
        $coupon = $this->getCoupon();

        return static::t('SkinActCouponSearchBar Orders with the applied coupon',
            [
                'coupon_code' => $coupon->getCode(),
                'coupon_link' => $this->buildURL('coupon', '', ['id' => $coupon->getId()])
            ]);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCouponSearchBar/OrderSearchWithCoupon.twig';
    }
}