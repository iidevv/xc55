<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\View\Menu\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\After ("QSL\OrderReports")
 * @Extender\After ("QSL\AbandonedCartReminder")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (Auth::getInstance()->isPermissionAllowed(Core::FULFILLMENT_STAFF_ACCESS)) {
            $items['communications'][static::ITEM_CHILDREN]['customer_profiles'] = $this->addItemPermission($items['communications'][static::ITEM_CHILDREN]['customer_profiles'], Core::FULFILLMENT_STAFF_ACCESS);
        }

        if (Core::checkAnyStaffPermission()) {
            unset($items['sales'][static::ITEM_CHILDREN]['payment_transactions'], $items['reports']);
        }

        return $items;
    }
}
