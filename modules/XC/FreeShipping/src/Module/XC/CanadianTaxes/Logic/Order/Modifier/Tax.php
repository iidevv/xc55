<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Module\XC\CanadianTaxes\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Tax business logic
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\CanadianTaxes")
 */
class Tax extends \XC\CanadianTaxes\Logic\Order\Modifier\Tax
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return boolean
     */
    public function canShippingCalculated(\XLite\Model\OrderItem $item)
    {
        return $item->isShipForFree() ? false : parent::canShippingCalculated($item);
    }
}
