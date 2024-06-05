<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\Module\XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("Qualiteam\SkinActPinterestPixel")
 */
class PinterestPixelPageVisit extends \Qualiteam\SkinActPinterestPixel\View\Product\Details\Customer\PinterestPixelPageVisit
{
    /**
     * @return string
     */
    protected function getUniqueContentId()
    {
        if (
            $this->getAttributeValues()
            && $variant = $this->getProduct()->getVariant($this->getAttributeValues())
        ) {
            $result = $variant->getSku() ?: $variant->getVariantId();
        } else {
            $result = parent::getUniqueContentId();
        }

        return $result;
    }
}
