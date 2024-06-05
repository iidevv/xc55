<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Module\XC\ThemeTweaker\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ThemeTweaker extends \XC\ThemeTweaker\Core\ThemeTweaker
{
    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isTargetAllowedInInlineEditorMode()
    {
        return parent::isTargetAllowedInInlineEditorMode()
            || \XLite\Core\Request::getInstance()->target == 'brand';
    }
}
