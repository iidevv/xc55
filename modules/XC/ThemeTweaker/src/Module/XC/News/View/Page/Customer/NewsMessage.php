<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\XC\News\View\Page\Customer;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\News")
 */
abstract class NewsMessage extends \XC\News\View\Page\Customer\NewsMessage implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    protected function getDefaultDisplayName()
    {
        return static::t('News message');
    }
}
