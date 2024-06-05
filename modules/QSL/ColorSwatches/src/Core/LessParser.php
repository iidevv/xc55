<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Config;

/**
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
        $additional_less_data      = parent::getAdditionalLessData();
        $color_swatches_box_width  = Config::getInstance()->QSL->ColorSwatches->box_width;
        $color_swatches_box_height = Config::getInstance()->QSL->ColorSwatches->box_height;

        return array_merge($additional_less_data, [
            'color-swatches-box-width'  => $color_swatches_box_width . 'px',
            'color-swatches-box-height' => $color_swatches_box_height . 'px',
        ]);
    }
}
