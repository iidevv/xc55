<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
abstract class Menus extends \CDev\SimpleCMS\Controller\Admin\Menus
{
    /**
     * @return string
     */
    public function getConciergeTitle()
    {
        return 'Menus';
    }
}
