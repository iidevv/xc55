<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\MobilePanel;

use Qualiteam\SkinActSkin\Main;
use XLite\Core\Translation\Label;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\AView;

class PanelButton extends AView
{
    public const PARAM_ICON = 'icon';

    public const PARAM_TITLE = 'title';

    public const PARAM_CLASS = 'class';

    public const PARAM_BADGE = 'badge';

    public const PARAM_ACTIVE = 'active';

    public const PARAM_URL    = 'url';

    protected function isActive(): bool
    {
        return $this->getParam(static::PARAM_ACTIVE);
    }

    protected function getButtonCSSClasses(): string
    {
        $classes = [ 'mobile-panel__button' ];

        if ($this->isActive()) {
            $classes[] = 'mobile-panel__button--state--active';
        }

        if ($extraClass = $this->getParam(static::PARAM_CLASS)) {
            $classes[] = $extraClass;
        }

        return implode(' ', $classes);
    }

    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ICON   => new TypeString('Button icon', ''),
            static::PARAM_TITLE  => new TypeString('Button title', ''),
            static::PARAM_CLASS  => new TypeString('Button extra CSS class', ''),
            static::PARAM_BADGE  => new TypeString('Badge text', ''),
            static::PARAM_ACTIVE => new TypeBool('Whether the button is active', false),
            static::PARAM_URL    => new TypeString('Button link', '')
        ];
    }

    protected function getDefaultTemplate(): string
    {
        return 'layout/mobile-panel/' . ($this->getURL() ? 'link' : 'button') . '.twig';
    }

    public function getBadge(): string
    {
        return $this->getParam(static::PARAM_BADGE);
    }

    public function getURL(): string
    {
        return (string)$this->getParam(static::PARAM_URL);
    }

    public function getIcon(): string
    {
        return $this->getParam(static::PARAM_ICON);
    }

    /**
     * @return string|Label
     */
    public function getTitle()
    {
        return $this->getParam(static::PARAM_TITLE);
    }

    public function getCommentedData(): array
    {
        return [];
    }

    protected function getButtonAttributes(): array
    {
        return [];
    }

    public function getButtonAttributesHTML(): string
    {
        return Main::convertAttributesToHTMLString($this->getButtonAttributes());
    }
}
