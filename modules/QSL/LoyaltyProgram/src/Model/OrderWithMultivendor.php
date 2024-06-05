<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Order model
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\LoyaltyProgram", "XC\MultiVendor"})
 */
class OrderWithMultivendor extends \XLite\Model\Order
{
    /**
     * Get the number of reward points the user will earn for the order.
     *
     * @return integer
     */
    public function getCalculatedRewardPoints()
    {
        return !\XC\MultiVendor\Main::isWarehouseMode() && $this->isParent()
            ? $this->calculateChildRewardPoints()
            : parent::getCalculatedRewardPoints();
    }

    /**
     * Get the number of reward points the user will earn for the order.
     *
     * @return integer
     */
    public function getRedeemedRewardPoints()
    {
        return !\XC\MultiVendor\Main::isWarehouseMode() && $this->isParent()
            ? $this->calculateChildRedeemedPoints()
            : parent::getRedeemedRewardPoints();
    }

    /**
     * Get the maximum number of reward points the shopper wants to redeem with the order.
     * A negative value means "no limit".
     *
     * @return integer
     */
    public function getMaxRedeemedRewardPoints()
    {
        return $this->isChild()
            ? $this->getParent()->getMaxRedeemedRewardPoints()
            : parent::getMaxRedeemedRewardPoints();
    }

    /**
     * Set the maximum number of reward points the shopper wants to redeem with the order.
     *
     * @param integer $points Number of points. A negative value means "no limit".
     */
    public function setMaxRedeemedRewardPoints($points, $force = false)
    {
        if ($this->isChild()) {
            $this->getParent()->setMaxRedeemedRewardPoints($points);
        } else {
            parent::setMaxRedeemedRewardPoints($points);
        }
    }

    /**
     * Check if the order model is the entity of a multivendor order that we
     * should calculate and redeem reward points for.
     *
     * @return bool
     */
    public function isRewardPointsEnabledOrderPart()
    {
        $warehouseMode = \XC\MultiVendor\Main::isWarehouseMode();

        return !($this instanceof \XLite\Model\Cart)
            || (($this->isChild() && !$warehouseMode) || ($this->isParent() && $warehouseMode));
    }

    /**
     * Number of rewards points used by modifiers for the order already.
     * Used in modifier calculations only.
     *
     * @return int
     */
    public function getNumberOfUsedRewardPoints()
    {
        return $this->isChild()
            ? $this->getParent()->getNumberOfUsedRewardPoints()
            : parent::getNumberOfUsedRewardPoints();
    }

    /**
     * Calculates the number of reward points that are required give the
     * maximum discount for the order.
     *
     * @param mixed $mode Apply mode (uses the module settings if not specified) OPTIONAL
     *
     * @return int
     */
    public function calculateRequiredRewardPoints($mode = null)
    {
        return ($this instanceof \XLite\Model\Cart) && !\XC\MultiVendor\Main::isWarehouseMode() && $this->isParent()
            ? $this->calculateRequiredChildRewardPoints($mode)
            : parent::calculateRequiredRewardPoints($mode);
    }

    /**
     * Increases the number of points used for the order by modifiers.
     * Used in modifier calculations only.
     *
     * @param int $points Number of used points
     */
    public function addUsedRewardPoints($points)
    {
        if ($this->isChild()) {
            $this->getParent()->addUsedRewardPoints($points);
        } else {
            parent::addUsedRewardPoints($points);
        }
    }

    /**
     * Sums reward points given for child orders.
     *
     * @return int
     */
    protected function calculateChildRewardPoints()
    {
        $points = 0;

        foreach ($this->getChildren() as $child) {
            $points += $child->getCalculatedRewardPoints();
        }

        return $points;
    }

    /**
     * Sums reward points given for child orders.
     *
     * @return int
     */
    protected function calculateChildRedeemedPoints()
    {
        $points = 0;

        foreach ($this->getChildren() as $child) {
            $points += $child->getRedeemedRewardPoints();
        }

        return $points;
    }

    /**
     * Check if the user can earn reward points for the order.
     *
     * @return boolean
     */
    protected function canCalculateRewardPoints()
    {
        // We calculate reward points for the parent order only
        // and never for vendor's orders (in both the modes)
        return $this->isRewardPointsEnabledOrderPart()
            && parent::canCalculateRewardPoints();
    }

    /**
     * processSucceed handler logic added by Loyalty Program module.
     */
    protected function processSucceedLoyaltyProgram()
    {
        // We always apply redeemed points on the parent order
        // and never on vendor's orders (in both the modes)
        if ($this->isRewardPointsEnabledOrderPart()) {
            parent::processSucceedLoyaltyProgram();
        }
    }

    /**
     * Calculates the number of reward points that are required give the
     * maximum discount for vendor orders.
     *
     * @param mixed $mode Apply mode (uses the module settings if not specified) OPTIONAL
     *
     * @return int
     */
    protected function calculateRequiredChildRewardPoints($mode = null)
    {
        $points = 0;

        foreach ($this->getChildren() as $child) {
            $points += $child->calculateRequiredRewardPoints($mode);
        }

        return $points;
    }
}
