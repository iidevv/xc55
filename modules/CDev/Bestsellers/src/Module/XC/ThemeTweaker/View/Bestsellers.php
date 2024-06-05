<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\Module\XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
abstract class Bestsellers extends \CDev\Bestsellers\View\Bestsellers implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * Returns default display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ? static::DISPLAY_MODE_STHUMB
            : parent::getDisplayMode();
    }

    /**
     * Get current widget type parameter
     *
     * @return boolean
     */
    protected function getWidgetType()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ? static::WIDGET_TYPE_SIDEBAR
            : static::WIDGET_TYPE_CENTER;
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutHelpMessage(): string
    {
        return static::t('List of products in this block is generated automatically. Read more about Promotional Blocks.');
    }
}
