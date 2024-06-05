<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\Logic;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * MoneyModificator: price with sale discount
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
class MoneyModificator extends \CDev\Sale\Logic\MoneyModificator
{
    /**
     * @param \XLite\Model\AEntity $model
     * @return bool
     */
    protected static function isApplyForWholesalePrices(\XLite\Model\AEntity $model)
    {
        $product = static::getObject($model);

        if ($product instanceof Product) {
            return !$product->isWholesalePricesEnabled()
                || (
                    $product->getApplySaleToWholesale()
                    && $product->getDiscountType() === Product::SALE_DISCOUNT_TYPE_PERCENT
                )
                || !$product->getWholesalePrices()
                || $product->getWholesaleQuantity() <= 1
                || $product->isFirstWholesaleTier();
        }

        return parent::isApplyForWholesalePrices($model);
    }
}
