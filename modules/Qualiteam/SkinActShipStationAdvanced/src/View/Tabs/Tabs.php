<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\View\Tabs;

use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\View\Tabs\ATabs;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Tabs extends ATabs
{
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            self::getTabsContainer()->getTargets(),
        );
    }

    protected static function getTabsContainer()
    {
        return Container::getContainer()->get('shipstation.tabs');
    }

    protected function defineTabs()
    {
        return self::getTabsContainer()->getTabs();
    }

    protected function isTabsNavigationVisible(): bool
    {
        return count($this->getTabs()) > 0;
    }
}