<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Module\CDev\GoogleAnalytics\Core\GA\Library;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\GoogleAnalytics")
 */
class GTag extends \CDev\GoogleAnalytics\Core\GA\Library\GTag
{
    protected static function ampWidgetClass(): string
    {
        return \QSL\AMP\Module\CDev\GoogleAnalytics\View\AmpWidget\GTag::class;
    }
}
