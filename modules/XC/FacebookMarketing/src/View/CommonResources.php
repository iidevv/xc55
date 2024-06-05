<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View;

use XCart\Extender\Mapping\Extender;
use XC\FacebookMarketing\View\PixelScripts\CommonScripts;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (\XC\FacebookMarketing\Main::isPixelEnabled()) {
            $list[static::RESOURCE_JS][] = 'modules/XC/FacebookMarketing/pixel_core.js';
            $list[static::RESOURCE_JS][] = 'modules/XC/FacebookMarketing/pixel_event.js';
        }

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XC\FacebookMarketing\Main::isPixelEnabled() && !\XLite::isAdminZone()) {
            $list = array_merge($list, CommonScripts::getInstance()->getFacebookPixelScripts());
        }

        return $list;
    }
}
