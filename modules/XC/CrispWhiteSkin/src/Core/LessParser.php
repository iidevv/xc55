<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Layout manager
 * @Extender\Mixin
 */
class LessParser extends \XLite\Core\LessParser
{
    use ExecuteCachedTrait;

    /**
     * @return array
     */
    protected function getAdditionalLessData()
    {
        $additional_less_data = parent::getAdditionalLessData();

        return $this->executeCachedRuntime(static function () use ($additional_less_data) {
            $categoryImageSize = \XLite\Logic\ImageResize\Generator::getImageSizes(
                \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                'Default'
            );

            $logoSize = \XLite\Logic\ImageResize\Generator::getImageSizes(
                \XLite\Logic\ImageResize\Generator::MODEL_LOGO,
                'Default'
            );

            [$category_width, $category_height] = $categoryImageSize;
            [$logo_width, $logo_height] = $logoSize;

            return array_merge($additional_less_data, [
                'layout-category-image-width'  => $category_width . 'px',
                'layout-category-image-height' => $category_height . 'px',
                'logo-desktop-image-height'    => $logo_height . 'px',
                'logo-desktop-image-width'     => $logo_width . 'px',
            ]);
        });
    }
}
