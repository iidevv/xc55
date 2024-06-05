<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * ACustomer
 * @Extender\Mixin
 * @Extender\Depend ("XC\CrispWhiteSkin")
 */
abstract class CrispWhiteProductLists extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/Reviews/product/items_list/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
