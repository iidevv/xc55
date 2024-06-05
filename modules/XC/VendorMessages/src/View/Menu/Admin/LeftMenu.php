<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

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

        $items['communications'][static::ITEM_CHILDREN]['messages'] = [
            static::ITEM_TITLE      => static::t('Order Messages'),
            static::ITEM_TARGET     => 'messages',
            static::ITEM_PERMISSION => 'manage conversations',
            static::ITEM_WEIGHT     => 150,
        ];

        return $items;
    }
}
