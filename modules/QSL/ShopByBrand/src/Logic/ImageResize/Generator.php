<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Logic\ImageResize;

use XCart\Extender\Mapping\Extender;

/**
 * ImageResize
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\ImageResize\Generator
{
    public const MODEL_BRAND = 'QSL\ShopByBrand\Model\Image\Brand\Image';

    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        $sizes                    = parent::defineImageSizes();
        $sizes[self::MODEL_BRAND] = [
            'Default' => [160, 160], // Brand thumbnail on the Brands page
        ];

        return $sizes;
    }

    /**
     * Get list of images sizes which administrator can edit via web interface
     *
     * @return array
     */
    public static function getEditableImageSizes()
    {
        $sizes                    = parent::getEditableImageSizes();
        $sizes[self::MODEL_BRAND] = [
            'Default',
        ];

        return $sizes;
    }
}
