<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class BrandProducts extends \XLite\View\AView
{
    /**
     * @return array|string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['brand_products']);
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * @return bool
     */
    protected function isSearchVisible()
    {
        return true;
    }
}
