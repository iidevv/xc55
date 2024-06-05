<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\TitleFromController;
use XLite\Model\Role\Permission;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['communications'][static::ITEM_CHILDREN]['back_in_stock_records'] = [
            static::ITEM_TITLE      => new TitleFromController('back_in_stock_records'),
            static::ITEM_TARGET     => 'back_in_stock_records',
            static::ITEM_PERMISSION => Permission::ROOT_ACCESS,
            static::ITEM_WEIGHT     => 150,
        ];

        return $items;
    }
}
