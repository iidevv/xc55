<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\View\StickyPanel\Product\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class AAdmin extends \XLite\View\StickyPanel\Product\Admin\AAdmin
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