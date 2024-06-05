<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\ProductsCarousel\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ProductsCarousel")
 */
abstract class ACustomer extends \QSL\ProductsCarousel\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * @return string[]
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'file'  => 'modules/QSL/ProductsCarousel/css/style.less',
                'merge' => 'bootstrap/css/bootstrap.less',
            ]
        );
    }
}
