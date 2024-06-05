<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View\Product\Details\Customer;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 * @Extender\After("CDev\Wholesale")
 */
class ProductVariantQuantity extends \XLite\View\Product\Details\Customer\Quantity
{
    /**
     * Check if the product has wholesale price
     *
     * @return boolean
     */
    protected function hasWholesalePrice()
    {
        return $this->getProductVariant() && !$this->getProductVariant()->getDefaultPrice()
            ? Database::getRepo(ProductVariantWholesalePrice::class)->hasWholesalePrice($this->getProductVariant())
            : parent::hasWholesalePrice();
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-wholesale-quantity';
    }
}
