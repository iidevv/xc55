<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Feeds extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [];
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return bool
     */
    protected function isTabsNavigationVisible()
    {
        return count($this->getTabs()) > 0;
    }
}
