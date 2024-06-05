<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\Menu\Admin;

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

        $list['promotions'][static::ITEM_CHILDREN]['product_stickers'] = [
            self::ITEM_TITLE      => static::t('Product stickers'),
            self::ITEM_TARGET     => 'product_stickers',
            self::ITEM_PERMISSION => 'manage product stickers',
            self::ITEM_WEIGHT     => 400,
        ];

        return $list;
    }
}
