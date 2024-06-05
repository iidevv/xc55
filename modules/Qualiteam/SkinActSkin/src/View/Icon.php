<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View;

use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\AView;

/**
 * Inline icons
 */
class Icon extends AView
{
    public const PARAM_ICON = 'icon';

    public const PARAM_CSS_CLASS = 'class';

    public const PARAM_STROKE = 'stroke';

    public const PARAM_FILL = 'fill';

    public const PARAM_DIR = 'dir';

    protected function getDefaultTemplate(): string
    {
        return 'icon.twig';
    }

    protected function getIconFilePath(): string
    {

        $icon = $this->sanitizeIconName($this->getIconName());

        if (substr($icon, 0, 8) !== 'modules/') {
            return 'images/' . $icon . '.svg';
        }

        return $icon . '.svg';
    }

    public function getImageContent(): string
    {
        $result = $this->getSVGImage($this->getIconFilePath());

        return str_replace(
            '<svg ',
            '<svg class="' . $this->getCSSClass() . '" ',
            (string)$result
        );
    }

    public function getIconName(): string
    {
        return $this->getParam(static::PARAM_ICON);
    }

    protected function getIconsWithStroke(): array
    {
        return ['eye', 'eye-slash'];
    }

    protected function getCSSClass(): string
    {
        $iconName = $this->getIconName();
        $classes = [
            'inline-icon',
            'inline-icon--' . $iconName
        ];

        if ($extraClass = $this->getParam(static::PARAM_CSS_CLASS)) {
            $classes[] = $extraClass;
        }

        if ($this->getParam(static::PARAM_STROKE) || in_array($iconName, $this->getIconsWithStroke())) {
            $classes[] = 'inline-icon--stroke';
        }

        if ($this->getParam(static::PARAM_FILL)) {
            $classes[] = 'inline-icon--fill';
        }

        return implode(' ', $classes);
    }

    protected function sanitizeIconName(string $icon): string
    {
        return preg_replace(['/[^a-zA-Z0-9_\-\/.]/', '/\.\./'], '', $icon);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ICON => new TypeString('Icon name or path (w/o file extension)', ''),
            static::PARAM_CSS_CLASS => new TypeString('Icon extra CSS classes', ''),
            static::PARAM_STROKE => new TypeBool('Whether the stroke color depends on the text color', false),
            static::PARAM_FILL => new TypeBool('Whether the fill color depends on the text color', false),
            static::PARAM_DIR => new TypeString('Icon directory', 'icons')
        ];
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getParam(static::PARAM_ICON);
    }
}
