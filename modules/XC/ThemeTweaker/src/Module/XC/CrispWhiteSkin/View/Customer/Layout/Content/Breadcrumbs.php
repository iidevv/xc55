<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\XC\CrispWhiteSkin\View\Customer\Layout\Content;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\CrispWhiteSkin")
 */
class Breadcrumbs extends \XC\CrispWhiteSkin\View\Customer\Layout\Content\Breadcrumbs
{
    protected function getGroupName(): string
    {
        return static::t('Breadcrumbs');
    }
}
