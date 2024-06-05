<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Logic\ImageResize;

use XCart\Extender\Mapping\Extender;

/**
 * ImageResize
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\ImageResize\Generator
{
    public const MODEL_PRODUCT_VARIANT = 'XC\ProductVariants\Model\Image\ProductVariant\Image';

    /**
     * Returns dimensions for given class
     *
     * @param string $class Class
     *
     * @return array
     */
    public static function getModelImageSizes($class)
    {
        if ($class === static::MODEL_PRODUCT_VARIANT) {
            $class = static::MODEL_PRODUCT;
        }

        return parent::getModelImageSizes($class);
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        $list = parent::defineSteps();
        $list[] = 'XC\ProductVariants\Logic\ImageResize\Step\ProductVariants';

        return $list;
    }
}
