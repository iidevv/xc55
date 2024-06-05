<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * @return string
     */
    protected function getFacebookPixelProductSku()
    {
        return $this->getProduct()->getSku();
    }
}
