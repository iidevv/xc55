<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Model\AttributeValue;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 * @Extender\After ("XC\ProductVariants")
 */
class AttributeValueSelect extends \XLite\Model\AttributeValue\AttributeValueSelect
{
    /**
     * @return boolean
     */
    public function isVariantAvailable()
    {
        return $this->variantAvailable ?: $this->variants->count() && $this->getProduct()->getIsAvailableForBackorder();
    }
}
