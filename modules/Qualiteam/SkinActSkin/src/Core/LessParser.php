<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Config;

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

        return array_merge(
            $additional_less_data,
            [
                'featured-products-bg'  => '#' . Config::getInstance()->QSL->ProductsCarousel->fp_carousel_bg_color,
                'recently-viewed-bg'    => '#' . Config::getInstance()->QSL->ProductsCarousel->rv_carousel_bg_color,
                'bb-products-bg'        => '#' . Config::getInstance()->QSL->ProductsCarousel->bb_carousel_bg_color,
                'coming-soon-bg'        => '#' . Config::getInstance()->QSL->ProductsCarousel->cs_carousel_bg_color,
                'new-arrivals-bg'       => '#' . Config::getInstance()->QSL->ProductsCarousel->na_carousel_bg_color,
            ]);
        //block-new-arrivals
    }
}
