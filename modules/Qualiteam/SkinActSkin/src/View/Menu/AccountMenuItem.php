<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu;

use XLite\Core\Translation\Label;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\AView;

class AccountMenuItem extends AView
{
    public const PARAM_CLASS = 'class';

    public const PARAM_URL = 'url';

    public const PARAM_ICON = 'icon';

    public const PARAM_TITLE = 'title';

    public const PARAM_BADGE = 'badge';

    public const PARAM_LINK_CLASS = 'linkClass';

    public const PARAM_ENABLED = 'enabled';

    public const ROOT_CSS_CLASS = 'account-links__list-item';

    public const PARAM_BADGE_CLASS = 'badgeClass';

    protected function getDefaultTemplate(): string
    {
        return 'layout/header/mobile_header_parts/account/link.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CLASS       => new TypeString('Additional CSS class', ''),
            static::PARAM_LINK_CLASS  => new TypeString('Additional CSS class for the link', ''),
            static::PARAM_URL         => new TypeString('Menu item URL', ''),
            static::PARAM_ICON        => new TypeString('Menu item icon', ''),
            static::PARAM_TITLE       => new TypeString('Menu item title', ''),
            static::PARAM_BADGE       => new TypeString('Content for the right column', ''),
            static::PARAM_ENABLED     => new TypeBool('Whether the menu item is enabled', true),
            static::PARAM_BADGE_CLASS => new TypeString('Badge extra CSS class', ''),
        ];
    }

    public function getCSSClass(): string
    {
        $result = static::ROOT_CSS_CLASS;

        if (!$this->isEnabled()) {
            $result .= ' ' . static::ROOT_CSS_CLASS . '--disabled';
        }

        if ($additionalClasses = $this->getParam(static::PARAM_CLASS)) {
            $result .= " {$additionalClasses}";
        }

        return $result;
    }

    public function getURL(): string
    {
        return $this->getParam(static::PARAM_URL);
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

    public function getBadge(): string
    {
        return $this->getParam(static::PARAM_BADGE);
    }

    public function isEnabled(): bool
    {
        return $this->getParam(self::PARAM_ENABLED);
    }

    public function getLinkCSSClass(): string
    {
        $linkClass = $this->getParam(static::PARAM_LINK_CLASS);

        return static::ROOT_CSS_CLASS . '-link' . ($linkClass ? " {$linkClass}" : '');
    }

    public function getBadgeCSSClass(): string
    {
        $extraClass = $this->getParam(static::PARAM_BADGE_CLASS);

        return 'account-links__list-item-badge' . ($extraClass ? " {$extraClass}" : '');
    }
}
