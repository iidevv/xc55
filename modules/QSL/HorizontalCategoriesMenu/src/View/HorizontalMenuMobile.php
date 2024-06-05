<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;

/**
 * Main menu
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\Mobile")
 */
abstract class HorizontalMenuMobile extends \QSL\HorizontalCategoriesMenu\View\HorizontalMenu
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !\XLite\Core\Request::getInstance()->isMobileDevice();
    }
}
