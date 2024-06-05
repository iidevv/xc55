<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\MobilePanel\Buttons;

use Qualiteam\SkinActSkin\Main;
use Qualiteam\SkinActSkin\View\MobilePanel\PanelButton;
use XCart\Extender\Mapping\ListChild;

/**
 * Cart button (mobile bottom panel)
 *
 * @ListChild (list="mobile-panel.list", weight="40")
 */
class Cart extends PanelButton
{
    protected function isActive(): bool
    {
        return parent::isActive() || \XLite::getController()->getTarget() === 'cart';
    }

    public function getIcon(): string
    {
        return parent::getIcon() ?: 'i-cart';
    }

    public function getTitle()
    {
        return parent::getTitle() ?: static::t('Cart');
    }

    public function getBadge(): string
    {
        return Main::getProductsCountForBadge();
    }

    public function getButtonCSSClasses(): string
    {
        return parent::getButtonCSSClasses() . ' mobile-panel__button--type--cart';
    }
}
