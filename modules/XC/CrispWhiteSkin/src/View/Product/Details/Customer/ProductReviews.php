<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Reviews list widget
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\Reviews")
 */
class ProductReviews extends \XC\Reviews\View\Customer\ProductReviews
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/product-details.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
