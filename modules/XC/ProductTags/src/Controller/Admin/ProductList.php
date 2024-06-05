<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product list controller
 *
 * @Extender\Mixin
 */
class ProductList extends \XLite\Controller\Admin\ProductList
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_unique(array_merge(parent::defineFreeFormIdActions(), ['search']));
    }
}
