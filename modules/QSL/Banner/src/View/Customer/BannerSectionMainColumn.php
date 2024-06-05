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
 * @ListChild (list="sidebar.first", zone="customer", weight="110")
 * @ListChild (list="sidebar.single", zone="customer", weight="110")
 */
class BannerSectionMainColumn extends \QSL\Banner\View\Customer\ABannerSection
{
    protected $location = 'MainColumn';

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && Layout::getInstance()->isSidebarFirstVisible();
    }
}
