<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\QSL\Banner\View\Menu\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\Banner")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['store_design'][static::ITEM_CHILDREN]['banners_list'] = $this->addItemPermission($items['store_design'][static::ITEM_CHILDREN]['banners_list'], Core::FULFILLMENT_STAFF_ACCESS);

        return $items;
    }
}