<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\FeaturedProducts")
 */
abstract class FeaturedProducts extends \CDev\FeaturedProducts\Controller\Admin\FeaturedProducts
{
    /**
     * @return string
     */
    public function getConciergeTitle()
    {
        return (\XLite\Core\Request::getInstance()->id ? 'Category' : 'Front page') . ': Featured products';
    }
}
