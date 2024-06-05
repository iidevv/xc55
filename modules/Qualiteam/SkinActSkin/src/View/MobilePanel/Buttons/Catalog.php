<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\MobilePanel\Buttons;

use Qualiteam\SkinActSkin\View\MobilePanel\PanelButton;
use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActSkin\Main;

/**
 * Home button (mobile bottom panel)
 *
 * @ListChild (list="mobile-panel.list", weight="20")
 */
class Catalog extends PanelButton
{
    protected function isVisible()
    {
        return parent::isVisible() && Main::isStoreHasCategories();
    }

    public function getIcon(): string
    {
        return parent::getIcon() ?: 'i-catalog';
    }

    public function getTitle()
    {
        return parent::getTitle() ?: static::t('Catalog');
    }

    public function getButtonCSSClasses(): string
    {
        return parent::getButtonCSSClasses() . ' mobile-panel__button--type--catalog';
    }
}
