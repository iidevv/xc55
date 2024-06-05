<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to category section
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Brands extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return [
            'brands'
        ];
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'brands' => [
                'weight'   => 100,
                'title'    => static::t('Brands'),
                'widget' => 'QSL\ShopByBrand\View\Brands',
            ]
        ];
    }
}
