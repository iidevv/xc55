<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * ACustomer
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/ColorSwatches/product/attribute_value/select/style.less',
            'merge' => 'bootstrap/css/bootstrap.less'
        ];

        return $list;
    }
}
