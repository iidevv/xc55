<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes
 * @Extender\Mixin
 */
class CommonAttributes extends \XLite\View\Product\Details\Customer\CommonAttributes
{
    /**
     * Return SKU of product
     *
     * @return string
     */
    protected function getSKU()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getDisplaySku()
            : parent::getSKU();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $variantId = $this->getProductVariant() ? $this->getProductVariant()->getId() : null;
        $list[] = $variantId;

        return $list;
    }

    /**
     * Return weight of product
     *
     * @return float
     */
    protected function getClearWeight()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getClearWeight()
            : parent::getClearWeight();
    }
}
