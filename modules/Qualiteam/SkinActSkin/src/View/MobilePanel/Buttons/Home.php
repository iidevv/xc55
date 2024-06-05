<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\MobilePanel\Buttons;

use Qualiteam\SkinActSkin\View\MobilePanel\PanelButton;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Converter;

/**
 * Home button (mobile bottom panel)
 *
 * @ListChild (list="mobile-panel.list", weight="10")
 */
class Home extends PanelButton
{
    protected function isActive(): bool
    {
        return \XLite::getController()->getTarget() === 'main';
    }

    public function getIcon(): string
    {
        return parent::getIcon() ?: 'i-home';
    }

    public function getTitle()
    {
        return parent::getTitle() ?: static::t('Home');
    }

    public function getButtonCSSClasses(): string
    {
        return parent::getButtonCSSClasses() . ' mobile-panel__button--type--home';
    }

    public function getURL(): string
    {
        $url = \XLite::getInstance()->getShopURL(Converter::buildURL(
            'main',
            '',
            [],
            \XLite::getCustomerScript()
        ));

        return parent::getURL() ?: $url;
    }
}
