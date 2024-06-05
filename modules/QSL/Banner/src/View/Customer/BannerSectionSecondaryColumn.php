<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Customer;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Layout;

/**
 * Banner box widget
 *
 * @ListChild (list="sidebar.second", zone="customer", weight="110")
 * @ListChild (list="sidebar.single", zone="customer", weight="110")
 */
class BannerSectionSecondaryColumn extends \QSL\Banner\View\Customer\ABannerSection
{
    protected $location = 'SecondaryColumn';

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && Layout::getInstance()->isSidebarSecondVisible();
    }
}
