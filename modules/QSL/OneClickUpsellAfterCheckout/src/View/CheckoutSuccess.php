<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OneClickUpsellAfterCheckout\View;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout success page
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\View\CheckoutSuccess
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/products_list.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => 'labels/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
