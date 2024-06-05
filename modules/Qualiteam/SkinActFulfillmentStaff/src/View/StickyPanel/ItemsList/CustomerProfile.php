<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\View\StickyPanel\ItemsList;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class CustomerProfile extends \XLite\View\StickyPanel\ItemsList\CustomerProfile
{
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        if (Core::isProfileHasStaffAccess()) {
            unset($list['export']);
        }
        return $list;
    }
}