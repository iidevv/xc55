<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\View\Tabs;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class FrontPage extends \XLite\View\Tabs\FrontPage
{
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if (Core::isProfileHasStaffAccess()) {
            unset($list['front_page']);
        }

        return $list;
    }

    protected function isTabsNavigationVisible()
    {
        if (Core::isProfileHasStaffAccess()) {
            return true;
        }

        return parent::isTabsNavigationVisible();
    }
}