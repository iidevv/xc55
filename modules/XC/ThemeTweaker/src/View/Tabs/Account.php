<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * @return string
     */
    protected function getDefaultDisplayName()
    {
        return $this->getTitle();
    }
}
