<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\XC\ProductVariants\Model;

use XC\ProductVariants\Model\ProductVariant;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class Product extends \XLite\Model\Product
{
    public function setRuntimeDefaultVariant(ProductVariant $defaultVariant): void
    {
        $this->defaultVariant = $defaultVariant;
    }
}
