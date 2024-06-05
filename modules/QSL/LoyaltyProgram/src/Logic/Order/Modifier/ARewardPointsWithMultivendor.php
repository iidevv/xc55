<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiVendor", "QSL\LoyaltyProgram"})
 */
abstract class ARewardPointsWithMultivendor extends \QSL\LoyaltyProgram\Logic\Order\Modifier\ARewardPoints
{
    /**
     * Calculate.
     *
     * Calculate for end orders only (parent for warehouse mode and children
     * for non warehouse mode).
     */
    public function calculate()
    {
        if ($this->getOrder()->isRewardPointsEnabledOrderPart()) {
            return parent::calculate();
        }
    }

    /**
     * Returns the number of redeemed reward points.
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return integer
     */
    protected function getSurchargeInfoNumberOfPoints(\XLite\Model\Base\Surcharge $surcharge)
    {
        $order = $surcharge->getOrder() ?: $this->getOrder();

        if ($order instanceof \XLite\Model\Cart && !\XC\MultiVendor\Main::isWarehouseMode() && $order->isChild()) {
            $points = $order->getParent()->getRedeemedRewardPoints();
        } else {
            $points = parent::getSurchargeInfoNumberOfPoints($surcharge);
        }

        return $points;
    }

    /**
     * Check for suitable discount
     *
     * @return boolean
     */
    protected function hasDiscount()
    {
        return $this->getOrder()->isRewardPointsEnabledOrderPart()
            && parent::hasDiscount();
    }
}
