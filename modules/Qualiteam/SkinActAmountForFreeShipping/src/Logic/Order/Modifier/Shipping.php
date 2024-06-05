<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAmountForFreeShipping\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate shipping modifier
 * @Extender\Mixin
 */
class Shipping extends \XLite\Logic\Order\Modifier\Shipping
{
    /**
     * Prepare rates
     *
     * @param \XLite\Model\Shipping\Rate[] $rates
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function prepareFreeShippingModuleRates($rates)
    {
        $rates = parent::prepareFreeShippingModuleRates($rates);

        $needOnlyFreeShipping = true;

        foreach ($this->getItems() as $item) {
            if (!$item->isShipForFreeByCategoryAmount()) {
                $needOnlyFreeShipping = false;
                break;
            }
        }

        if ($needOnlyFreeShipping) {
            foreach ($rates as $n => $rate) {
                if (!$rate->getMethod()->getFree()) {
                    unset($rates[$n]);
                }
            }
        }

        return $rates;
    }
}
