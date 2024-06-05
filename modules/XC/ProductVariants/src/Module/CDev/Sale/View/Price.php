<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * Product price
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class Price extends \XLite\View\Price
{
    /**
     * Return old price value
     *
     * @return float
     */
    protected function getOldPrice()
    {
        return $this->getProductVariant()
            ? \XLite::getInstance()->getCurrency()->roundValue($this->getProductVariant()->getDisplayPriceBeforeSale())
            : parent::getOldPrice();
    }

    /**
     * Return old price value without possible market price
     *
     * @return float
     */
    protected function getPureOldPrice()
    {
        return $this->getProductVariant()
            ? $this->getOldPrice()
            : parent::getPureOldPrice();
    }
}
