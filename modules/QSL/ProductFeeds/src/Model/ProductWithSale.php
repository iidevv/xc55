<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated product model.
 *
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\Sale", "QSL\ProductFeeds"})
 */
class ProductWithSale extends \XLite\Model\Product
{
    /**
     * Get VAT Price
     *
     * @return float
     */
    public function getVatPriceBeforeSale()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getDisplayPriceBeforeSale', ['taxable'], 'vat');
    }
}
