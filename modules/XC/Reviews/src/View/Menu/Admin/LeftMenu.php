<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Menu\Admin;

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
        $list = parent::defineItems();

        if (isset($list['communications'])) {
            $list['communications'][static::ITEM_CHILDREN]['reviews'] = [
                static::ITEM_TITLE  => static::t('Reviews'),
                static::ITEM_TARGET => 'reviews',
                static::ITEM_PERMISSION => 'manage reviews',
                static::ITEM_WEIGHT => 170,
            ];
        }

        return $list;
    }
}
