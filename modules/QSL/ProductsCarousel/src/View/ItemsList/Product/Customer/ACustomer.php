<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductsCarousel\View\ItemsList\Product\Customer;

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
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/ProductsCarousel/lib/owl.carousel.js';
        $list[] = 'modules/QSL/ProductsCarousel/js/script.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductsCarousel/style/owl.carousel.css';
        $list[] = 'modules/QSL/ProductsCarousel/style/owl.theme.css';

        $list[] = [
            'file'  => 'modules/QSL/ProductsCarousel/style/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
