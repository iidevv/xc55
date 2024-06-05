<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\MarketPrice")
 */
abstract class PriceMarketPrice extends \XLite\View\Price
{
    /**
     * Determine if we need to display product market price
     *
     * @return boolean
     */
    protected function isShowMarketPrice()
    {
        return !$this->participateSale() && parent::isShowMarketPrice();
    }
}
