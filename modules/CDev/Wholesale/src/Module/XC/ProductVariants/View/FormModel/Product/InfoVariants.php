<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Decorator Info
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class InfoVariants extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return string
     */
    protected function getPriceDescriptionTemplate()
    {
        /** @var \CDev\Wholesale\Model\Product $product */
        $product = $this->getProductEntity();

        if (
            $product
            && $product->hasVariants()
            && $product->isWholesalePricesEnabled()
            && count($product->getWholesalePrices()) > 0
        ) {
            return 'modules/CDev/Wholesale/form_model/product/info/wholesale_variants_defined_link.twig';
        } elseif (
            $product
            && $product->isWholesalePricesEnabled()
            && count($product->getWholesalePrices()) > 0
        ) {
            return 'modules/CDev/Wholesale/form_model/product/info/wholesale_defined_link.twig';
        }

        return parent::getPriceDescriptionTemplate();
    }
}
