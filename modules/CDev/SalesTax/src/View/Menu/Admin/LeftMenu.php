<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\Menu\Admin;

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

        if (isset($items['store_setup'][static::ITEM_CHILDREN]['tax_classes'])) {
            $items['store_setup'][static::ITEM_CHILDREN]['tax_classes'][static::ITEM_TARGET] = 'sales_tax';
        }

        return $items;
    }
}
