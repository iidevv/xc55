<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Model;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * View list decorator
 * @Extender\Mixin
 */
class ViewList extends \XLite\Model\ViewList
{
    /**
     * Check if this view list item will be rendered
     *
     * @return boolean
     */
    public function isDisplayed()
    {
        return ThemeTweaker::getInstance()->isInLayoutMode()
            || parent::isDisplayed();
    }
}
