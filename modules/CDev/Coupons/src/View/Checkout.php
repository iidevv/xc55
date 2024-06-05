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
abstract class Checkout extends \XLite\View\Checkout
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/cart_coupons.css';
        $list[] = 'modules/CDev/Coupons/button/add_coupon.css';

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
}
