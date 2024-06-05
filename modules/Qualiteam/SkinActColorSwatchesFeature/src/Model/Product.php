<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Model;

use XCart\Extender\Mapping\Extender as Extender;

/**
* @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    public function getColorSwatches(): array
    {
        $result = [];
        $attributes = $this->getEditableAttributes();

        if ($attributes) {

            /** @var \XLite\Model\Attribute $attribute */
            foreach ($attributes as $attribute) {
                if ($attribute->isColorSwatchesAttribute()) {

                    /** @var \QSL\ColorSwatches\Model\AttributeValue\AttributeValueSelect $value */
                    foreach ($attribute->getAttributeValue($this) as $value) {
                        $result[] = $value->getSwatch();
                    };
                }
            }
        }

        return $result;
    }
}