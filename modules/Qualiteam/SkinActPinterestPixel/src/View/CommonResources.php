<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\View;

use Qualiteam\SkinActPinterestPixel\Main;
use Qualiteam\SkinActPinterestPixel\View\PixelScripts\CommonScripts;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (Main::isPixelEnabled()) {
            $list[static::RESOURCE_JS][] = Main::getModulePath() . '/pinterest_core.js';
            $list[static::RESOURCE_JS][] = Main::getModulePath() . '/pinterest_event.js';
        }

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (Main::isPixelEnabled() && !\XLite::isAdminZone()) {
            $list = array_merge($list, CommonScripts::getInstance()->getPinterestPixelScripts());
        }

        return $list;
    }
}