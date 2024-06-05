<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Order extends \XLite\View\Order
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/order.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
