<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\QSL\Banner\View\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Banner box widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\Banner")
 */
class BannerSectionWideBottomList extends \QSL\Banner\View\Customer\BannerSectionWideBottomList
{
    protected function isVisible()
    {
        return false;
    }
}
