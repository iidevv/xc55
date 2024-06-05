<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\View\FormField;

use CDev\Sale\Model\Product;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants","CDev\Wholesale"})
 */
class WholesalePrices extends \CDev\Wholesale\Module\XC\ProductVariants\View\FormField\WholesalePrices
{
    /**
     * @return bool
     */
    protected function isVariantOnAbsoluteSale()
    {
        $hasSpecificSale = !$this->getEntity()->getDefaultSale()
            || $this->getEntity()->getProduct()->getParticipateSale();
        $saleDiscountType = !$this->getEntity()->getDefaultSale()
            ? $this->getEntity()->getDiscountType()
            : $this->getEntity()->getProduct()->getDiscountType();

        return $hasSpecificSale
            && $saleDiscountType === Product::SALE_DISCOUNT_TYPE_PRICE;
    }

    /**
     * @return bool
     */
    protected function isWholesaleNotAllowed()
    {
        return parent::isWholesaleNotAllowed()
            || $this->isVariantOnAbsoluteSale();
    }

    /**
     * @return string
     */
    protected function getWholesaleNotAllowedMessage()
    {
        if ($this->isVariantOnAbsoluteSale()) {
            $salePrice = $this->getEntity()->getDefaultSale()
                ? $this->getEntity()->getProduct()->getSalePriceValue()
                : $this->getEntity()->getSalePriceValue();

            return static::t(
                'Wholesale prices for this product variant are disabled because its sale price is set as an absolute value (X). To enable wholesale prices, use the relative value in % for the Sale field.',
                ['price' => $this->formatPrice($salePrice)]
            );
        }

        return parent::getWholesaleNotAllowedMessage();
    }
}
