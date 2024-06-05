<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View;

use XCart\Extender\Mapping\Extender;

/**
 * Cart
 * @Extender\Mixin
 */
class Cart extends \XLite\View\Cart
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FreeShipping/label/style.css';

        return $list;
    }
}
