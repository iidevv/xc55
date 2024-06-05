<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiVendor", "QSL\ProductQuestions"})
 */
class LeftMenuWithMultiVendor extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (\XLite\Core\Auth::getInstance()->isVendor()) {
            $items['communications'][self::ITEM_CHILDREN]['product_questions'] = $this->addItemPermission($items['communications'][self::ITEM_CHILDREN]['product_questions'], '[vendor] manage catalog');
        }

        return $items;
    }
}
