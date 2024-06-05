<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar;

use XCart\Extender\Mapping\Extender;

/**
 * DataSources
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class DataSources extends \XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSources
{
    protected function defineDataWidgets()
    {
        return array_merge(parent::defineDataWidgets(), [
            '\XC\ProductVariants\View\NotificationEditor\Sidebar\DataSource\ProductVariant',
        ]);
    }
}
