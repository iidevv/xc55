<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 *
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    protected function defineItems()
    {
        $list = parent::defineItems();

        $cnd = new \XLite\Core\CommonCell();

        $cnd->inProgress = true;

        $count = Database::getRepo(\XLite\Model\Order::class)->search($cnd, true) ?: false;

        if ($count > 0) {

            $list['sales'][static::ITEM_CHILDREN]['orders_in_progress'] = [
                static::ITEM_TITLE => static::t('SkinActCreateOrder In Progress'),
                static::ITEM_TARGET => 'orders_in_progress',
                static::ITEM_PERMISSION => 'manage orders',
                static::ITEM_WEIGHT => 101,
                static::ITEM_WIDGET => \Qualiteam\SkinActCreateOrder\View\Menu\Admin\InProgressOrdersCount::class
            ];

        }

        return $list;
    }
}
