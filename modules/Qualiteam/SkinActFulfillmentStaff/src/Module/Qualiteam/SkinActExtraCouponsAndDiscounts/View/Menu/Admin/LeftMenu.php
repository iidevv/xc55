<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\Qualiteam\SkinActExtraCouponsAndDiscounts\View\Menu\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActProMembership")
 * @Extender\Depend("Qualiteam\SkinActExtraCouponsAndDiscounts")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['pro_membership'][static::ITEM_CHILDREN]['extra_coupons_and_discounts'] = $this->addItemPermission($items['pro_membership'][static::ITEM_CHILDREN]['extra_coupons_and_discounts'], Core::FULFILLMENT_STAFF_ACCESS);

        return $items;
    }
}