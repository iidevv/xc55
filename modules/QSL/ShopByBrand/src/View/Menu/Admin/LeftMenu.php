<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\TitleFromController;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        $list['catalog'][static::ITEM_CHILDREN]['brands'] = [
            static::ITEM_TITLE      => new TitleFromController('brands'),
            static::ITEM_TARGET     => 'brands',
            static::ITEM_WEIGHT     => 250,
        ];

        return $list;
    }
}
