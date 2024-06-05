<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("!XC\CustomOrderStatuses")
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['sales'])) {
            $list['sales'][static::ITEM_CHILDREN]['order_statuses'] = [
                static::ITEM_TITLE      => static::t('Order settings'),
                static::ITEM_TARGET     => 'return_reasons',
                static::ITEM_PERMISSION => 'manage orders',
                static::ITEM_WEIGHT     => 250,
            ];
        }

        return $list;
    }
}
