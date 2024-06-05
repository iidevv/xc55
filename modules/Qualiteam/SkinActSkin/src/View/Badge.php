<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View;

use XLite\Model\WidgetParam\TypeString;

/**
 * Inline icons
 */
class Badge extends \XLite\View\AView
{
    public const PARAM_TEXT = 'text';

    public const PARAM_CLASS = 'class';

    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TEXT  => new TypeString('Badge text', ''),
            static::PARAM_CLASS => new TypeString('Extra CSS classes', '')
        ];
    }

    protected function getDefaultTemplate(): string
    {
        return 'layout/mobile-panel/badge.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getParam(static::PARAM_TEXT);
    }

    public function getText(): string
    {
        return $this->getParam(static::PARAM_TEXT);
    }

    public function getCSSClasses(): string
    {
        $extraClasses = $this->getParam(static::PARAM_CLASS);

        return 'badge' . ($extraClasses ? " {$extraClasses}" : '');
    }
}
