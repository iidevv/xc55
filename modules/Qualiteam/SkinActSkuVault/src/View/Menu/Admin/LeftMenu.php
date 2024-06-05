<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\Menu\Admin;

use Qualiteam\SkinActSkuVault\View\Tabs\SkuVault;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['store_setup'][static::ITEM_CHILDREN]['skuvault_settings'] = [
            static::ITEM_TITLE  => static::t('[SkuVault] skuvault_settings_left_menu_title'),
            static::ITEM_TARGET => SkuVault::TAB_GENERAL,
            static::ITEM_WEIGHT => 1000,
        ];

        return $items;
    }
}
