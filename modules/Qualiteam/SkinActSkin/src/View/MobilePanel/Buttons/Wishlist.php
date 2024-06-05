<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\MobilePanel\Buttons;

use Qualiteam\SkinActSkin\View\MobilePanel\PanelButton;
use XCart\Extender\Mapping\ListChild;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;

/**
 * Home button (mobile bottom panel)
 *
 * @Extender\Depend("CDev\SimpleCMS")
 * @ListChild (list="mobile-panel.list", weight="40")
 */
class Wishlist extends PanelButton
{
    protected function isActive(): bool
    {
        return \XLite::getController()->getTarget() === 'wishlist';
    }

    public function getIcon(): string
    {
        return parent::getIcon() ?: 'i-heart';
    }

    public function getTitle()
    {
        return parent::getTitle() ?: static::t('Wish List');
    }

    public function getButtonCSSClasses(): string
    {
        return parent::getButtonCSSClasses() . ' mobile-panel__button--type--wishlist';
    }

    public function getURL(): string
    {
        $url = \XLite::getInstance()->getShopURL(Converter::buildURL(
            'wishlist',
            '',
            [],
            \XLite::getCustomerScript()
        ));

        return parent::getURL() ?: $url;
    }
}
