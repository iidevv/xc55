<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Quantity widget
 * @Extender\Mixin
 */
class Quantity extends \XLite\View\Product\Details\Customer\Quantity
{
    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        $productVariant = $this->getProductVariant();

        return $productVariant
            ? $productVariant->getAvailableAmount() - $productVariant->getItemsInCart()
            : parent::getMaxQuantity();
    }
}
