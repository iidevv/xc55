<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Module\XC\ProductVariants\Model;

use XCart\Extender\Mapping\Extender;

/**
 * The "product" model class
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 * @Extender\After("XC\FacebookMarketing")
 */
class Product extends \XLite\Model\Product
{
    /**
     * Return product identifier for facebook pixel
     *
     * @return string
     */
    public function getFacebookPixelProductIdentifier()
    {
        $result = parent::getSku();

        if ($this->hasVariants() && $variant = $this->getDefaultVariant()) {
            $result = $variant->getSku() ?: $variant->getVariantId();
        }

        return $result;
    }
}
