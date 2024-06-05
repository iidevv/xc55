<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Logic\ImageResize;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\ImageResize\Generator
{
    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        $result = parent::defineImageSizes();
        $result[static::MODEL_PRODUCT]['RecentlyViewedThumbnail'] = [120, 120];

        return $result;
    }
}
