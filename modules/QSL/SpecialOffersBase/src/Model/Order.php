<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated order model
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Returns the order subtotal including per-item surcharges.
     *
     * @return float
     */
    public function getSpecialOffersSubtotal()
    {
        return $this->getSubtotal() + $this->getItemsSurchargesSum();
    }

    /**
     * SUM of all available items surcharges
     *
     * @return fload
     */
    protected function getItemsSurchargesSum()
    {
        $totalSurcharges = 0.0;
        /**
         * @var \XLite\Model\OrderItem[] $items
         */
        $items = $this->getItems();
        foreach ($items as $item) {
            /**
             * @var \XLite\Model\OrderItem\Surcharge[] $surcharges
             */
            $surcharges = $item->getSurcharges();
            foreach ($surcharges as $surcharge) {
                if ($surcharge->getAvailable()) {
                    $totalSurcharges += $surcharge->getValue();
                }
            }
        }

        return $totalSurcharges;
    }
}
