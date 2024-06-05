<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("!QSL\ProductFeeds")
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['catalog'])) {
            $list['catalog'][static::ITEM_CHILDREN]['google_shopping_groups'] = [
                static::ITEM_TITLE  => static::t('Feeds'),
                static::ITEM_TARGET => 'google_shopping_groups',
                static::ITEM_WEIGHT => 1000,
            ];
        }

        return $list;
    }
}
