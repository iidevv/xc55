<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check if Pinterest Pixel enabled
     *
     * @return bool
     */
    public static function isPixelEnabled(): bool
    {
        return (bool) \XLite\Core\Config::getInstance()->Qualiteam->SkinActPinterestPixel->script_code;
    }

    /**
     * Get module path
     */
    public static function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActPinterestPixel';
    }

    /**
     * Pinterest Pixel URL
     */
    public static function getPixelUrl(): string
    {
        return 'https://ct.pinterest.com/v3/';
    }
}