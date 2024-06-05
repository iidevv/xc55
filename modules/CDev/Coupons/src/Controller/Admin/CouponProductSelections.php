<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Controller\Admin;

class CouponProductSelections extends \XLite\Controller\Admin\ProductSelections
{
    /**
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage coupons');
    }

    /**
     * @param integer $productId Product ID
     *
     * @return bool
     */
    public function isExcludedProductId($productId)
    {
        $couponProduct = [
            'coupon'  => \XLite\Core\Request::getInstance()->coupon_id,
            'product' => $productId,
        ];

        return (bool)\XLite\Core\Database::getRepo('CDev\Coupons\Model\CouponProduct')
                ->findOneBy($couponProduct);
    }

    /**
     * @return \CDev\Coupons\Model\Coupon|null
     */
    public function getCoupon()
    {
        $couponId = \XLite\Core\Request::getInstance()->coupon_id;

        return $this->executeCachedRuntime(static function () use ($couponId) {
            return \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')
                ->find($couponId);
        }, ['getCoupon', $couponId]);
    }

    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: \CDev\Coupons\View\ItemsList\Model\CouponProductSelection::class;
    }
}
