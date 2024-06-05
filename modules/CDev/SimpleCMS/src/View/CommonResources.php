<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * Return theme common files
     *
     * @param boolean $adminZone Admin zone flag OPTIONAL
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list                         = parent::getThemeFiles($adminZone);
        $list[static::RESOURCE_CSS][] = 'modules/CDev/SimpleCMS/page/style.css';

        return $list;
    }
}
