<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Authorization
 * @Extender\Mixin
 */
class Checkout extends \XLite\View\Checkout
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'css/less/checkout.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => 'css/less/address-book.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => 'css/less/address-modify.less',
            'merge' => 'bootstrap/css/bootstrap.less'
        ];

        return $list;
    }
}
