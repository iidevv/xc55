<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\View\DTO\RenderedWidget;

abstract class ASingleView extends \XLite\View\AView
{
    /**
     * @var bool
     */
    protected static $isDisplayedAlready = false;

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !self::$isDisplayedAlready;
    }

    protected function prepareTemplateDisplay($template)
    {
        self::$isDisplayedAlready = true;

        return parent::prepareTemplateDisplay($template);
    }


    /**
     * @param RenderedWidget $widget
     */
    protected function displayRenderedWidget(RenderedWidget $widget)
    {
        parent::displayRenderedWidget($widget);

        self::$isDisplayedAlready = true;
    }
}
