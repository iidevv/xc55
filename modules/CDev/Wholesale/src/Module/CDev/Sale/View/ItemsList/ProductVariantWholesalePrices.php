<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\CDev\Sale\View\ItemsList;

use CDev\Sale\Model\Product;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class ProductVariantWholesalePrices extends \CDev\Wholesale\Module\XC\ProductVariants\View\ItemsList\ProductVariantWholesalePrices
{
    /**
     * @return bool
     */
    protected function isOnAbsoluteSale()
    {
        $hasSpecificSale = !$this->getProductVariant()->getDefaultSale()
            || $this->getProduct()->getParticipateSale();
        $saleDiscountType = !$this->getProductVariant()->getDefaultSale()
            ? $this->getProductVariant()->getDiscountType()
            : $this->getProduct()->getDiscountType();

        return $hasSpecificSale
                && $saleDiscountType === Product::SALE_DISCOUNT_TYPE_PRICE;
    }
}
