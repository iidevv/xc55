<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Image settings model
 * @Extender\Mixin
 */
class ImageSettings extends \XLite\Model\ImageSettings
{
    /**
     * Get list of available image size types
     *
     * @return array
     */
    protected function getImageTypes()
    {
        $types                                                  = parent::getImageTypes();
        $types[\XLite\Logic\ImageResize\Generator::MODEL_BRAND] = 'brand';

        return $types;
    }
}
