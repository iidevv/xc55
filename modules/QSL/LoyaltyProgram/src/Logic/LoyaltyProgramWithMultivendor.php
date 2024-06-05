<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic;

use XCart\Extender\Mapping\Extender;

/**
 * Class handling common Loyalty Program function.
 *
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiVendor", "QSL\LoyaltyProgram"})
 */
class LoyaltyProgramWithMultivendor extends \QSL\LoyaltyProgram\Logic\LoyaltyProgram
{
    /**
     * Returns the base amount that reward discount should be calculated for.
     *
     * @param \XLite\Model\Order $order Order
     * @param integer            $mode  Apply mode (see ApplyDiscountTo class constants) OPTIONAL
     *
     * @return float
     */
    public function getRewardDiscountBase(\XLite\Model\Order $order, $mode = null)
    {
        if (!\XC\MultiVendor\Main::isWarehouseMode() && $order->isParent()) {
            $total = 0;
            foreach ($order->getChildren() as $child) {
                $total += $this->getRewardDiscountBase($child, $mode);
            }
        } else {
            $total = parent::getRewardDiscountBase($order, $mode);
        }

        return $total;
    }
}
