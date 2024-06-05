<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!($adminZone ?? \XLite::isAdminZone())) {
            $list[static::RESOURCE_JS][] = 'js/aos.min.js';
            $list[static::RESOURCE_JS][] = 'js/aos.init.js';
            $list[static::RESOURCE_CSS][] = 'css/aos.min.css';
        }

        return $list;
    }
}
