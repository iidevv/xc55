<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Cart coupons
 *
 * @ListChild (list="cart.panel.box", weight="200")
 * @ListChild (list="checkout.review.selected", weight="15")
 * @ListChild (list="checkout_fastlane.sections.details", weight="200")
 */
class CartCoupons extends \XLite\View\AView
{
    /**
     * Used coupons list (local cache)
     *
     * @var   array
     */
    protected $coupons;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/cart_coupons.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Coupons/cart_coupons.js';

        return $list;
    }

    /**
     * @return boolean
     */
    protected function isFieldOnly()
    {
        return true;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Coupons/cart_coupons.twig';
    }

    // {{{ Content helpers

    /**
     * Get coupons
     *
     * @return array
     */
    protected function getCoupons()
    {
        if ($this->coupons === null) {
            $this->coupons = $this->getCart()->getUsedCoupons()->toArray();
        }

        return $this->coupons;
    }

    // }}}

    /**
     * Check if coupon panel 'Have a discount coupon?' is visible
     *
     * @return boolean
     */
    protected function isCouponPanelVisible()
    {
        return !$this->getCart()->hasSingleUseCoupon();
    }

    /**
     * Check if need to always show coupon box
     *
     * @return mixed|\XLite\Core\ConfigCell
     */
    public function showCouponBoxAlways()
    {
        return \XLite\Core\Config::getInstance()->CDev->Coupons->show_box;
    }
}
