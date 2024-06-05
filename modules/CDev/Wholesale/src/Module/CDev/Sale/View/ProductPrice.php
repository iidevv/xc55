<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class ProductPrice extends \CDev\Wholesale\View\ProductPrice
{
    /**
     * @param $firstWholesalePrice
     * @return mixed
     */
    protected function getZeroTierDisplayPrice($firstWholesalePrice)
    {
        $priceBeforeSale = $firstWholesalePrice->getOwner()->getNetPriceBeforeSale();
        return max(parent::getZeroTierDisplayPrice($firstWholesalePrice), $priceBeforeSale);
    }
}
