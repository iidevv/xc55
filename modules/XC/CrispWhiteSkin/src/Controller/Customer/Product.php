<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * Check if additional mobile breadcrumbs are shown
     *
     * @return boolean
     */
    public function isShowAdditionalMobileBreadcrumbs()
    {
        return true;
    }
}
