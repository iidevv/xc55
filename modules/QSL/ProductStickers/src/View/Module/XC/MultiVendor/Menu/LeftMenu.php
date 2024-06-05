<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\Module\XC\MultiVendor\Menu;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (Auth::getInstance()->isVendor() && \XLite\Core\Config::getInstance()->QSL->ProductStickers->vendor_stickers) {
            $items['catalog'][self::ITEM_CHILDREN]['product_stickers'] = [
                self::ITEM_TITLE      => static::t('Product stickers'),
                self::ITEM_TARGET     => 'product_stickers',
                self::ITEM_PERMISSION => '[vendor] manage catalog',
                self::ITEM_WEIGHT     => 300,
            ];
        }

        return $items;
    }
}
