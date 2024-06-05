<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Module\XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 * @Extender\After("XC\FacebookMarketing")
 */
class PixelValue extends \XC\FacebookMarketing\View\Product\Details\Customer\PixelValue
{
    /**
     * @return string
     */
    protected function getFacebookPixelContentId()
    {
        if (
            $this->getAttributeValues()
            && $variant = $this->getProduct()->getVariant($this->getAttributeValues())
        ) {
            $result = $variant->getSku() ?: $variant->getVariantId();
        } else {
            $result = parent::getFacebookPixelContentId();
        }

        return $result;
    }
}
