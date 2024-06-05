<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\CDev\Coupons\View\Menu\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['promotions'][static::ITEM_CHILDREN]['coupons'] = $this->addItemPermission($items['promotions'][static::ITEM_CHILDREN]['coupons'], Core::FULFILLMENT_STAFF_ACCESS);

        return $items;
    }
}