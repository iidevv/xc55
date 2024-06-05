<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * @return bool
     */
    public function hasColorSwatchAttribute()
    {
        $attributes = $this->getEditableAttributes();

        if ($attributes) {
            foreach ($attributes as $attribute) {
                if ($attribute->isColorSwatchesAttribute()) {
                    return true;
                }
            }
        }

        return false;
    }
}
