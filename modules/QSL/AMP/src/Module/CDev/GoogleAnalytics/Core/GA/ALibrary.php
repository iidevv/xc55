<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Module\CDev\GoogleAnalytics\Core\GA;

use QSL\AMP\Module\CDev\GoogleAnalytics\Core\GA\Interfaces\ILibrary;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\GoogleAnalytics")
 */
abstract class ALibrary extends \CDev\GoogleAnalytics\Core\GA\ALibrary implements ILibrary
{
    public function getAmpWidgetContent(): string
    {
        if ($w = $this->getWidget(static::ampWidgetClass(), $this->getAmpWidgetParams())) {
            return $w->getContent() ?? '';
        }

        return '';
    }

    protected function getAmpWidgetParams()
    {
        return $this->getTagWidgetParams();
    }

    protected static function ampWidgetClass(): string
    {
        return '';
    }
}
