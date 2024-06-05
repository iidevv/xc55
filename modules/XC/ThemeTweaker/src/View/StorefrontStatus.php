<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XLite;

/**
 * @Extender\Mixin
 */
class StorefrontStatus extends \XLite\View\StorefrontStatus
{
    /**
     * Get public shop URL
     *
     * @return string
     */
    protected function getOpenedShopURL()
    {
        $result = parent::getOpenedShopURL();
        if (ThemeTweakerPanel::isThemeTweakerEnabled()) {
            $result = XLite::getController()->buildURL(
                'theme_tweaker_switcher',
                '',
                [
                    'switch' => 'off',
                    'returnURL' => $result
                ]
            );
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $result = parent::getJSFiles();
        if (!ThemeTweakerPanel::isThemeTweakerEnabled()) {
            $result[] = 'modules/XC/ThemeTweaker/storefront_status.js';
        }
        return $result;
    }
}
