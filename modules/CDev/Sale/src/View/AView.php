<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    /**
     * Return sale participation flag
     *
     * @param \XLite\Model\Product $product Product model
     *
     * @return boolean
     */
    protected function participateSaleAdmin(\XLite\Model\Product $product)
    {
        return $product->getParticipateSale() ||
            ($product->hasParticipateSale() && empty($product->getApplicableSaleDiscounts()));
    }

    /**
     * @param \CDev\Sale\Model\SaleDiscount $saleDiscount
     *
     * @return string
     */
    protected function getSaleDiscountEditLink(\CDev\Sale\Model\SaleDiscount $saleDiscount)
    {
        return $this->buildURL('sale_discount', '', ['id' => $saleDiscount->getId()]);
    }
}
